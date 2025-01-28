<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Traits\ApiResponses;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(IndexOrderRequest $request)
    {

        $dateStart = $request->query('date_start');
        $dateEnd = $request->query('date_end');
        $name = $request->query('name');
        $description = $request->query('description');

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
    public function show(Order $order)
    {
        return new OrderResource($order->load('products'));
    }

    /**
     * Creates a new order
     * 
     * @param App\Http\Requests\StoreOrderRequest;
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        return new OrderResource(Order::create($request->all()));
    }

    /**
     * Updates an existing order.
     * 
     * @param App\Http\Requests\StoreOrderRequest;
     * 
     * @return \Illuminate\Http\JsonRespons
     */
    public function update(StoreOrderRequest $request, Order $order) {
        $order->update($request->all());
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
