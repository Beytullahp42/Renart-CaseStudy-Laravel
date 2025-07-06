<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/products', [App\Http\Controllers\ProductController::class, 'fetchProducts'])
    ->name('products.fetch');

Route::get('/gold-price', [App\Http\Controllers\ProductController::class, 'getGoldPrice']);
