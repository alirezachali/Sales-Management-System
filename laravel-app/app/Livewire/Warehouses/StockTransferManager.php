<?php

namespace App\Livewire\Warehouses;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockTransferManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $filter = 'all';

    // فرم ایجاد انتقال
    public bool $showCreateModal = false;
    public string $from_warehouse_id = '';
    public string $to_warehouse_id = '';
    public ?string $notes = null;
    public string $productSearch = '';
    public array $searchResults = [];
    public array $items = [];
    public int $itemCounter = 0;

    public bool $showDetailModal = false;
    public ?int $detailId = null;

    protected function rules(): array
    {
        return [
            'from_warehouse_id' => ['required', 'exists:warehouses,id'],
            'to_warehouse_id' => ['required', 'different:from_warehouse_id', 'exists:warehouses,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'from_warehouse_id.required' => 'انبار مبدأ را انتخاب کنید.',
            'to_warehouse_id.required' => 'انبار مقصد را انتخاب کنید.',
            'to_warehouse_id.different' => 'انبار مبدأ و مقصد نمی‌توانند یکی باشند.',
        ];
    }

    public function updatedProductSearch(): void
    {
        $term = trim($this->productSearch);

        if (mb_strlen($term) < 2) {
            $this->searchResults = [];

            return;
        }

        $this->searchResults = Product::where('is_active', true)
            ->where(fn ($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%"))
            ->limit(8)
            ->get(['id', 'name', 'barcode', 'unit']);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function addItem(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $fromId = (int) $this->from_warehouse_id;

        $available = 0;
        if ($fromId) {
            $available = (float) DB::table('product_warehouse_stocks')
                ->where('product_id', $product->id)
                ->where('warehouse_id', $fromId)
                ->value('quantity');
        }

        if (isset($this->items[$productId])) {
            $this->items[$productId]['quantity'] += 1;

            return;
        }

        $this->items[$productId] = [
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'unit' => $product->unit,
            'available' => $available,
            'quantity' => 1,
        ];

        $this->productSearch = '';
        $this->searchResults = [];
    }

    public function removeItem(int $productId): void
    {
        unset($this->items[$productId]);
    }

    public function updatedItemsQuantity($value, $key): void
    {
        // key مثل items.3.quantity
        $parts = explode('.', (string) $key);
        $id = $parts[1] ?? null;

        if ($id !== null && isset($this->items[$id])) {
            $this->items[$id]['quantity'] = max(0, (float) $value);
        }
    }

    public function create(): void
    {
        $this->authorizeAction('transfers.create');

        $this->validate();

        if (empty($this->items)) {
            session()->flash('error', 'حداقل یک کالا به انتقال اضافه کنید.');

            return;
        }

        DB::transaction(function () {
            $transfer = StockTransfer::create([
                'reference' => StockTransfer::generateReference(),
                'from_warehouse_id' => (int) $this->from_warehouse_id,
                'to_warehouse_id' => (int) $this->to_warehouse_id,
                'status' => 'pending',
                'notes' => $this->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($this->items as $item) {
                $qty = (float) ($item['quantity'] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['id'],
                    'quantity' => $qty,
                ]);
            }
        });

        session()->flash('success', 'انتقال ثبت شد و در انتظار تأیید دریافت است.');

        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function confirmReceive(int $transferId, WarehouseService $warehouseService): void
    {
        $this->authorizeAction('transfers.receive');

        $transfer = StockTransfer::with('items')->findOrFail($transferId);

        if ($transfer->status !== 'pending') {
            session()->flash('error', 'این انتقال قبلاً بررسی شده است.');

            return;
        }

        DB::transaction(function () use ($transfer, $warehouseService) {
            foreach ($transfer->items as $item) {
                $product = Product::find($item->product_id);

                if (! $product) {
                    continue;
                }

                $warehouseService->removeFromWarehouse(
                    $product,
                    $transfer->from_warehouse_id,
                    (float) $item->quantity,
                    'transfer',
                    'انتقال کالا به انبار '.$transfer->toWarehouse->name,
                );

                $warehouseService->addToWarehouse(
                    $product,
                    $transfer->to_warehouse_id,
                    (float) $item->quantity,
                    'transfer',
                    'دریافت کالا از انبار '.$transfer->fromWarehouse->name,
                );
            }

            $transfer->update([
                'status' => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);
        });

        session()->flash('success', 'انتقال با موفقیت تأیید و موجودی انبار مقصد به‌روزرسانی شد.');
        $this->showDetailModal = false;
    }

    public function cancel(int $transferId): void
    {
        $this->authorizeAction('transfers.create');

        $transfer = StockTransfer::findOrFail($transferId);

        if ($transfer->status !== 'pending') {
            session()->flash('error', 'فقط انتقال در انتظار قابل لغو است.');

            return;
        }

        $transfer->update(['status' => 'cancelled']);
        session()->flash('success', 'انتقال لغو شد.');
    }

    public function showDetail(int $id): void
    {
        $this->detailId = $id;
        $this->showDetailModal = true;
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showDetailModal = false;
    }

    private function resetForm(): void
    {
        $this->reset(['from_warehouse_id', 'to_warehouse_id', 'notes', 'productSearch', 'searchResults', 'items']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $transfers = StockTransfer::with(['fromWarehouse:id,name', 'toWarehouse:id,name', 'creator:id,name'])
            ->withCount('items')
            ->when($this->filter === 'pending', fn ($q) => $q->where('status', 'pending'))
            ->when($this->filter === 'received', fn ($q) => $q->where('status', 'received'))
            ->latest()
            ->paginate(12);

        $detail = $this->detailId
            ? StockTransfer::with([
                'items.product:id,name,barcode,unit',
                'fromWarehouse:id,name',
                'toWarehouse:id,name',
                'creator:id,name',
            ])->find($this->detailId)
            : null;

        return view('livewire.warehouses.stock-transfer-manager', [
            'transfers' => $transfers,
            'warehouses' => Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'detail' => $detail,
        ]);
    }
}
