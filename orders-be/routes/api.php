<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login']);

Route::controller(OrderController::class)->group( function () {
    Route::get('/orders', 'index');
    Route::get('/orders/{order}', 'show');
    Route::post('/orders', 'store');
    Route::post('/orders/{order}/products', 'addProduct');
    Route::put('/orders/{order}', 'update');
    Route::delete('/orders/{order}', 'delete');
    Route::delete('/orders/{order}/products/{product}', 'deleteProduct');
});

Route::controller(ProductController::class)->group( function () {
    Route::get('/products', 'index');
    Route::get('/products/{product}', 'show');
    Route::post('/products', 'store');
    Route::put('/products/{product}', 'update');
    Route::delete('/products/{product}', 'delete');
});