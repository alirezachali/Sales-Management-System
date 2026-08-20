<?php

namespace App\Livewire\Products;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockManager extends Component
{
    use WithPagination;

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

    public function mount(Product $product): void
    {
        $this->product = $product;

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
        ];
    }

    protected function messages(): array
    {
        return [
            'quantity.required' => 'وارد کردن مقدار الزامی است.',
            'quantity.min' => 'مقدار باید بزرگ‌تر از صفر باشد.',
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
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    |                          ثبت ورود/خروج کالا                        |
    |--------------------------------------------------------------------|
    */
    public function save(): void
    {
        $data = $this->validate();

        if ($this->formType === 'sale' && $this->product->stock < $data['quantity']) {
            $this->addError('quantity', 'موجودی کالا کافی نیست.');
            return;
        }

        DB::transaction(function () use ($data) {
            if ($this->formType === 'purchase') {
                $this->product->increment('stock', $data['quantity']);

                StockMovement::create([
                    'product_id' => $this->product->id,
                    'type' => 'purchase',
                    'quantity' => $data['quantity'],
                    'description' => $data['description'] ?? 'ورود کالا از خرید',
                ]);
            } else {
                $this->product->decrement('stock', $data['quantity']);

                StockMovement::create([
                    'product_id' => $this->product->id,
                    'type' => 'sale',
                    'quantity' => $data['quantity'],
                    'description' => $data['description'] ?? 'فروش کالا',
                ]);
            }
        });

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
            ->latest()
            ->paginate(20);

        return view('livewire.products.stock-manager', [
            'movements' => $movements,
        ]);
    }
}