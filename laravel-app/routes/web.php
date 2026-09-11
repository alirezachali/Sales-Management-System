<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/* مسیر اصلی */
Route::get('/', function () {
    return redirect()->route(auth()->check() ? auth()->user()->dashboardRouteName() : 'login');
});

/*  |--------------------------------------------------|
    |     NOT Authenticated Route     |
    |--------------------------------------------------|*/
/* مسیرهایی که نیاز به احراز هویت ندارند */
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});


/* مسیر خروج کاربر از برنامه */
Route::post('logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');


/*  |--------------------------------------------------|
    |      Authenticated Route       |
    |--------------------------------------------------|*/
/* مسیر هایی که نیاز به احراز هویت دارند */
Route::middleware('auth')->group(function () {

    /* مسیر صفحه داشبورد مدیریتی (نقش‌های مدیر و مدیر کل) */
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('can:dashboard.view');

    /* داشبوردهای اختصاصی نقش‌های صندوقدار، حسابدار و انباردار
       (کنترلر در صورت ناهماهنگی نقش، کاربر را به داشبورد خودش هدایت می‌کند) */
    Route::get('dashboard/cashier', [DashboardController::class, 'cashier'])->name('dashboard.cashier');

    Route::get('dashboard/accountant', [DashboardController::class, 'accountant'])->name('dashboard.accountant');

    Route::get('dashboard/warehouse', [DashboardController::class, 'warehouse'])->name('dashboard.warehouse');


    Route::get('products', function () {return view('products.index');})->name('products.index')
        ->middleware('can:products.view');

    Route::get('purchase-invoices', function () {return view('purchase-invoices.index');})->name('purchase-invoices.index')
        ->middleware('can:purchases.view');

    /* بقیه‌ی مسیرهای resource همچنان از طریق کنترلر (برای سازگاری با لینک‌های قدیمی) */
    Route::resource('products', ProductController::class)->except(['show', 'index'])
        ->middleware('can:products.edit');

    Route::get('products/{product}/stock', function (\App\Models\Product $product) 
        {return view('products.stock', compact('product'));})
            ->name('products.stock')->middleware('can:stocks.view');
  
    Route::post('products/{product}/stock', [ProductController::class, 'storeStock'])->name('products.stock.store')
        ->middleware('can:stocks.adjust,product');

    Route::get('products/{product}/stock/sale', [ProductController::class, 'createSale'])->name('products.sale.create');
    
    Route::post('products/{product}/stock/sale', [ProductController::class, 'storeSale'])->name('products.sale.store');
    
    Route::get('products/{product}/stock/create', [ProductController::class, 'createStock'])->name('products.stock.create');
    
    /* مسیر جنراتور بارکد برای محصولات جدید بدون بارکد خاصی از */
    Route::get('products/generate-barcode', [BarcodeController::class, 'generate'])->name('products.generate.barcode');

    /* مسیر چاپ لیبل محصولات */
    Route::get('products/{product}/label', [LabelController::class, 'show'])->name('products.label');

 
    /* مسیر صفحه صندوق فروش */
    Route::get('pos', [SaleController::class, 'index'])->name('pos.index')
        ->middleware('can:pos.view');

    Route::get('pos/product', [SaleController::class, 'findProduct'])->name('pos.product')
        ->middleware('can:pos.view');

    Route::post('pos/checkout', [SaleController::class, 'checkout'])->name('pos.checkout')
        ->middleware('can:sales.create');


    /* مسیر نمایش فاکتور فروش بعد از خرید مشتری */
    Route::get('invoice/{sale}', [SaleController::class, 'invoice'])->name('invoice')
        ->middleware('can:sales.view');


    /* مسیر صفحه تنظیمات */
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')
        ->middleware('can:settings.view');

    /* مسیر ذخیره تنظیمات جدید */
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update')
        ->middleware('can:settings.edit');


    /* مسیر تغییر زبان برنامه (کلیک روی پرچم در منوی ناوبری) */
    Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

    /* مسیر صفحه لیست دسته بندی های محصولات */
    Route::resource('categories', CategoryController::class)
        ->middleware('can:categories.view');


    /* مسیر لیست کاربران */
    Route::resource('users', UserController::class)->except('show')
        ->middleware('can:users.view');

    /* مسیر تغییر رمزعبور کاربر */
    Route::put('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password')
        ->middleware('can:users.edit');

    /* مسیر لیست نقش ها */
    Route::resource('user/roles', RoleController::class)->except('show')
        ->middleware('can:roles.view');

    /* مسیر ویرایش مجوزهای مربوط به هر نقش کاربر */
    Route::get('user/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions')
        ->middleware('can:roles.permissions');

    Route::post('user/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync')
        ->middleware('can:roles.permissions');
    

    /* مسیر صفحه‌ی باشگاه مشتریان  */
    Route::get('customers', function () {return view('customers.index');})->name('customers.index')
        ->middleware('can:customers.view');

    /* بقیه‌ی مسیرهای resource همچنان از طریق کنترلر (برای سازگاری با لینک‌های قدیمی) */
    Route::resource('customers', CustomerController::class)->except(['show', 'index'])
        ->middleware('can:customers.view');

    /* مسیر جستجوی مشتریان (استفاده‌شده در ماژول فروش/pos) */
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search')
        ->middleware('can:customers.view');

    /* مسیر مدیریت رده‌های باشگاه مشتریان  */
    Route::get('customer/roles', function () {return view('customers.roles.index');})->name('customer-roles.index')
        ->middleware('can:customers.view');

    /* مسیر نمایش لیست تامین‌کنندگان */
    Route::get('/suppliers', function () {return view('suppliers.index');})->name('suppliers.index')
        ->middleware('can:suppliers.view');

    /* مسیر نمایش لیست کارکنان */
    Route::get('employees', function () {return view('employees.index');})->name('employees.index')
        ->middleware('can:employees.view');

    /* مسیر نمایش لیست هزینه‌ها */
    Route::get('expenses', function () {return view('expenses.index');})->name('expenses.index')
        ->middleware('can:expenses.view');

    /* مسیر لیست کارها */
    Route::get('todos', function () {return view('todos.index');})->name('todos.index')
        ->middleware('can:todos.view');

    /* مسیر گزارش فروش */
    Route::get('reports.sales', function () {return view('reports.sales');})->name('reports.sales')
        ->middleware('can:reports.sales');

    /* مسیر گزارش فاکتورهای خرید */
    Route::get('reports.purchases', function () {return view('reports.purchases');})->name('reports.purchases')
        ->middleware('can:reports.purchases');

    /* مسیر گزارش ورود و خروج کالا */
    Route::get('reports.stockmovements', function () {return view('reports.stockmovements');})->name('reports.stockmovements')
        ->middleware('can:reports.view');

    /* مسیر مدیریت مالی */
    Route::get('financial', function () {return view('financial.index');})->name('financial.index')
        ->middleware('can:financial.view');

    /* مسیر مدیریت بدهی ها */
    Route::get('debts', function () {return view('debts.index');})->name('debts.index')
        ->middleware('can:debts.view');

    /* مسیر نمایش لیست برندها */
    Route::resource('brands', BrandController::class)
        ->middleware('can:brands.view');
});