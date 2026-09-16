<?php

namespace App\Livewire\Warehouses;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Product;
use App\Models\ProductWarehouseStock;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockCountManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public bool $showCreateModal = false;
    public string $warehouse_id = '';
    public ?string $notes = null;
    public bool $includeEmpty = true;

    public bool $showCountModal = false;
    public ?int $countId = null;

    public string $scanBarcode = '';
    public string $search = '';

    public array $counted = [];

    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function openCreateModal(): void
    {
        $this->authorizeAction('counts.create');

        $this->reset(['warehouse_id', 'notes']);
        $this->includeEmpty = true;
        $this->warehouse_id = (string) (Warehouse::getDefaultId() ?? '');
        $this->showCreateModal = true;
    }

    public function create(): void
    {
        $this->authorizeAction('counts.create');

        $this->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $warehouseId = (int) $this->warehouse_id;

        $count = DB::transaction(function () use ($warehouseId) {
            $count = StockCount::create([
                'reference' => StockCount::generateReference(),
                'warehouse_id' => $warehouseId,
                'status' => 'draft',
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]);

            ProductWarehouseStock::with('product:id,name,barcode,unit')
                ->where('warehouse_id', $warehouseId)
                ->when(! $this->includeEmpty, fn ($q) => $q->where('quantity', '!=', 0))
                ->each(function ($stock) use ($count) {
                    StockCountItem::create([
                        'stock_count_id' => $count->id,
                        'product_id' => $stock->product_id,
                        'system_quantity' => (float) $stock->quantity,
                        'counted_quantity' => (float) $stock->quantity,
                    ]);
                });

            return $count;
        });

        session()->flash('success', 'برگه انبارگردانی با شماره '.$count->reference.' ساخته شد.');

        $this->showCreateModal = false;
        $this->startCount($count->id);
    }

    public function startCount(int $id): void
    {
        $this->countId = $id;
        $this->reset(['scanBarcode', 'search', 'counted']);
        $this->showCountModal = true;

        // بارگذاری مقادیر شمارش‌شده‌ی قبلی در حافظه‌ی کامپوننت
        $items = StockCountItem::where('stock_count_id', $id)->get();

        foreach ($items as $item) {
            $this->counted[$item->product_id] = (float) $item->counted_quantity;
        }
    }

    public function scan(): void
    {
        $barcode = trim($this->scanBarcode);

        if ($barcode === '' || ! $this->countId) {
            return;
        }

        $item = StockCountItem::with('product:id,name,unit')
            ->where('stock_count_id', $this->countId)
            ->whereHas('product', fn ($q) => $q->where('barcode', $barcode))
            ->first();

        if (! $item) {
            session()->flash('count_error', 'این کالا در برگه انبارگردانی نیست.');
            $this->scanBarcode = '';

            return;
        }

        $current = (float) ($this->counted[$item->product_id] ?? $item->system_quantity);
        $this->counted[$item->product_id] = $current + 1;
        $this->scanBarcode = '';
    }

    public function saveCounts(): void
    {
        $this->authorizeAction('counts.edit');

        if (! $this->countId) {
            return;
        }

        DB::transaction(function () {
            foreach ($this->counted as $productId => $qty) {
                StockCountItem::where('stock_count_id', $this->countId)
                    ->where('product_id', $productId)
                    ->update(['counted_quantity' => max(0, (float) $qty)]);
            }
        });

        session()->flash('success', 'مقادیر شمارش ذخیره شد.');
    }

    public function finalize(WarehouseService $warehouseService): void
    {
        $this->authorizeAction('counts.finalize');

        $count = StockCount::with('items')->findOrFail($this->countId);

        if ($count->status !== 'draft') {
            session()->flash('error', 'این برگه قبلاً نهایی شده است.');

            return;
        }

        DB::transaction(function () use ($count, $warehouseService) {
            foreach ($count->items as $item) {
                $product = Product::find($item->product_id);

                if (! $product) {
                    continue;
                }

                $warehouseService->setWarehouseQuantity(
                    $product,
                    $count->warehouse_id,
                    (float) $item->counted_quantity,
                    'انبارگردانی '.$count->reference,
                );
            }

            $count->update([
                'status' => 'finalized',
                'finalized_by' => auth()->id(),
                'finalized_at' => now(),
            ]);
        });

        session()->flash('success', 'انبارگردانی نهایی و موجودی انبار اصلاح شد.');
        $this->showCountModal = false;
    }

    public function closeCount(): void
    {
        $this->showCountModal = false;
        $this->countId = null;
        $this->resetPage();
    }

    public function cancel(int $id): void
    {
        $this->authorizeAction('counts.edit');

        StockCount::findOrFail($id)->update(['status' => 'cancelled']);
        session()->flash('success', 'برگه انبارگردانی لغو شد.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorizeAction('counts.edit');

        StockCount::findOrFail($this->deletingId)->delete();
        session()->flash('success', 'برگه انبارگردانی حذف شد.');
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $counts = StockCount::with(['warehouse:id,name', 'creator:id,name'])
            ->latest()
            ->paginate(12);

        $active = null;
        $items = collect();

        if ($this->showCountModal && $this->countId) {
            $active = StockCount::with('warehouse:id,name')->find($this->countId);

            if ($active) {
                $items = StockCountItem::with('product:id,name,barcode,unit')
                    ->where('stock_count_id', $active->id)
                    ->when(trim($this->search) !== '', function ($q) {
                        $q->whereHas('product', fn ($w) => $w
                            ->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('barcode', 'like', '%'.$this->search.'%'));
                    })
                    ->orderBy('product_id')
                    ->paginate(50);
            }
        }

        return view('livewire.warehouses.stock-count-manager', [
            'counts' => $counts,
            'active' => $active,
            'items' => $items,
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
