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
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BrandController;


/* مسیر اصلی*/
Route::get('/', function () { return redirect()->route('dashboard');});

/*  |---------------------------------|
    |     NOT Authenticated Route     |
    |---------------------------------|*/
/* مسیرهایی که نیاز به احراز هویت ندارند*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

/* مسیر خروج کاربر از برنامه*/
Route::post('logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');

/*  |--------------------------------|
    |      Authenticated Route       |
    |--------------------------------|*/
/* مسیر هایی که نیاز به احراز هویت داردند*/
Route::middleware('auth')->group(function () {

    /* مسیر صفحه داشبورد مدیریتی*/
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group([], function () {
        /* مسیر صفحه لیست محصولات*/
        Route::resource('products', ProductController::class)->except(['show']);
        Route::get('products/{product}/stock', [ProductController::class,'stock'])->name('products.stock');    
        Route::post('products/{product}/stock', [ProductController::class,'storeStock'])->name('products.stock.store');
        Route::get('products/{product}/stock/sale', [ProductController::class,'createSale'])->name('products.sale.create');
        Route::post('products/{product}/stock/sale', [ProductController::class,'storeSale'])->name('products.sale.store');
        Route::get('products/{product}/stock/create', [ProductController::class,'createStock'])->name('products.stock.create');
        /* مسیر جنراتور بارکد برای محصولات جدید بدون بارکد کارخانه ای */
        Route::get('products/generate-barcode', [BarcodeController::class, 'generate'])->name('products.generate.barcode');
        /* مسیر چاپ لیبل محصولات */
        Route::get('products/{product}/label', [LabelController::class, 'show'])->name('products.label');
    });

    Route::group([], function () {
        /* مسیر صفحه صندوق فروش*/
        Route::get('pos', [SaleController::class,'index'])->name('pos.index');
        Route::get('pos/product', [SaleController::class, 'findProduct'])->name('pos.product');  
        Route::post('pos/checkout', [SaleController::class, 'checkout'])->name('pos.checkout');
    });
    
    /* مسیر نمایش فاکتور فروش بعد از خرید مشتری*/
    Route::get('invoice/{sale}', [SaleController::class, 'invoice'])->name('invoice');

    Route::group([], function () {
        /* مسیر صفحه تنظیمات*/
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        /* مسیر ذخیره تنظیمات جدید*/
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    /* مسیر صفحه لیست دسته بندی های محصولات*/
    Route::resource('categories', CategoryController::class); 

    Route::group([], function () {
        /* مسیر نمایش لیست کاربران*/
        Route::resource('users', UserController::class)->except('show');
        /* مسیر تغییر رمزعبور کاربر*/
        Route::put('users/{user}/password', [UserController::class,'updatePassword'])->name('users.password');
        /* مسیر نمایش لیست نقش های کاربر*/
        
        Route::resource('user/roles', RoleController::class)->except('show');
        /* مسیر ویرایش مجوزهای مربوط به هر نقش کاربر*/
        Route::get('user/roles/{role}/permissions', [RoleController::class,'permissions'])->name('roles.permissions');
        Route::post('user/roles/{role}/permissions', [RoleController::class,'syncPermissions'])->name('roles.permissions.sync');
    });

    
    Route::group([], function () {
        /* مسیر نمایش لیست مشتریان*/
        Route::resource('customers', CustomerController::class);
        /* مسیر جستجوی مشتریان */
        Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');

        /* مسیر نمایش لیست نقش های مشتری*/ 
        Route::resource('customer/roles', CustomerRoleController::class);
    });

    // مسیر نمایش لیست تامین کنندگان
    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
    
    // مسیر نمایش لیست برندها
    Route::resource('brands', BrandController::class);

    
    
});