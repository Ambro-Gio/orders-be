<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Traits\ApiResponses;
use App\Http\Resources\OrderCollection;

class OrderController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        request()->validate([
            'date_start' => [
                'bail',
                'date_format:Y-m-d\TH:i:s.u\Z',
            ],
            'date_end' => [
                'bail',
                'date_format:Y-m-d\TH:i:s.u\Z',
                'after:date_start'
            ],
            'name' => ['bail', 'string'],
            'description' => ['bail', 'string'],
        ]);

        $dateStart = request()->query('date_start');
        $dateEnd = request()->query('date_end');
        $name = request()->query('name');
        $description = request()->query('description');

        $orders = Order::when($dateStart, function ($query, $dateStart) {
            $query->where('date', '>=', $dateStart);
        })->when(
            $dateEnd,
            function ($query, $dateEnd) {
                $query->where('date', '<=', $dateEnd);
            }
        )->when($name, function ($query, $name) {
            $query->where('name', 'like', "%{$name}%");
        })->when($description, function ($query, $description) {
            $query->where('description', 'like', "%{$description}%");
        })->paginate();

        return new OrderCollection($orders);
    }

    /**
     * Returns an Order with its associated products.
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        $order = Order::with('products')->find($id);

        if (!$order) {
            return $this->error("Order not found");
        }

        return $this->ok([
            "ID" => $order->id,
            "name" => $order->name,
            "description" => $order->description,
            "products" => $order->products
        ]);
    }

    /**
     * Deletes an order.
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        $order = Order::find($id);

        if (!$order) {
            return $this->error("Order not found");
        }

        $order->delete();

        return $this->ok("Order deleted");
    }
}
