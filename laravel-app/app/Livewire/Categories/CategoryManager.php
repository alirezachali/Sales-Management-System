<?php

namespace App\Livewire\Categories;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    protected string $paginationTheme = 'bootstrap';

    /*
    |--------------------------------------------------------------------|
    |                              فیلتر                                  |
    |--------------------------------------------------------------------|
    */
    public string $search = '';

    /*
    |--------------------------------------------------------------------|
    |                     فیلدهای فرم افزودن/ویرایش                       |
    |--------------------------------------------------------------------|
    */
    public ?int $editingId = null;
    public string $name = '';
    public ?string $description = null;
    public bool $is_active = true;

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;
    public string $deletingName = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search');
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'name'        => [
                'required', 'string', 'max:100',
                Rule::unique('categories', 'name')->ignore($this->editingId),
            ],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => __('categories.validation.name_required'),
            'name.unique'   => __('categories.validation.name_unique'),
            'name.max'      => __('categories.validation.name_max'),
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
        $category = Category::findOrFail($id);

        $this->editingId   = $category->id;
        $this->name        = $category->name;
        $this->description = $category->description;
        $this->is_active   = (bool) $category->is_active;

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeAction($this->editingId ? 'categories.edit' : 'categories.create');

        $this->validate();

        $data = [
            'name'        => $this->name,
            'description' => $this->description ?: null,
            'is_active'   => $this->is_active,
        ];

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update($data);
            session()->flash('success', __('categories.messages.updated'));
        } else {
            Category::create($data);
            session()->flash('success', __('categories.messages.created'));
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
    public function confirmDelete(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->deletingId   = $category->id;
        $this->deletingName = $category->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorizeAction('categories.delete');

        if ($this->deletingId) {
            $category = Category::findOrFail($this->deletingId);

            // دسته‌بندی دارای کالا قابل حذف نیست
            if ($category->products()->exists()) {
                session()->flash('error', __('categories.messages.delete_blocked'));
                $this->showDeleteModal = false;
                $this->deletingId = null;

                return;
            }

            $category->delete();
            session()->flash('success', __('categories.messages.deleted'));
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
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
        $this->editingId   = null;
        $this->name        = '';
        $this->description = null;
        $this->is_active   = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Category::withCount('products');

        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.categories.category-manager', [
            'categories'         => $query->latest()->paginate(15),
            'totalCategories'    => Category::count(),
            'activeCategories'   => Category::where('is_active', true)->count(),
            'inactiveCategories' => Category::where('is_active', false)->count(),
            'emptyCategories'    => Category::doesntHave('products')->count(),
        ]);
    }
}
