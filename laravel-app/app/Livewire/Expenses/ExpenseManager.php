<?php

namespace App\Livewire\Expenses;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Hekmatinasser\Verta\Verta;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلترها                                |
    |--------------------------------------------------------------------|
    */
    public string $search = '';
    public string $filterCategoryId = '';
    public string $filterPaymentMethod = '';
    public string $filterPeriod = '';

    /*
    |--------------------------------------------------------------------|
    |                        فیلدهای فرم افزودن/ویرایش                    |
    |--------------------------------------------------------------------|
    */
    public ?int $editingId = null;
    public string $title = '';
    public string $expense_category_id = '';
    public ?string $employee_id = null;
    public ?string $amount = null;
    public ?string $expense_date = null;
    public string $payment_method = 'cash';
    public ?string $description = null;
    public ?string $reference_number = null;

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public bool $showDetailsModal = false;
    public ?int $detailsId = null;
    public bool $showCategoryModal = false;
    public bool $showCategoryDeleteModal = false;
    public ?int $categoryId = null;
    public ?int $deletingCatId = null;
    public string $category_name = '';
    public ?string $category_description = null;
    public bool $category_is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPaymentMethod(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPeriod(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterCategoryId', 'filterPaymentMethod', 'filterPeriod']);
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'description' => ['nullable', 'string'],
            'reference_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'وارد کردن عنوان هزینه الزامی است.',
            'expense_category_id.required' => 'انتخاب دسته‌بندی هزینه الزامی است.',
            'amount.required' => 'وارد کردن مبلغ الزامی است.',
            'amount.min' => 'مبلغ نمی‌تواند منفی باشد.',
            'expense_date.required' => 'وارد کردن تاریخ هزینه الزامی است.',
        ];
    }

    /*
    |--------------------------------------------------------------------|
    |                            مودال هزینه‌ها                           |
    |--------------------------------------------------------------------|
    */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $expense = Expense::findOrFail($id);

        $this->editingId = $expense->id;
        $this->title = $expense->title;
        $this->expense_category_id = (string) $expense->expense_category_id;
        $this->employee_id = $expense->employee_id ? (string) $expense->employee_id : null;
        $this->amount = $expense->amount;
        $this->expense_date = $expense->expense_date->toDateString();
        $this->payment_method = $expense->payment_method;
        $this->description = $expense->description;
        $this->reference_number = $expense->reference_number;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function openDetails(int $id): void
    {
        $this->detailsId = $id;
        $this->showDetailsModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->showDetailsModal = false;
        $this->showCategoryModal = false;
        $this->showCategoryDeleteModal = false;
        $this->deletingId = null;
        $this->detailsId = null;
        $this->categoryId = null;
        $this->deletingCatId = null;
        $this->resetForm();
        $this->resetCategoryForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->expense_category_id = '';
        $this->employee_id = null;
        $this->amount = null;
        $this->expense_date = now()->toDateString();
        $this->payment_method = 'cash';
        $this->description = null;
        $this->reference_number = null;
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
        $data['employee_id'] = $data['employee_id'] ?: null;
        $data['amount'] = (float) $data['amount'];

        if ($this->editingId) {
            Expense::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'هزینه با موفقیت ویرایش شد');
        } else {
            Expense::create(array_merge($data, ['user_id' => auth()->id()]));
            session()->flash('success', 'هزینه با موفقیت ثبت شد');
        }

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Expense::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'هزینه حذف شد');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                    مودال دسته‌بندی هزینه‌ها                          |
    |--------------------------------------------------------------------|
    */
    public function openCategoryCreateModal(): void
    {
        $this->resetCategoryForm();
        $this->showCategoryModal = true;
    }

    public function openCategoryEditModal(int $id): void
    {
        $category = ExpenseCategory::findOrFail($id);

        $this->categoryId = $category->id;
        $this->category_name = $category->name;
        $this->category_description = $category->description;
        $this->category_is_active = (bool) $category->is_active;

        $this->resetErrorBag();
        $this->showCategoryModal = true;
    }

    public function confirmCategoryDelete(int $id): void
    {
        $this->deletingCatId = $id;
        $this->showCategoryDeleteModal = true;
    }

    private function resetCategoryForm(): void
    {
        $this->categoryId = null;
        $this->category_name = '';
        $this->category_description = null;
        $this->category_is_active = true;
        $this->resetErrorBag();
    }

    protected function categoryRules(): array
    {
        return [
            'category_name' => ['required', 'string', 'max:100'],
            'category_description' => ['nullable', 'string'],
            'category_is_active' => ['boolean'],
        ];
    }

    protected function categoryMessages(): array
    {
        return [
            'category_name.required' => 'وارد کردن نام دسته‌بندی الزامی است.',
        ];
    }

    public function saveCategory(): void
    {
        $data = $this->validate($this->categoryRules(), $this->categoryMessages());

        $data['name'] = $data['category_name'];
        $data['description'] = $data['category_description'];
        $data['is_active'] = $data['category_is_active'];

        if ($this->categoryId) {
            ExpenseCategory::findOrFail($this->categoryId)->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'is_active' => $data['is_active'],
            ]);
            session()->flash('success', 'دسته‌بندی هزینه با موفقیت ویرایش شد');
        } else {
            ExpenseCategory::create($data);
            session()->flash('success', 'دسته‌بندی هزینه با موفقیت ثبت شد');
        }

        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    public function deleteCategory(): void
    {
        if ($this->deletingCatId) {
            ExpenseCategory::findOrFail($this->deletingCatId)->delete();
            session()->flash('success', 'دسته‌بندی هزینه حذف شد');
        }

        $this->showCategoryDeleteModal = false;
        $this->deletingCatId = null;
    }

    /*
    |--------------------------------------------------------------------|
    |                                رندر                                 |
    |--------------------------------------------------------------------|
    */
    public function render()
    {
        $query = Expense::with(['category', 'employee', 'user']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('reference_number', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterCategoryId !== '') {
            $query->where('expense_category_id', $this->filterCategoryId);
        }

        if ($this->filterPaymentMethod !== '') {
            $query->where('payment_method', $this->filterPaymentMethod);
        }

        if ($this->filterPeriod !== '') {
            if ($this->filterPeriod === 'today') {
                $query->whereDate('expense_date', now()->toDateString());
            }

            if ($this->filterPeriod === 'month') {
                $query->whereBetween('expense_date', [
                    Verta::now()->startMonth()->toCarbon()->toDateString(),
                    Verta::now()->endMonth()->toCarbon()->toDateString(),
                ]);
            }
        }

        $expenses = $query->latest('expense_date')->paginate(15);

        $detailsExpense = $this->detailsId
            ? Expense::with(['category', 'employee', 'user'])->find($this->detailsId)
            : null;

        $todayStart = now()->toDateString();
        $monthStart = Verta::now()->startMonth()->toCarbon()->toDateString();
        $monthEnd = Verta::now()->endMonth()->toCarbon()->toDateString();

        return view('livewire.expenses.expense-manager', [
            'expenses' => $expenses,
            'categories' => ExpenseCategory::active()->orderBy('name')->get(),
            'allCategories' => ExpenseCategory::orderBy('name')->get([
                'id', 'name', 'is_active',
            ]),
            'employees' => Employee::active()->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'paymentMethods' => Expense::PAYMENT_METHODS,
            'totalToday' => Expense::whereDate('expense_date', $todayStart)->sum('amount'),
            'totalMonth' => Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount'),
            'totalAll' => Expense::sum('amount'),
            'expenseCount' => Expense::count(),
            'topCategories' => Expense::with('category')
                ->selectRaw('expense_category_id, SUM(amount) as total')
                ->groupBy('expense_category_id')
                ->orderByDesc('total')
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->limit(5)
                ->get(),
            'detailsExpense' => $detailsExpense,
        ]);
    }
}
