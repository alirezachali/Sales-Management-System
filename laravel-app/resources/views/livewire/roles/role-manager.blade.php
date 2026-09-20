<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')


    {{-- ================== کارت‌های آماری ================== --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تـــعداد نـــقش هـــا</div>
                    <div class="h1 mb-0">
                        {{-- {{ $totalCategories }} --}}5
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">نـــقش هـــای فـــعال</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $activeCategories }} --}}5
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">نـــقش هـــای غـــیرفـــعال</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $inactiveCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">نـــقش هـــای بـــدون کـــاربر</div>
                    <div class="h1 mb-0 text-warning">
                        {{-- {{ $emptyCategories }} --}}2
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================== کارت جستجو ================== --}}
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

    {{-- ============================== جدول لیست نقش ها ============================== --}}
    <div class="card shadow-sm" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-shield-lock-fill text-fuchsia"></i>
                    مـــــدیریت نـــــقش‌هـــــا
                </h3>
                <small class="text-muted">مدیریت نقش‌های کاربران سیستم و مجوزهای دسترسی به هر بخش</small>
            </div>
            <div class="d-flex">

                @can('roles.create')
                    <button class="btn btn-primary glow-btn" wire:click="openCreateModal"
                        title="برای افزودن نقش جدید به سیستم کلیک کنید">
                        <i class="bi bi-plus-circle"></i>
                        افـــزودن نـــقش
                    </button>
                @endcan

            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60">ردیف</th>
                            <th>نام نقش</th>
                            <th width="160">شناسه</th>
                            <th>توضیحات</th>
                            <th width="100">تعداد کاربران</th>
                            <th width="160">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr wire:key="role-{{ $role->id }}">
                                <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                                <td>{{ $role->display_name }}</td>
                                <td>
                                    <span class="badge bg-{{ $role->color ?? 'secondary' }}-subtle text-{{ $role->color ?? 'secondary' }}-emphasis">
                                        {{ $role->name }}
                                        <i class="{{ $role->icon }}"></i>
                                    </span>
                                </td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis">{{ $role->users_count }}</span>
                                </td>
                                <td>
                                    {{-- دکمه ویرایش یک نقش --}}
                                    @can('roles.edit')
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                            wire:click="openEditModal({{ $role->id }})"
                                            title="برای ویرایش این نقش کلیک کنید">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                    @endcan
                                    {{-- دکمه حذف یک نقش --}}
                                    @can('roles.delete')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="confirmDelete({{ $role->id }})"
                                            title="برای حذف این نقش کلیک کنید">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    @endcan
                                    {{-- دکمه ویرایش مجوزهای یک نقش --}}
                                    @can('roles.permissions')
                                        <a href="{{ route('roles.permissions', $role) }}"
                                            class="btn btn-sm btn-outline-info"
                                            title="برای ویرایش مجوز های این نقش کلیک کنید">
                                            <i class="bi bi-shield-lock-fill"></i>
                                        </a>
                                    @endcan
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

        <div class="card-footer">
            {{ $roles->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- ================================== مودال افزودن/ویرایش نقش ================================== --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
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

    {{-- ======================================== مودال تایید حذف ======================================== --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
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
