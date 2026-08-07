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
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerRoleController;




/*  |--------------------|
    |     Home Route     |
    |--------------------|*/
/* مسیر اصلی*/
Route::get('/', function () { return redirect()->route('dashboard');});


/*  |---------------------------------|
    |     NOT Authenticated Route     |
    |---------------------------------|*/
/* مسیرهایی که نیاز به احراز هویت ندارند*/
Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [LoginController::class, 'login']);

});


/* Loguot Route */
/* مسیر خروج کاربر از برنامه*/
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');


/*  |--------------------------------|
    |      Authenticated Route       |
    |--------------------------------|*/
/* مسیر هایی که نیاز به احراز هویت داردند*/
Route::middleware('auth')->group(function () {

    /* Dashboard */
    /* مسیر صفحه داشبورد مدیریتی*/
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    
    /* Product */
    /* مسیر صفحه لیست محصولات*/
    Route::resource('products', ProductController::class)->except(['show']);
    
    Route::get('products/{product}/stock', [ProductController::class,'stock'])->name('products.stock');    
    Route::post('products/{product}/stock', [ProductController::class,'storeStock'])->name('products.stock.store');
    
    Route::get('products/{product}/stock/sale', [ProductController::class,'createSale'])->name('products.sale.create');
    Route::post('products/{product}/stock/sale', [ProductController::class,'storeSale'])->name('products.sale.store');

    Route::get('products/{product}/stock/create', [ProductController::class,'createStock'])->name('products.stock.create');

    
    /* POS */
    /* مسیر صفحه صندوق فروش*/
    Route::get('/pos', [SaleController::class,'index'])->name('pos.index');
    
    Route::get('/pos/product', [SaleController::class, 'findProduct'])->name('pos.product');
       
    Route::post('/pos/checkout', [SaleController::class, 'checkout'])->name('pos.checkout');

    
    /* invoice */
    /* مسیر نمایش فاکتور فروش بعد از خرید مشتری*/
    Route::get('/invoice/{sale}', [SaleController::class, 'invoice'])->name('invoice');

    
    /* Setting */
    /* مسیر صفحه تنظیمات*/
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    /* مسیر ذخیره تنظیمات جدید*/
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');


    /* Category */
    /* مسیر صفحه لیست دسته بندی های محصولات*/
    Route::resource('categories', CategoryController::class); 


    /* User */
    /* مسیر نمایش لیست کاربران*/
    Route::resource('users', UserController::class)->except('show');
    /* مسیر تغییر رمزعبور کاربر*/
    Route::put('/users/{user}/password', [UserController::class,'updatePassword'])->name('users.password');

    
    /* User Role */
    /* مسیر نمایش لیست نقش های کاربر*/
    Route::resource('roles', RoleController::class)->except('show');

    
    /* User Role Permission */
    /* مسیر ویرایش مجوزهای مربوط به هر نقش کاربر*/
    Route::get('/roles/{role}/permissions', [RoleController::class,'permissions'])->name('roles.permissions');
    Route::post('/roles/{role}/permissions', [RoleController::class,'syncPermissions'])->name('roles.permissions.sync');

    
    /* Customer */
    /* مسیر جستجوی مشتریان */
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    /* مسیر نمایش لیست مشتریان*/
    Route::resource('customers', CustomerController::class);

    
    /* Customer Role */
    /* مسیر نمایش لیست نقش های مشتری*/ 
    Route::resource('customer-roles', CustomerRoleController::class);
    
});