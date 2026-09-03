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
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/* مسیر اصلی */
Route::get('/', function () {
    return redirect()->route('dashboard');
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
    /* مسیر صفحه داشبورد مدیریتی */
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group([], function () {
        /* مسیر صفحه لیست محصولات - یک ویوی Blade معمولی که کامپوننت Livewire را
           با تگ <livewire:products.product-manager /> در خودش جای می‌دهد (دقیقاً
           همان الگوی ماژول تامین‌کنندگان در suppliers.index). جستجو، فیلتر، افزودن،
           ویرایش و حذف همه بدون رفرش صفحه انجام می‌شوند. */
        Route::get('products', function () {
            return view('products.index');
        })->name('products.index');

        Route::get('purchase-invoices', function () {
            return view('purchase-invoices.index');
        })->name('purchase-invoices.index');
        /* بقیه‌ی مسیرهای resource همچنان از طریق کنترلر (برای سازگاری با لینک‌های قدیمی) */
        Route::resource('products', ProductController::class)->except(['show', 'index']);
        Route::get('products/{product}/stock', function (\App\Models\Product $product) {
            return view('products.stock', compact('product'));
        })->name('products.stock');
        /* مسیرهای زیر دیگر از رابط جدید استفاده نمی‌شوند (ورود/خروج کالا الان از طریق
           همان صفحه‌ی products.stock و به‌صورت مودال زنده انجام می‌شود) ولی برای
           سازگاری با لینک‌های قدیمی حذف نشده‌اند. */
        Route::post('products/{product}/stock', [ProductController::class, 'storeStock'])->name('products.stock.store');
        Route::get('products/{product}/stock/sale', [ProductController::class, 'createSale'])->name('products.sale.create');
        Route::post('products/{product}/stock/sale', [ProductController::class, 'storeSale'])->name('products.sale.store');
        Route::get('products/{product}/stock/create', [ProductController::class, 'createStock'])->name('products.stock.create');
        /* مسیر جنراتور بارکد برای محصولات جدید بدون بارکد خاصی از */
        Route::get('products/generate-barcode', [BarcodeController::class, 'generate'])->name('products.generate.barcode');
        /* مسیر چاپ لیبل محصولات */
        Route::get('products/{product}/label', [LabelController::class, 'show'])->name('products.label');
    });

    Route::group([], function () {
        /* مسیر صفحه صندوق فروش */
        Route::get('pos', [SaleController::class, 'index'])->name('pos.index');
        Route::get('pos/product', [SaleController::class, 'findProduct'])->name('pos.product');
        Route::post('pos/checkout', [SaleController::class, 'checkout'])->name('pos.checkout');
    });

    /* مسیر نمایش فاکتور فروش بعد از خرید مشتری */
    Route::get('invoice/{sale}', [SaleController::class, 'invoice'])->name('invoice');

    Route::group([], function () {
        /* مسیر صفحه تنظیمات */
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        /* مسیر ذخیره تنظیمات جدید */
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    /* مسیر تغییر زبان برنامه (کلیک روی پرچم در منوی ناوبری) */
    Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

    /* مسیر صفحه لیست دسته بندی های محصولات */
    Route::resource('categories', CategoryController::class);

    Route::group([], function () {
        /* مسیر لیست کاربران */
        Route::resource('users', UserController::class)->except('show');
        /* مسیر تغییر رمزعبور کاربر */
        Route::put('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
        /* مسیر لیست نقش ها */

        Route::resource('user/roles', RoleController::class)->except('show');
        /* مسیر ویرایش مجوزهای مربوط به هر نقش کاربر */
        Route::get('user/roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::post('user/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync');
    });

    Route::group([], function () {
        /* مسیر صفحه‌ی باشگاه مشتریان - کامپوننت Livewire (جستجو، فیلتر رده،
           افزودن، ویرایش، حذف و گردش حساب، همه بدون رفرش صفحه) */
        Route::get('customers', function () {
            return view('customers.index');
        })->name('customers.index');
        /* بقیه‌ی مسیرهای resource همچنان از طریق کنترلر (برای سازگاری با لینک‌های قدیمی) */
        Route::resource('customers', CustomerController::class)->except(['show', 'index']);
        /* مسیر جستجوی مشتریان (استفاده‌شده در ماژول فروش/pos) */
        Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');

        /* مسیر مدیریت رده‌های باشگاه مشتریان - کامپوننت Livewire
           (افزودن/ویرایش/حذف همگی داخل کامپوننت CustomerRoleManager انجام می‌شود) */
        Route::get('customer/roles', function () {
            return view('customers.roles.index');
        })->name('customer-roles.index');
    });

    // مسیر نمایش لیست تامین‌کنندگان
    // این خط رو داخل routes/web.php اضافه کن (به‌جای Route::resource قبلی برای suppliers)

Route::get('/suppliers', function () {
    return view('suppliers.index');
})->name('suppliers.index')->middleware('auth');

    // مسیر نمایش لیست کارکنان
    Route::get('employees', function () {
        return view('employees.index');
    })->name('employees.index');

    // مسیر نمایش لیست هزینه‌ها
    Route::get('expenses', function () {
        return view('expenses.index');
    })->name('expenses.index');

    // مسیر لیست کارها
    Route::get('todos', function () {
        return view('todos.index');
    })->name('todos.index');

    // مسیر گزارش فروش
     Route::get('reports.sales', function () {
        return view('reports.sales');
    })->name('reports.sales');

    // مسیر مدیریت مالی
    Route::get('financial', function () {
        return view('financial.index');
    })->name('financial.index');

    // مسیر نمایش لیست برندها
    Route::resource('brands', BrandController::class);
});