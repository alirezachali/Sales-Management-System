<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\CustomerRole;
use App\Services\CustomerAccountService;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلترها                                |
    |--------------------------------------------------------------------|
    */
    public string $search = '';
    public string $filterRoleId = '';

    /*
    |--------------------------------------------------------------------|
    |                        فیلدهای فرم افزودن/ویرایش                    |
    |--------------------------------------------------------------------|
    */
    public ?int $editingId = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $mobile = '';
    public ?string $phone = null;
    public ?string $national_code = null;
    public ?string $birth_date = null;
    public ?string $gender = null;
    public ?string $province = null;
    public ?string $city = null;
    public ?string $postal_code = null;
    public ?string $address = null;
    public string $customer_role_id = '';
    public ?string $notes = null;
    public bool $is_active = true;

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public bool $showLedgerModal = false;
    public ?int $ledgerCustomerId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRoleId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterRoleId']);
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile' => [
                'required', 'string', 'max:20',
                $this->editingId
                    ? Rule::unique('customers', 'mobile')->ignore($this->editingId)
                    : Rule::unique('customers', 'mobile'),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'national_code' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'customer_role_id' => ['nullable', 'exists:customer_roles,id'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name.required' => 'وارد کردن نام الزامی است.',
            'last_name.required' => 'وارد کردن نام خانوادگی الزامی است.',
            'mobile.required' => 'وارد کردن شماره موبایل الزامی است.',
            'mobile.unique' => 'این شماره موبایل قبلاً برای مشتری دیگری ثبت شده است.',
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                        مودال افزودن/ویرایش                         |
    |--------------------------------------------------------------------|
    */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $customer = Customer::findOrFail($id);

        $this->editingId = $customer->id;
        $this->first_name = $customer->first_name;
        $this->last_name = $customer->last_name;
        $this->mobile = $customer->mobile;
        $this->phone = $customer->phone;
        $this->national_code = $customer->national_code;
        $this->birth_date = $customer->birth_date ? (string) $customer->birth_date : null;
        $this->gender = $customer->gender;
        $this->province = $customer->province;
        $this->city = $customer->city;
        $this->postal_code = $customer->postal_code;
        $this->address = $customer->address;
        $this->customer_role_id = (string) $customer->customer_role_id;
        $this->notes = $customer->notes;
        $this->is_active = (bool) $customer->is_active;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function openLedger(int $id): void
    {
        $this->ledgerCustomerId = $id;
        $this->showLedgerModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->showLedgerModal = false;
        $this->ledgerCustomerId = null;
        $this->deletingId = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->mobile = '';
        $this->phone = null;
        $this->national_code = null;
        $this->birth_date = null;
        $this->gender = null;
        $this->province = null;
        $this->city = null;
        $this->postal_code = null;
        $this->address = null;
        $this->customer_role_id = '';
        $this->notes = null;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    /*
    |--------------------------------------------------------------------|
    |                          ذخیره (افزودن/ویرایش)                      |
    |--------------------------------------------------------------------|
    */
    public function save(): void
    {
        $data = $this->validate();
        $data['customer_role_id'] = $data['customer_role_id'] ?: null;

        if ($this->editingId) {
            Customer::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'مشتری با موفقیت ویرایش شد');
        } else {
            Customer::create($data);
            session()->flash('success', 'مشتری با موفقیت ثبت شد و به باشگاه مشتریان اضافه شد');
        }

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                                 حذف                                 |
    |--------------------------------------------------------------------|
    */
    public function delete(): void
    {
        if ($this->deletingId) {
            Customer::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'مشتری حذف شد');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                    بازمحاسبه‌ی دستی رده‌ی مشتری                     |
    |--------------------------------------------------------------------|
    */
    public function recalculateRole(int $id): void
    {
        $customer = Customer::findOrFail($id);
        $customer->recalculateRole();

        session()->flash('success', 'رده‌ی باشگاه مشتریان این مشتری بازمحاسبه شد');
    }

    public function render()
    {
        $query = Customer::with('role');

        if ($this->search !== '') {
            $query->search($this->search);
        }

        if ($this->filterRoleId !== '') {
            $query->where('customer_role_id', $this->filterRoleId);
        }

        $customers = $query->latest()->paginate(15);

        $ledgerCustomer = null;
        $ledgerTransactions = null;
        $ledgerBalance = null;

        if ($this->ledgerCustomerId) {
            $ledgerCustomer = Customer::find($this->ledgerCustomerId);

            if ($ledgerCustomer) {
                $ledgerTransactions = $ledgerCustomer->accountTransactions()->latest()->limit(20)->get();
                $ledgerBalance = app(CustomerAccountService::class)->balance($ledgerCustomer->id);
            }
        }

        return view('livewire.customers.customer-manager', [
            'customers' => $customers,
            'roles' => CustomerRole::orderBy('sort_order')->get(),
            'totalCustomers' => Customer::count(),
            'activeCustomers' => Customer::where('is_active', true)->count(),
            'inactiveCustomers' => Customer::where('is_active', false)->count(),
            'noRoleCustomers' => Customer::whereNull('customer_role_id')->count(),
            'ledgerCustomer' => $ledgerCustomer,
            'ledgerTransactions' => $ledgerTransactions,
            'ledgerBalance' => $ledgerBalance,
        ]);
    }
}