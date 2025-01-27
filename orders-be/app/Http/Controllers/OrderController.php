<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Traits\ApiResponses;

class OrderController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->ok(Order::all());
    }

    /**
     * Returns an Order with its associated products.
     * 
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $order = Order::find($id);

        if(!$order){
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
     * Deletes and order.
     * @param int $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){

        $order = Order::find($id);

        if(!$order){
            return $this->error("Order not found");
        }

        $order->delete();

        return $this->ok("Order deleted");
    }

}
