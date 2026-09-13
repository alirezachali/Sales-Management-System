<?php

namespace App\Livewire\Products;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    protected string $paginationTheme = 'bootstrap';

    public Product $product;

    /*
    |--------------------------------------------------------------------|
    |                     فرم ورود/خروج کالا از انبار                     |
    |--------------------------------------------------------------------|
    */
    // نوع عملیات جاری: purchase (ورود کالا) یا sale (خروج/فروش کالا)
    public string $formType = 'purchase';
    public bool $showFormModal = false;
    public $quantity = null;
    public ?string $description = null;
    public string $warehouse_id = '';

    public function mount(Product $product): void
    {
        $this->product = $product;

        $this->warehouse_id = (string) (Warehouse::getDefaultId() ?? '');

        // اگر از دکمه‌های «ورود کالا» / «خروج کالا» در صفحه‌ی لیست محصولات آمده باشیم،
        // مودال مربوطه بلافاصله باز می‌شود (مثلاً ?action=purchase یا ?action=sale)
        $action = request()->query('action');

        if (in_array($action, ['purchase', 'sale'], true)) {
            $this->formType = $action;
            $this->showFormModal = true;
        }
    }

    protected function rules(): array
    {
        return [
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'description' => ['nullable', 'string'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
        ];
    }

    protected function messages(): array
    {
        return [
            'quantity.required' => 'وارد کردن مقدار الزامی است.',
            'quantity.min' => 'مقدار باید بزرگ‌تر از صفر باشد.',
            'warehouse_id.required' => 'انتخاب انبار الزامی است.',
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public function openAddStockModal(): void
    {
        $this->resetForm();
        $this->formType = 'purchase';
        $this->showFormModal = true;
    }

    public function openRemoveStockModal(): void
    {
        $this->resetForm();
        $this->formType = 'sale';
        $this->showFormModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->quantity = null;
        $this->description = null;
        $this->warehouse_id = (string) (Warehouse::getDefaultId() ?? '');
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    |                          ثبت ورود/خروج کالا                        |
    |--------------------------------------------------------------------|
    */
    public function save(WarehouseService $warehouseService): void
    {
        $this->authorizeAction('stocks.adjust');

        $data = $this->validate();

        $warehouseId = (int) $data['warehouse_id'];

        if ($this->formType === 'sale') {
            $available = (float) DB::table('product_warehouse_stocks')
                ->where('product_id', $this->product->id)
                ->where('warehouse_id', $warehouseId)
                ->value('quantity');

            if ($available < $data['quantity']) {
                $this->addError('quantity', 'موجودی این کالا در انبار انتخابی کافی نیست (موجود: '.rtrim(rtrim(number_format($available, 3, '.', ''), '0'), '.').').');

                return;
            }
        }

        if ($this->formType === 'purchase') {
            $warehouseService->addToWarehouse(
                $this->product,
                $warehouseId,
                (float) $data['quantity'],
                'purchase',
                $data['description'] ?: 'ورود کالا از خرید',
            );
        } else {
            $warehouseService->removeFromWarehouse(
                $this->product,
                $warehouseId,
                (float) $data['quantity'],
                'sale',
                $data['description'] ?: 'فروش/خروج کالا',
            );
        }

        $this->product->refresh();

        session()->flash(
            'success',
            $this->formType === 'purchase' ? 'ورود کالا با موفقیت ثبت شد' : 'خروج کالا ثبت شد'
        );

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function render()
    {
        $movements = $this->product
            ->stockMovements()
            ->with('warehouse:id,name')
            ->latest()
            ->paginate(20);

        return view('livewire.products.stock-manager', [
            'movements' => $movements,
            'warehouses' => Warehouse::where('is_active', true)->orderBy('is_default', 'desc')->orderBy('name')->get(['id', 'name']),
            'warehouseStocks' => $this->product
                ->warehouseStocks()
                ->with('warehouse:id,name')
                ->where('quantity', '!=', 0)
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------|
    |                      خروجی اکسل و CSV گردش کالا                     |
    |--------------------------------------------------------------------|
    */
    public function exportCsv()
    {
        $fileName = 'گردش-کالا-' . $this->product->barcode . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // BOM برای نمایش صحیح حروف فارسی در Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['تاریخ', 'نوع عملیات', 'مقدار', 'واحد', 'توضیحات']);

            $this->product->stockMovements()->latest()->chunk(500, function ($movements) use ($handle) {
                foreach ($movements as $movement) {
                    fputcsv($handle, [
                        $movement->created_at->format('Y-m-d H:i'),
                        $this->movementTypeLabel($movement->type),
                        $movement->quantity,
                        $this->product->unit,
                        $movement->description,
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExcel()
    {
        $fileName = 'گردش-کالا-' . $this->product->barcode . '.xls';
        $product = $this->product;

        return response()->streamDownload(function () use ($product) {
            echo "\xEF\xBB\xBF"; // BOM برای نمایش صحیح حروف فارسی
            echo '<html><head><meta charset="UTF-8"></head><body dir="rtl">';
            echo '<table border="1">';
            echo '<thead><tr>
                    <th>تاریخ</th>
                    <th>نوع عملیات</th>
                    <th>مقدار</th>
                    <th>واحد</th>
                    <th>توضیحات</th>
                  </tr></thead><tbody>';

            $product->stockMovements()->latest()->chunk(500, function ($movements) use ($product) {
                foreach ($movements as $movement) {
                    echo '<tr>'
                        . '<td>' . e($movement->created_at->format('Y-m-d H:i')) . '</td>'
                        . '<td>' . e($this->movementTypeLabel($movement->type)) . '</td>'
                        . '<td>' . e($movement->quantity) . '</td>'
                        . '<td>' . e($product->unit) . '</td>'
                        . '<td>' . e($movement->description) . '</td>'
                        . '</tr>';
                }
            });

            echo '</tbody></table></body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function movementTypeLabel(string $type): string
    {
        return match ($type) {
            'initial' => 'موجودی اولیه',
            'purchase' => 'خرید',
            'sale' => 'فروش',
            'adjust' => 'اصلاح',
            'transfer' => 'انتقال بین انبار',
            'count' => 'انبارگردانی',
            'return' => 'مرجوعی',
            default => $type,
        };
    }
}