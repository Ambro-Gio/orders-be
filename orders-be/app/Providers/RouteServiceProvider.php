<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    use ApiResponses;
    
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
                return $this->error("order not found")
                    ->throwResponse();
            }
            return $order;
        });

        // Route::bind('product', function ($value) {
        //     $product = Product::find($value);
        //     if (!$product) {
        //         return $this->error("product not found")
        //             ->throwResponse();
        //     }
        //     return $product;
        // });
    }
}
