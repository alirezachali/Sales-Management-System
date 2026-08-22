<?php

namespace App\Livewire\Roles;

use App\Models\Role;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class RoleManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    /*
    |--------------------------------------------------------------------|
    |                     فیلدهای فرم افزودن/ویرایش                       |
    |--------------------------------------------------------------------|
    */
    public ?int $editingId = null;
    public string $display_name = '';
    public string $name = '';
    public ?string $description = null;
    public string $color = 'primary';
    public string $icon = 'bi-shield-lock';

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

    protected function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'name'         => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->ignore($this->editingId),
            ],
            'description'  => ['nullable', 'string', 'max:500'],
            'color'        => ['nullable', 'string', 'max:20'],
            'icon'         => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function messages(): array
    {
        return [
            'display_name.required' => 'وارد کردن نام نقش الزامی است.',
            'name.required'         => 'وارد کردن شناسه نقش الزامی است.',
            'name.unique'           => 'این شناسه قبلاً برای نقش دیگری ثبت شده است.',
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
        $role = Role::findOrFail($id);

        $this->editingId    = $role->id;
        $this->display_name = $role->display_name;
        $this->name         = $role->name;
        $this->description  = $role->description;
        $this->color        = $role->color ?? 'primary';
        $this->icon         = $role->icon ?? 'bi-shield-lock';

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            Role::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'نقش با موفقیت ویرایش شد.');
        } else {
            Role::create($validated);
            session()->flash('success', 'نقش با موفقیت ایجاد شد.');
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
        $role = Role::findOrFail($id);

        $this->deletingId   = $role->id;
        $this->deletingName = $role->display_name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $role = Role::findOrFail($this->deletingId);

            // نقشی که به کاربران اختصاص داده شده قابل حذف نیست
            if ($role->users()->exists()) {
                session()->flash('error', 'این نقش به یک یا چند کاربر اختصاص داده شده و قابل حذف نیست.');
                $this->showDeleteModal = false;
                $this->deletingId = null;

                return;
            }

            $role->delete();
            session()->flash('success', 'نقش با موفقیت حذف شد.');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId    = null;
        $this->display_name = '';
        $this->name         = '';
        $this->description  = null;
        $this->color        = 'primary';
        $this->icon         = 'bi-shield-lock';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Role::withCount('users');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('display_name', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.roles.role-manager', [
            'roles' => $query->latest()->paginate(15),
        ]);
    }
}
