<?php

namespace App\Livewire\Warehouses;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseQRCode;
use App\Services\QRCodeService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class QrCodeManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $search = '';
    public string $filterExpiration = 'all';
    public string $filterWarehouse = '';

    // فرم ایجاد QR Code
    public bool $showFormModal = false;
    public ?int $editingId = null;

    public string $warehouse_id = '';
    public ?string $product_id = null;
    public string $unit_type = 'box';
    public string $quantity_in_unit = '1';
    public string $production_date_jalali = '';
    public string $expiration_date_jalali = '';
    public string $entry_date_jalali = '';
    public string $location_section = '';
    public string $location_shelf = '';
    public string $location_row = '';
    public string $location_position = '';
    public array $productsBarcode = [];
    public string $barcodeInput = '';
    public string $notes = '';
    public ?string $performed_by = null;

    // نمایش QR
    public bool $showQrModal = false;
    public ?WarehouseQRCode $viewingQr = null;

    // حذف
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'unit_type' => ['required', 'in:box,carton,shelf,pallet,bag,other'],
            'quantity_in_unit' => ['required', 'integer', 'min:1', 'max:999999'],
            'production_date_jalali' => ['nullable'],
            'expiration_date_jalali' => ['nullable'],
            'entry_date_jalali' => ['nullable'],
            'location_section' => ['nullable', 'string', 'max:100'],
            'location_shelf' => ['nullable', 'string', 'max:50'],
            'location_row' => ['nullable', 'string', 'max:50'],
            'location_position' => ['nullable', 'string', 'max:50'],
            'performed_by' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'warehouse_id.required' => 'انتخاب انبار الزامی است.',
            'unit_type.required' => 'نوع واحد الزامی است.',
            'quantity_in_unit.required' => 'تعداد محصول در واحد الزامی است.',
            'quantity_in_unit.min' => 'تعداد باید حداقل ۱ باشد.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->entry_date_jalali = verta()->format('Y/m/d');
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $qr = WarehouseQRCode::findOrFail($id);

        $this->editingId = $qr->id;
        $this->warehouse_id = (string) $qr->warehouse_id;
        $this->product_id = $qr->product_id ? (string) $qr->product_id : null;
        $this->unit_type = $qr->unit_type;
        $this->quantity_in_unit = (string) $qr->quantity_in_unit;
        $this->production_date_jalali = gregorianToJalaliInput($qr->production_date);
        $this->expiration_date_jalali = gregorianToJalaliInput($qr->expiration_date);
        $this->entry_date_jalali = gregorianToJalaliInput($qr->entry_date);
        $this->location_section = $qr->location_section ?? '';
        $this->location_shelf = $qr->location_shelf ?? '';
        $this->location_row = $qr->location_row ?? '';
        $this->location_position = $qr->location_position ?? '';
        $this->productsBarcode = $qr->products_barcode ?? [];
        $this->notes = $qr->notes ?? '';
        $this->performed_by = $qr->performed_by ? (string) $qr->performed_by : null;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function save(QRCodeService $qrService): void
    {
        $this->authorizeAction($this->editingId ? 'qrcodes.edit' : 'qrcodes.create');

        $validated = $this->validate();

        // تبدیل تاریخ‌های شمسی به میلادی
        $productionDate = jalaliToGregorian($this->production_date_jalali);
        $expirationDate = jalaliToGregorian($this->expiration_date_jalali);
        $entryDate = jalaliToGregorian($this->entry_date_jalali);

        DB::transaction(function () use ($validated, $productionDate, $expirationDate, $entryDate, $qrService) {
            $warehouse = Warehouse::find($validated['warehouse_id']);

            if ($this->editingId) {
                $qr = WarehouseQRCode::findOrFail($this->editingId);
                $qr->update([
                    'warehouse_id' => $validated['warehouse_id'],
                    'product_id' => $validated['product_id'] ? (int) $validated['product_id'] : null,
                    'unit_type' => $validated['unit_type'],
                    'quantity_in_unit' => (int) $validated['quantity_in_unit'],
                    'production_date' => $productionDate,
                    'expiration_date' => $expirationDate,
                    'entry_date' => $entryDate,
                    'location_section' => $validated['location_section'] ?: null,
                    'location_shelf' => $validated['location_shelf'] ?: null,
                    'location_row' => $validated['location_row'] ?: null,
                    'location_position' => $validated['location_position'] ?: null,
                    'products_barcode' => $this->productsBarcode,
                    'performed_by' => $validated['performed_by'] ? (int) $validated['performed_by'] : auth()->id(),
                    'notes' => $validated['notes'] ?: null,
                ]);

                // بازتولید QR
                $qrService->regenerate($qr);
            } else {
                $identifier = WarehouseQRCode::generateIdentifier($warehouse);

                $qr = WarehouseQRCode::create([
                    'qr_identifier' => $identifier,
                    'warehouse_id' => $validated['warehouse_id'],
                    'product_id' => $validated['product_id'] ? (int) $validated['product_id'] : null,
                    'unit_type' => $validated['unit_type'],
                    'quantity_in_unit' => (int) $validated['quantity_in_unit'],
                    'production_date' => $productionDate,
                    'expiration_date' => $expirationDate,
                    'entry_date' => $entryDate,
                    'location_section' => $validated['location_section'] ?: null,
                    'location_shelf' => $validated['location_shelf'] ?: null,
                    'location_row' => $validated['location_row'] ?: null,
                    'location_position' => $validated['location_position'] ?: null,
                    'products_barcode' => $this->productsBarcode,
                    'notes' => $validated['notes'] ?: null,
                    'created_by' => auth()->id(),
                    'performed_by' => $validated['performed_by'] ? (int) $validated['performed_by'] : auth()->id(),
                ]);

                // تولید تصویر QR
                $path = $qrService->generate($qr);
                $qr->update(['qr_image_path' => $path]);
            }
        });

        session()->flash('success', $this->editingId ? 'QR Code ویرایش شد.' : 'QR Code جدید ساخته شد.');

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function addBarcode(): void
    {
        $barcode = trim($this->barcodeInput);

        if ($barcode === '') {
            return;
        }

        if (! in_array($barcode, $this->productsBarcode)) {
            $this->productsBarcode[] = $barcode;
        }

        $this->barcodeInput = '';
    }

    public function removeBarcode(int $index): void
    {
        unset($this->productsBarcode[$index]);
        $this->productsBarcode = array_values($this->productsBarcode);
    }

    public function viewQr(int $id): void
    {
        $this->viewingQr = WarehouseQRCode::with(['warehouse', 'product', 'creator', 'performer'])->findOrFail($id);
        $this->showQrModal = true;
    }

    public function downloadQr(int $id)
    {
        $this->authorizeAction('qrcodes.view');

        $qr = WarehouseQRCode::findOrFail($id);

        if (! $qr->qr_image_path) {
            session()->flash('error', 'تصویر QR یافت نشد.');

            return;
        }

        $path = storage_path("app/public/{$qr->qr_image_path}");

        if (! file_exists($path)) {
            session()->flash('error', 'فایل QR یافت نشد.');

            return;
        }

        return response()->download($path, "qr-{$qr->qr_identifier}.svg", [
            'Content-Type' => 'image/svg+xml',
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(QRCodeService $qrService): void
    {
        $this->authorizeAction('qrcodes.delete');

        $qr = WarehouseQRCode::findOrFail($this->deletingId);

        $qrService->delete($qr);
        $qr->delete();

        session()->flash('success', 'QR Code حذف شد.');

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showQrModal = false;
        $this->showDeleteModal = false;
        $this->resetErrorBag();
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'warehouse_id', 'product_id', 'unit_type', 'quantity_in_unit',
            'production_date_jalali', 'expiration_date_jalali', 'entry_date_jalali',
            'location_section', 'location_shelf', 'location_row', 'location_position',
            'productsBarcode', 'barcodeInput', 'notes',
        ]);
        $this->unit_type = 'box';
        $this->quantity_in_unit = '1';
        $this->resetErrorBag();
    }

    /**
     * تبدیل تاریخ شمسی به میلادی
     */
    private function parseJalaliDate(string $jalali): ?string
    {
        if (empty(trim($jalali))) {
            return null;
        }

        try {
            $v = verta($jalali);

            return $v->formatGregorian('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    public function render()
    {
        $query = WarehouseQRCode::with(['warehouse:id,name,code', 'product:id,name,barcode'])
            ->with('creator:id,name')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $term = "%{$this->search}%";
                    $sub->where('qr_identifier', 'like', $term)
                        ->orWhere('location_section', 'like', $term)
                        ->orWhere('location_shelf', 'like', $term)
                        ->orWhere('location_row', 'like', $term)
                        ->orWhereHas('product', fn ($p) => $p->where('name', 'like', $term));
                });
            })
            ->when($this->filterWarehouse, function ($q) {
                $q->where('warehouse_id', $this->filterWarehouse);
            })
            ->when($this->filterExpiration === 'expired', function ($q) {
                $q->whereNotNull('expiration_date')
                    ->whereDate('expiration_date', '<', now()->toDateString());
            })
            ->when($this->filterExpiration === 'near', function ($q) {
                $q->whereNotNull('expiration_date')
                    ->whereDate('expiration_date', '>=', now()->toDateString())
                    ->whereDate('expiration_date', '<=', now()->addDays(30)->toDateString());
            })
            ->when($this->filterExpiration === 'no_date', function ($q) {
                $q->whereNull('expiration_date');
            });

        $qrcodes = $query->latest()->paginate(15);

        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']);
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'barcode', 'unit']);

        $stats = [
            'total' => WarehouseQRCode::count(),
            'expired' => WarehouseQRCode::whereNotNull('expiration_date')
                ->whereDate('expiration_date', '<', now()->toDateString())->count(),
            'near' => WarehouseQRCode::whereNotNull('expiration_date')
                ->whereDate('expiration_date', '>=', now()->toDateString())
                ->whereDate('expiration_date', '<=', now()->addDays(30)->toDateString())->count(),
        ];

        return view('livewire.warehouses.qr-code-manager', [
            'qrcodes' => $qrcodes,
            'warehouses' => $warehouses,
            'products' => $products,
            'users' => \App\Models\User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'stats' => $stats,
            'unitTypes' => WarehouseQRCode::unitTypes(),
        ]);
    }
}