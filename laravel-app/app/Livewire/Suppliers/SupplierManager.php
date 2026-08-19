<?php

namespace App\Livewire\Suppliers;

use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierManager extends Component
{
    use WithPagination;

    // فیلدهای فرم (مشترک بین ساخت و ویرایش)
    public ?int $supplierId = null;
    public string $name = '';
    public ?string $company_name = null;
    public ?string $contact_person = null;
    public string $type = 'individual';
    public ?string $national_id = null;
    public ?string $economic_code = null;
    public ?string $registration_number = null;
    public string $mobile = '';
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $website = null;
    public ?string $province = null;
    public ?string $city = null;
    public ?string $address = null;
    public ?string $postal_code = null;
    public float $credit_limit = 0;
    public float $opening_balance = 0;
    public ?string $bank_account_number = null;
    public ?string $iban = null;
    public ?string $payment_terms = null;
    public ?int $rating = null;
    public ?string $notes = null;
    public bool $is_active = true;

    // کنترل مودال‌ها
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:100'],
            'company_name'        => ['nullable', 'string', 'max:150'],
            'contact_person'      => ['nullable', 'string', 'max:100'],
            'type'                => ['required', Rule::in(['individual', 'company'])],
            'national_id'         => ['nullable', 'string', 'max:20'],
            'economic_code'       => ['nullable', 'string', 'max:30'],
            'registration_number' => ['nullable', 'string', 'max:30'],
            'mobile'              => [
                'required', 'string', 'max:15',
                Rule::unique('suppliers', 'mobile')->ignore($this->supplierId),
            ],
            'phone'               => ['nullable', 'string', 'max:15'],
            'email'               => ['nullable', 'email', 'max:100'],
            'website'             => ['nullable', 'url', 'max:150'],
            'province'            => ['nullable', 'string', 'max:100'],
            'city'                => ['nullable', 'string', 'max:100'],
            'address'             => ['nullable', 'string'],
            'postal_code'         => ['nullable', 'string', 'max:15'],
            'credit_limit'        => ['nullable', 'numeric', 'min:0'],
            'opening_balance'     => ['nullable', 'numeric'],
            'bank_account_number' => ['nullable', 'string', 'max:30'],
            'iban'                => ['nullable', 'string', 'max:34'],
            'payment_terms'       => ['nullable', 'string', 'max:100'],
            'rating'              => ['nullable', 'integer', 'min:1', 'max:5'],
            'notes'               => ['nullable', 'string'],
            'is_active'           => ['boolean'],
        ];
    }

    protected array $messages = [
        'name.required'   => 'وارد کردن نام الزامی است.',
        'mobile.required' => 'وارد کردن موبایل الزامی است.',
        'mobile.unique'   => 'این شماره موبایل قبلاً برای تامین‌کننده دیگری ثبت شده است.',
        'type.required'   => 'نوع تامین‌کننده را انتخاب کنید.',
    ];

    /**
     * باز کردن مودال برای ساخت تامین‌کننده جدید
     */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    /**
     * باز کردن مودال برای ویرایش یک تامین‌کننده
     */
    public function openEditModal(int $id): void
    {
        $supplier = Supplier::findOrFail($id);

        $this->supplierId          = $supplier->id;
        $this->name                = $supplier->name;
        $this->company_name        = $supplier->company_name;
        $this->contact_person      = $supplier->contact_person;
        $this->type                = $supplier->type;
        $this->national_id         = $supplier->national_id;
        $this->economic_code       = $supplier->economic_code;
        $this->registration_number = $supplier->registration_number;
        $this->mobile              = $supplier->mobile;
        $this->phone               = $supplier->phone;
        $this->email               = $supplier->email;
        $this->website              = $supplier->website;
        $this->province            = $supplier->province;
        $this->city                = $supplier->city;
        $this->address             = $supplier->address;
        $this->postal_code         = $supplier->postal_code;
        $this->credit_limit        = (float) $supplier->credit_limit;
        $this->opening_balance     = (float) $supplier->opening_balance;
        $this->bank_account_number = $supplier->bank_account_number;
        $this->iban                = $supplier->iban;
        $this->payment_terms       = $supplier->payment_terms;
        $this->rating              = $supplier->rating;
        $this->notes               = $supplier->notes;
        $this->is_active           = (bool) $supplier->is_active;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    /**
     * ذخیره (ساخت یا ویرایش) تامین‌کننده
     */
    public function save(): void
    {
        $validated = $this->validate();

        if ($this->supplierId) {
            $supplier = Supplier::findOrFail($this->supplierId);
            $supplier->update($validated);
            session()->flash('success', 'اطلاعات تامین‌کننده بروزرسانی شد.');
        } else {
            $validated['code'] = $this->generateCode();
            $validated['created_by'] = auth()->id();
            Supplier::create($validated);
            session()->flash('success', 'تامین‌کننده جدید با موفقیت ثبت شد.');
        }

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    /**
     * باز کردن مودال تایید حذف
     */
    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * حذف نرم تامین‌کننده
     */
    public function delete(): void
    {
        if ($this->deletingId) {
            Supplier::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'تامین‌کننده حذف شد.');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'supplierId', 'name', 'company_name', 'contact_person', 'type',
            'national_id', 'economic_code', 'registration_number', 'mobile',
            'phone', 'email', 'website', 'province', 'city', 'address',
            'postal_code', 'credit_limit', 'opening_balance',
            'bank_account_number', 'iban', 'payment_terms', 'rating', 'notes',
        ]);
        $this->type = 'individual';
        $this->credit_limit = 0;
        $this->opening_balance = 0;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    private function generateCode(): string
    {
        $last = Supplier::withTrashed()->latest('id')->first();
        $nextNumber = $last ? ((int) Str::afterLast($last->code, '-') + 1) : 1;

        return 'SUP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.suppliers.supplier-manager', [
            'suppliers' => Supplier::latest()->paginate(15),
        ]);
    }
}