<?php

namespace App\Livewire\Dashboard;

use App\Livewire\Concerns\AuthorizesActions;
use App\Livewire\Concerns\HasTodoQuickAdd;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class WarehouseOverview extends Component
{
    use AuthorizesActions;
    use HasTodoQuickAdd;

    /*
    |--------------------------------------------------------------------|
    |                 بازه‌ی زمانی به‌روزرسانی خودکار (ثانیه)                |
    |--------------------------------------------------------------------|
    | با wire:poll در ویو استفاده می‌شود تا آمار داشبورد بدون رفرش صفحه   |
    | و بدون دخالت کاربر، هر چند ثانیه یک‌بار خودکار تازه شود.            |
    */
    public int $pollingSeconds = 300;

    /*
    |--------------------------------------------------------------------|
    |                              رندر                                  |
    |--------------------------------------------------------------------|
    | آمار داشبورد انباردار: تعداد کل کالاها (حتی بدون موجودی)، تعداد
    | دسته‌بندی‌ها، کالاهای در حال اتمام (بر اساس تنظیم stock_alert)،
    | کالاهای پرفروش و کم‌فروش (بر اساس مجموع تعداد فروش‌رفته).
    */
    public function render()
    {
        // حداقل موجودی برای قرارگیری کالا در لیست «در حال اتمام» (پیش‌فرض ۵)
        $stockAlert = (int) (setting('stock_alert') ?? 5);

        // این داشبورد با poll هر ۳۰ ثانیه رندر می‌شود؛ آمارها را چند دقیقه کش
        // می‌کنیم تا هر poll باعث اجرای کوئری‌های سنگین روی کل sale_items نشود.
        $productsCount = cache()->remember('warehouse-products-count', now()->addMinutes(5), fn () => Product::count());

        // تعداد دسته‌بندی‌های محصولات
        $categoriesCount = cache()->remember('warehouse-categories-count', now()->addMinutes(5), fn () => Category::count());

        // کالاهایی که در حال اتمام هستند (موجودی <= حد هشدار)
        $runningOutList = cache()->remember('warehouse-running-out-'.$stockAlert, now()->addMinutes(2), function () use ($stockAlert) {
            return Product::where('stock', '<=', $stockAlert)
                ->orderBy('stock')
                ->take(15)
                ->get();
        });

        $runningOutCount = cache()->remember('warehouse-running-out-count-'.$stockAlert, now()->addMinutes(2), fn () => Product::where('stock', '<=', $stockAlert)
            ->count());

        // رتبه‌بندی کالاها بر اساس مجموع تعداد فروش‌رفته (پرفروش و کم‌فروش).
        // چون دیتابیس در حالت ONLY_FULL_GROUP_BY است، همه‌ی ستون‌های
        // غیرتجمعی در group by ذکر می‌شوند.
        // این کوئری join سنگینی روی کل sale_items دارد؛ ۵ دقیقه کش می‌شود.
        $rankedQuery = fn () => Product::query()
            ->select('products.id', 'products.name', 'products.unit', 'products.stock')
            ->selectRaw('COALESCE(SUM(sale_items.quantity), 0) as sold_quantity')
            ->leftJoin('sale_items', 'sale_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name', 'products.unit', 'products.stock');

        $ranking = cache()->remember('warehouse-ranking', now()->addMinutes(5), function () use ($rankedQuery) {
            return [
                'best' => $rankedQuery()->orderByDesc('sold_quantity')->take(10)->get(),
                'poor' => $rankedQuery()->orderBy('sold_quantity')->take(10)->get(),
            ];
        });

        $bestSellers = $ranking['best'];
        $poorSellers = $ranking['poor'];

        // کارهای انجام‌نشده‌ی این کاربر برای نمایش در کارت لیست کارها
        $todos = $this->currentUserTodos();

        return view('livewire.dashboard.warehouse-overview', compact(
            'stockAlert',
            'productsCount',
            'categoriesCount',
            'runningOutList',
            'runningOutCount',
            'bestSellers',
            'poorSellers',
            'todos',
        ));
    }
}