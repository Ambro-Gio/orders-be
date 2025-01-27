<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login']);

Route::controller(OrderController::class)->group( function () {
    Route::get('/orders', 'index');
    Route::get('/orders/{id}', 'show');
    Route::post('/orders', 'store');
    Route::delete('/orders/{id}', 'destroy');
});