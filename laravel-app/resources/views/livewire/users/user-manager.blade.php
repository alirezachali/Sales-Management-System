<div dir="rtl">

    {{-- نمایش پیغام‌های موفقیت --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{-- نمایش پیغام‌های خطا --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif


    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">تعداد کاربران</div>
                    <div class="h1 mb-0">
                        {{ $totalUsers }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">کاربران فعال</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $activeCategories }} --}}2
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">کاربران غیرفعال</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $inactiveCategories }} --}}1
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">تعداد کارمندان</div>
                    <div class="h1 mb-0 text-warning">
                        {{-- {{ $emptyCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- کارت جستجو و فیلتر --}}
    <div class="card glass-card mb-4">
        <div class="card-body">
            <div class="row g-2">

                <div class="col-lg-7">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" wire:model.live.debounce.400ms="search"
                            placeholder="جستجو بر اساس نام، نام کاربری یا موبایل">
                    </div>
                </div>

                <div class="col-lg-3">
                    <select wire:model.live="filterRoleId" class="form-select">
                        <option value="">همه نقش‌ها</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100"
                        title="پاک کردن فیلترهای جستجو">
                        پاک کردن
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- جدول کاربران --}}
    <div class="card shadow-sm" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    مدیریت کاربران
                </h3>
                <small class="text-muted">مدیریت اطلاعات کاربران سیستم</small>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('roles.index') }}">
                    <button class="btn btn-info text-dark" title=" مدیریت نقش‌هاو مجوزهای دسترسی آنها">
                        <i class="bi bi-shield-lock"></i>
                        مدیریت نقش‌ها
                    </button>
                </a>

                <button class="btn btn-primary" wire:click="openCreateModal"
                    title="برای افزودن کاربر جدید به سیستم کلیک کنید">
                    <i class="bi bi-plus-circle"></i>
                    افزودن کاربر
                </button>

            </div>
        </div>


        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="50">ردیف</th>
                            <th>نام</th>
                            <th width="122">نام کاربری</th>
                            <th width="80">وضعیت</th>
                            <th width="110">نقش</th>
                            <th width="220">آخرین ورود</th>
                            <th width="130">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge bg-success text-dark">فعال</span>
                                    @else
                                        <span class="badge bg-danger text-dark">غیرفعال</span>
                                    @endif
                                </td>
                                <td>{{ $user->role?->display_name ?? '-' }}</td>
                                <td>{{ $user->last_login_at ? jalaliDateTime($user->last_login_at) : '-' }}</td>
                                <td>
                                    {{-- دکمه ویرایش مشخصات یک کاربر --}}
                                    <button type="button" class="btn btn-sm btn-warning text-dark"
                                        wire:click="openEditModal({{ $user->id }})"
                                        title="برای ویرایش این کاربر کلیک کنید">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    {{-- دکمه تغییر رمز ورود یک کاربر --}}
                                    <button type="button" class="btn btn-sm btn-info text-dark"
                                        wire:click="openPasswordModal({{ $user->id }})"
                                        title="برای تغییر کلمه عبور این کاربر کلیک کنید">
                                        <i class="bi bi-key-fill"></i>
                                    </button>
                                    {{-- دکمه حذف یک کاربر --}}
                                    <button type="button" class="btn btn-sm btn-danger text-dark"
                                        wire:click="confirmDelete({{ $user->id }})"
                                        title="برای حذف این کاربر کلیک کنید">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    هیچ کاربری ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>

    {{-- ============================ مودال افزودن/ویرایش کاربر ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="user-form-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? 'ویرایش کاربر' : 'افزودن کاربر جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"
                                title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">نام و نام خانوادگی <span
                                            class="text-danger">*</span></label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">نام کاربری <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="username"
                                        class="form-control @error('username') is-invalid @enderror">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">نقش <span class="text-danger">*</span></label>
                                    <select wire:model="role_id"
                                        class="form-select @error('role_id') is-invalid @enderror">
                                        <option value="">انتخاب نقش</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ایمیل</label>
                                    <input type="email" wire:model="email"
                                        class="form-control @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">موبایل</label>
                                    <input type="text" wire:model="phone"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if (!$editingId)
                                    <div class="col-md-6">
                                        <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                                        <input type="password" wire:model="password"
                                            class="form-control @error('password') is-invalid @enderror">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">تکرار رمز عبور <span
                                                class="text-danger">*</span></label>
                                        <input type="password" wire:model="password_confirmation"
                                            class="form-control">
                                    </div>
                                @endif

                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="user-is-active">
                                        <label class="form-check-label" for="user-is-active">کاربر فعال باشد</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals"
                                title="انصراف">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $editingId ? 'ذخیره تغییرات' : 'ذخیره' }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال تغییر رمز عبور ============================ --}}
    @if ($showPasswordModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="user-password-modal">
            <div class="modal-dialog modal-dialog-centered">
                <form wire:submit="updatePassword">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">تغییر رمز عبور</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"
                                title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-muted">
                                تغییر رمز عبور کاربر
                                <strong>{{ $passwordUserName }}</strong>
                            </p>

                            <div class="mb-3">
                                <label class="form-label">رمز عبور جدید</label>
                                <input type="password" wire:model="new_password"
                                    class="form-control @error('new_password') is-invalid @enderror">
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">تکرار رمز عبور</label>
                                <input type="password" wire:model="new_password_confirmation" class="form-control">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals"
                                title="انصراف">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="updatePassword">
                                <span wire:loading wire:target="updatePassword"
                                    class="spinner-border spinner-border-sm"></span>
                                ذخیره
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال تایید حذف ============================ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="user-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">حذف کاربر</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"
                            title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">
                            آیا از حذف کاربر
                            <strong>{{ $deletingName }}</strong>
                            اطمینان دارید؟
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
