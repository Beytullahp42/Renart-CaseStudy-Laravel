<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [App\Http\Controllers\ProductController::class, 'fetchProducts'])
    ->name('products.fetch');

Route::get('/gold-price', [App\Http\Controllers\ProductController::class, 'getGoldPrice']);

Route::post('/products', [App\Http\Controllers\ProductController::class, 'filterProducts'])
    ->name('products');
