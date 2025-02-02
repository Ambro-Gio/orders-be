<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\AddProductRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponses;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponses;

    protected OrderService $orderService;

    //ProductService injection
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param App\Http\Requests\IndexOrderRequest;
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexOrderRequest $request)
    {

        $dateStart = $request->query('date_start');
        $dateEnd = $request->query('date_end');
        $name = $request->query('name');
        $description = $request->query('description');

        $orders = Order::where('user_id', auth()->id())
            ->when($dateStart, function ($query, $dateStart) {
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
            })->orderBy('date', 'DESC')->paginate();

        return new OrderCollection($orders);
    }

    /**
     * Returns an Order with its associated products.
     * 
     * @param \App\Models\Order $order
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id())
            return $this->error("unauthorized", 401);

        return new OrderResource($order->load('products'));
    }

    /**
     * Creates a new order.
     * Accepts an optional array of products to be attached.
     * 
     * @param App\Http\Requests\StoreOrderRequest;
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $order = Order::create([
                    "name" => $request->name,
                    "description" => $request->description,
                    "user_id" => auth()->id(),
                ]);

                if (!$request->has('products')) {
                    return new OrderResource($order);
                }

                // Attach products if provided
                $order = $this->orderService->addProductsToOrder($order, $request->products);

                return new OrderResource($order->load('products'));
            });
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Updates an existing order.
     * 
     * @param App\Http\Requests\StoreOrderRequest;
     * @param \App\Models\Order $order
     * 
     * @return \Illuminate\Http\JsonRespons
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        if ($order->user_id !== auth()->id())
            return $this->error("unauthorized", 401);

        $order->update($request->only(['name', 'description']));
        return $this->ok("OK");
    }

    /**
     * Adds a new product in an order.
     * 
     * @param App\Http\Requests\AddProductRequest;
     * @param \App\Models\Order $order
     * 
     * @return \Illuminate\Http\JsonRespons
     */
    public function addProduct(AddProductRequest $request, Order $order)
    {

        if ($order->user_id !== auth()->id())
            return $this->error("unauthorized", 401);

        try {
            $order = $this->orderService->addProductsToOrder($order, $request->products);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }

        return new OrderResource($order->load('products'));
    }

    /**
     * Deletes an order and its associated products.
     * @param \App\Models\Order $order
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Order $order)
    {

        if ($order->user_id !== auth()->id())
            return $this->error("unauthorized", 401);

        try {
            return DB::transaction(function () use ($order) {

                foreach ($order->products as $product) {
                    $orderQuantity = $product->pivot->quantity;
                    $product->stock->increment('stock_quantity', $orderQuantity);
                }

                $order->delete();

                return $this->ok("OK");
            });
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }


    /**
     * Deletes an product from its order.
     *
     * @param \App\Models\Order $order
     * @param \App\Models\Product $product
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct(Order $order, Product $product)
    {
        if ($order->user_id !== auth()->id())
            return $this->error("unauthorized", 401);
        
        try {
            DB::transaction(function () use ($order, $product) {
                // Check if the product exists in the order
                $pivotRow = $order->products()
                    ->where('product_id', $product->id)
                    ->first();

                if (!$pivotRow) {
                    throw new \Exception("Product not found in the order.");
                }

                $orderQuantity = $pivotRow->pivot->quantity;
                $order->products()->detach($product->id);
                $product->stock->increment('stock_quantity', $orderQuantity);
            });

            return $this->ok("OK");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
