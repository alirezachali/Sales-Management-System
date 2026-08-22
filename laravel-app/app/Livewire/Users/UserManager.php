<?php

namespace App\Livewire\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
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
    |                     فیلدهای فرم افزودن/ویرایش                       |
    |--------------------------------------------------------------------|
    */
    public ?int $editingId = null;
    public string $name = '';
    public string $username = '';
    public ?string $email = null;
    public ?string $phone = null;
    public string $role_id = '';
    public bool $is_active = true;

    // رمز عبور (فقط هنگام ساخت کاربر جدید استفاده می‌شود)
    public string $password = '';
    public string $password_confirmation = '';

    /*
    |--------------------------------------------------------------------|
    |                     فیلدهای مودال تغییر رمز عبور                    |
    |--------------------------------------------------------------------|
    */
    public ?int $passwordUserId = null;
    public string $passwordUserName = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    /*
    |--------------------------------------------------------------------|
    |                              مودال‌ها                               |
    |--------------------------------------------------------------------|
    */
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public bool $showPasswordModal = false;
    public ?int $deletingId = null;
    public string $deletingName = '';

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
            'name'      => ['required', 'string', 'max:255'],
            'username'  => [
                'required', 'string', 'max:100',
                Rule::unique('users', 'username')->ignore($this->editingId),
            ],
            'email'     => [
                'nullable', 'email',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role_id'   => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
            'password'  => $this->editingId
                ? ['nullable']
                : ['required', 'confirmed', 'min:6'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'      => 'وارد کردن نام الزامی است.',
            'username.required'  => 'وارد کردن نام کاربری الزامی است.',
            'username.unique'    => 'این نام کاربری قبلاً ثبت شده است.',
            'email.email'        => 'ایمیل وارد شده معتبر نیست.',
            'email.unique'       => 'این ایمیل قبلاً ثبت شده است.',
            'role_id.required'   => 'انتخاب نقش الزامی است.',
            'role_id.exists'     => 'نقش انتخاب‌شده معتبر نیست.',
            'password.required'  => 'وارد کردن رمز عبور الزامی است.',
            'password.confirmed' => 'رمز عبور و تکرار آن یکسان نیستند.',
            'password.min'       => 'رمز عبور باید حداقل ۶ کاراکتر باشد.',
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
        $user = User::findOrFail($id);

        $this->editingId = $user->id;
        $this->name      = $user->name;
        $this->username  = $user->username;
        $this->email     = $user->email;
        $this->phone     = $user->phone;
        $this->role_id   = (string) $user->role_id;
        $this->is_active = (bool) $user->is_active;

        // رمز عبور در ویرایش لمس نمی‌شود (تغییر رمز مودال جداگانه دارد)
        $this->password = '';
        $this->password_confirmation = '';

        $this->resetErrorBag();
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'      => $this->name,
            'username'  => $this->username,
            'email'     => $this->email ?: null,
            'phone'     => $this->phone ?: null,
            'role_id'   => $this->role_id,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'کاربر با موفقیت ویرایش شد.');
        } else {
            $data['password'] = $this->password; // به‌صورت خودکار hash می‌شود (cast در مدل)
            User::create($data);
            session()->flash('success', 'کاربر با موفقیت ایجاد شد.');
        }

        $this->showFormModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    |                          تغییر رمز عبور                            |
    |--------------------------------------------------------------------|
    */
    public function openPasswordModal(int $id): void
    {
        $user = User::findOrFail($id);

        $this->passwordUserId   = $user->id;
        $this->passwordUserName = $user->name;
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->resetErrorBag();
        $this->showPasswordModal = true;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'new_password' => ['required', 'confirmed', 'min:6'],
        ], [
            'new_password.required'  => 'وارد کردن رمز عبور جدید الزامی است.',
            'new_password.confirmed' => 'رمز عبور و تکرار آن یکسان نیستند.',
            'new_password.min'       => 'رمز عبور باید حداقل ۶ کاراکتر باشد.',
        ]);

        User::findOrFail($this->passwordUserId)->update([
            'password' => $this->new_password,
        ]);

        session()->flash('success', 'رمز عبور با موفقیت تغییر کرد.');

        $this->showPasswordModal = false;
        $this->reset(['passwordUserId', 'passwordUserName', 'new_password', 'new_password_confirmation']);
    }

    /*
    |--------------------------------------------------------------------|
    |                                 حذف                                 |
    |--------------------------------------------------------------------|
    */
    public function confirmDelete(int $id): void
    {
        $user = User::findOrFail($id);

        $this->deletingId   = $user->id;
        $this->deletingName = $user->name;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            // امکان حذف کاربری که وارد سیستم شده وجود ندارد
            if ($this->deletingId === auth()->id()) {
                session()->flash('error', 'امکان حذف کاربر وارد شده وجود ندارد.');
                $this->showDeleteModal = false;
                $this->deletingId = null;

                return;
            }

            User::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'کاربر با موفقیت حذف شد.');
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->showPasswordModal = false;
        $this->deletingId = null;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name      = '';
        $this->username  = '';
        $this->email     = null;
        $this->phone     = null;
        $this->role_id   = '';
        $this->is_active = true;
        $this->password  = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = User::with('role');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterRoleId !== '') {
            $query->where('role_id', $this->filterRoleId);
        }

        return view('livewire.users.user-manager', [
            'users'      => $query->latest()->paginate(15),
            'roles'      => Role::orderBy('display_name')->get(),
            'totalUsers' => User::count(),
        ]);
    }
}
