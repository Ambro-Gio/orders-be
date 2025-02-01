<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(OrderController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/orders', 'index');
        Route::get('/orders/{order}', 'show');
        Route::post('/orders', 'store');
        Route::post('/orders/{order}/products', 'addProduct');
        Route::put('/orders/{order}', 'update');
        Route::delete('/orders/{order}', 'delete');
        Route::delete('/orders/{order}/products/{product}', 'deleteProduct');
    });

Route::controller(ProductController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/products', 'index');
        Route::get('/products/{product}', 'show');
        Route::post('/products', 'store');
        Route::put('/products/{product}', 'update');
    });


Route::post("/users", [UserController::class, "store"]);