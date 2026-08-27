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
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد نقش ها</div>
                    <div class="h1 mb-0">
                        {{-- {{ $totalCategories }} --}}5
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">نقش های فعال</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $activeCategories }} --}}5
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">نقش های غیرفعال</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $inactiveCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">نقش های بدون کاربر</div>
                    <div class="h1 mb-0 text-warning">
                        {{-- {{ $emptyCategories }} --}}2
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- کارت جستجو --}}
    <div class="card glass-card mb-4 border-3">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" wire:model.live.debounce.400ms="search"
                    placeholder="جستجو بر اساس نام نقش یا شناسه">
            </div>
        </div>
    </div>


    {{-- هدر صفحه --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-shield-lock-fill text-primary"></i>
                    مدیریت نقش‌ها
                </h3>
                <small class="text-muted">مدیریت نقش‌های کاربران سیستم و مجوزهای دسترسی به هر بخش</small>
            </div>
            <div class="d-flex gap-3">

                <a href="{{ route('users.index') }}">
                    <button class="btn btn-info text-dark" title="بازگشت به لیست کاربران">
                        <i class="bi bi-arrow-right"></i>
                        بازگشت
                    </button>
                </a>

                <button class="btn btn-primary" wire:click="openCreateModal"
                    title="برای افزودن نقش جدید به سیستم کلیک کنید">
                    <i class="bi bi-plus-circle"></i>
                    افزودن نقش
                </button>

            </div>
        </div>

        <div class="card shadow-sm" wire:loading.class="opacity-50">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50">ردیف</th>
                                <th>نام نقش</th>
                                <th width="150">شناسه</th>
                                <th>توضیحات</th>
                                <th width="90">تعداد کاربران</th>
                                <th width="130">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr wire:key="role-{{ $role->id }}">
                                    <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                                    <td>{{ $role->display_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $role->color ?? 'secondary' }} text-dark">
                                            {{ $role->name }}
                                            <i class="{{ $role->icon }}"></i>
                                        </span>
                                    </td>
                                    <td>{{ $role->description }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{ $role->users_count }}</span>
                                    </td>
                                    <td>
                                        {{-- دکمه ویرایش یک نقش --}}
                                        <button type="button" class="btn btn-sm btn-warning text-dark"
                                            wire:click="openEditModal({{ $role->id }})"
                                            title="برای ویرایش این نقش کلیک کنید">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        {{-- دکمه حذف یک نقش --}}
                                        <button type="button" class="btn btn-sm btn-danger text-dark"
                                            wire:click="confirmDelete({{ $role->id }})"
                                            title="برای حذف این نقش کلیک کنید">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                        {{-- دکمه ویرایش مجوزهای یک نقش --}}
                                        <a href="{{ route('roles.permissions', $role) }}" class="btn btn-sm btn-info text-dark"
                                            title="برای ویرایش مجوز های این نقش کلیک کنید">
                                            <i class="bi bi-shield-lock-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        هیچ نقشی ثبت نشده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">{{ $roles->links() }}</div>
    </div>

    {{-- ============================ مودال افزودن/ویرایش نقش ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="role-form-modal">
            <div class="modal-dialog modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? 'ویرایش نقش' : 'نقش جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-12">
                                    <label class="form-label">نام نقش <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="display_name"
                                        class="form-control @error('display_name') is-invalid @enderror">
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">
                                        شناسه (عنوان نقش به انگلیسی و حروف کوچک) <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">رنگ نشان</label>
                                    <select wire:model="color" class="form-select">
                                        <option value="primary">آبی</option>
                                        <option value="secondary">خاکستری</option>
                                        <option value="success">سبز</option>
                                        <option value="info">آبی روشن</option>
                                        <option value="warning">زرد</option>
                                        <option value="danger">قرمز</option>
                                        <option value="dark">مشکی</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">آیکن (Bootstrap Icons)</label>
                                    <input type="text" wire:model="icon" class="form-control"
                                        placeholder="bi-shield-lock">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">توضیحات</label>
                                    <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="3"></textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

    {{-- ============================ مودال تایید حذف ============================ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="role-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">حذف نقش</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"
                            title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">
                            آیا از حذف نقش
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
