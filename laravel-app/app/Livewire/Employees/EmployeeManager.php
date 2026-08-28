<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeeManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلترها                                |
    |--------------------------------------------------------------------|
    */
    public string $search = '';
    public string $filterStatus = '';

    /*
    |--------------------------------------------------------------------|
    |                        فیلدهای فرم افزودن/ویرایش                    |
    |--------------------------------------------------------------------|
    */
    public ?int $editingId = null;
    public string $first_name = '';
    public string $last_name = '';
    public ?string $mobile = null;
    public ?string $national_code = null;
    public ?string $job_title = null;
    public ?string $hired_at = null;
    public ?string $base_salary = null;
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus']);
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile' => [
                'nullable', 'string', 'max:20',
                $this->editingId
                    ? Rule::unique('employees', 'mobile')->ignore($this->editingId)
                    : Rule::unique('employees', 'mobile'),
            ],
            'national_code' => [
                'nullable', 'string', 'max:20',
                $this->editingId
                    ? Rule::unique('employees', 'national_code')->ignore($this->editingId)
                    : Rule::unique('employees', 'national_code'),
            ],
            'job_title' => ['nullable', 'string', 'max:150'],
            'hired_at' => ['nullable', 'date'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name.required' => 'وارد کردن نام الزامی است.',
            'last_name.required' => 'وارد کردن نام خانوادگی الزامی است.',
            'mobile.unique' => 'این شماره موبایل قبلاً برای کارمند دیگری ثبت شده است.',
            'national_code.unique' => 'این کد ملی قبلاً برای کارمند دیگری ثبت شده است.',
            'base_salary.min' => 'حقوق پایه نمی‌تواند منفی باشد.',
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
        $employee = Employee::findOrFail($id);

        $this->editingId = $employee->id;
        $this->first_name = $employee->first_name;
        $this->last_name = $employee->last_name;
        $this->mobile = $employee->mobile;
        $this->national_code = $employee->national_code;
        $this->job_title = $employee->job_title;
        $this->hired_at = $employee->hired_at ? $employee->hired_at->toDateString() : null;
        $this->base_salary = $employee->base_salary;
        $this->notes = $employee->notes;
        $this->is_active = (bool) $employee->is_active;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->mobile = null;
        $this->national_code = null;
        $this->job_title = null;
        $this->hired_at = null;
        $this->base_salary = null;
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
        $data['base_salary'] = $data['base_salary'] !== null && $data['base_salary'] !== ''
            ? (float) $data['base_salary']
            : 0;

        if ($this->editingId) {
            Employee::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'کارمند با موفقیت ویرایش شد');
        } else {
            Employee::create($data);
            session()->flash('success', 'کارمند با موفقیت ثبت شد');
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
            Employee::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'کارمند حذف شد');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function render()
    {
        $query = Employee::query();

        if ($this->search !== '') {
            $query->search($this->search);
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', $this->filterStatus === 'active');
        }

        $employees = $query->latest()->paginate(15);

        return view('livewire.employees.employee-manager', [
            'employees' => $employees,
            'totalEmployees' => Employee::count(),
            'activeEmployees' => Employee::where('is_active', true)->count(),
            'inactiveEmployees' => Employee::where('is_active', false)->count(),
            'monthlySalaryTotal' => Employee::where('is_active', true)->sum('base_salary'),
        ]);
    }
}
