<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserController;




/*
    |--------------------|
    |     Home Route     |
    |--------------------|
    */
Route::get('/', function () { return redirect()->route('dashboard');});


/*
    |---------------------------------|
    |     NOT Authenticated Route     |
    |---------------------------------|
    */
Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [LoginController::class, 'login']);

});


/* Loguot Route */
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');


/*
    |--------------------------------|
    |      Authenticated Route       |
    |--------------------------------|
    */
Route::middleware('auth')->group(function () {

    /* Dashboard */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* Product */
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('products/{product}/stock', [ProductController::class,'stock'])->name('products.stock');    
    Route::get('products/{product}/stock/create', [ProductController::class,'createStock'])->name('products.stock.create');
    Route::post('products/{product}/stock', [ProductController::class,'storeStock'])->name('products.stock.store');
    Route::get('products/{product}/stock/sale', [ProductController::class,'createSale'])->name('products.sale.create');
    Route::post('products/{product}/stock/sale', [ProductController::class,'storeSale'])->name('products.sale.store');

    /* POS */
    Route::get('/pos', [SaleController::class,'index'])->name('pos.index');
    Route::get('/pos/product', [SaleController::class, 'findProduct'])->name('pos.product');    
    Route::post('/pos/checkout', [SaleController::class, 'checkout'])->name('pos.checkout');

    /* invoice */
    Route::get('/invoice/{sale}', [SaleController::class, 'invoice'])->name('invoice');

    /* Setting */
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    /* Category */
    Route::resource('categories', CategoryController::class); 
    
    /* User */
    Route::resource('users', UserController::class)->except('show');

});




