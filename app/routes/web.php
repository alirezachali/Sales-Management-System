<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('products', ProductController::class)
    ->except(['show']);

Route::get(
    'products/{product}/stock',
    [ProductController::class,'stock']
)
->name('products.stock');    

Route::get(
    'products/{product}/stock/create',
    [ProductController::class,'createStock']
)
->name('products.stock.create');


Route::post(
    'products/{product}/stock',
    [ProductController::class,'storeStock']
)
->name('products.stock.store');

Route::get(
    'products/{product}/stock/sale',
    [ProductController::class,'createSale']
)
->name('products.sale.create');


Route::post(
    'products/{product}/stock/sale',
    [ProductController::class,'storeSale']
)
->name('products.sale.store');


Route::get('/pos', [SaleController::class,'index'])
    ->name('pos.index');


Route::get('/pos/product', [SaleController::class, 'findProduct'])
    ->name('pos.product');    


Route::post(
    '/pos/checkout',
    [SaleController::class, 'checkout']
)->name('pos.checkout');    