<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::bind('order', function ($value) {
            $order = Order::find($value);
            if (!$order) {
                return response()->json([
                    'Message' => "order not found"
                ], 404)->throwResponse();
            }
            return $order;
        });

        Route::bind('product', function ($value) {
            $product = Product::find($value);
            if (!$product) {
                return response()->json([
                    'Message' => "Product not found"
                ], 404)->throwResponse();
            }
            return $product;
        });
    }
}
