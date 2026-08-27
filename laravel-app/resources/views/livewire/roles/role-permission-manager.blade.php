<div dir="rtl">

    {{-- نمایش پیغام‌های موفقیت / خطا --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

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
                    <div class="subheader">تعداد کل مجوزها</div>
                    <div class="h1 mb-0">
                        {{-- {{ $totalCategories }} --}}97
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مجوزهای فعال</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $activeCategories }} --}}97
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مجوزهای غیرفعال</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $inactiveCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد گروه های مجوز</div>
                    <div class="h1 mb-0 text-warning">
                        {{-- {{ $emptyCategories }} --}}23
                    </div>
                </div>
            </div>
        </div>
    </div>


    <form wire:submit="save">

        {{-- هدر صفحه --}}
        <div class="card shadow-sm mb-4 border-3" wire:loading.class="opacity-50">
            <div class="card-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">
                        دسترسی های رول :
                        <span class="badge bg-{{ $role->color ?? 'secondary' }} text-dark">
                            {{ $role->name }}
                            <i class="{{ $role->icon }}"></i>
                        </span>
                    </h3>
                    <small class="text-muted">مدیریت مجوزها و سطح دسترسی به بخش های سیستم توسط هر نقش</small>
                </div>

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                        <i class="bi bi-check-circle"></i>
                        ذخیره تغییرات
                    </button>

                    <a href="{{ route('roles.index') }}" class="btn btn-info" title="بازگشت به صفحه لیست نقش ها">
                        <i class="bi bi-arrow-right"></i>
                        بازگشت
                    </a>
                </div>

            </div>
        </div>

        <div class="permissions-grid">

            @foreach ($groups as $group)
                <div class="permission-column">
                    <div class="card shadow-sm border-3">

                        <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-dark">
                            <strong>
                                <i class="bi {{ $group->icon }}"></i>
                                {{ $group->name }}
                            </strong>

                            @php
                                $groupPermissionIds = $group->permissions
                                    ->pluck('id')
                                    ->map(fn($id) => (string) $id)
                                    ->all();
                                $groupAllChecked =
                                    count($groupPermissionIds) > 0 &&
                                    count(array_intersect($groupPermissionIds, $selected)) ===
                                        count($groupPermissionIds);
                            @endphp

                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="group-{{ $group->id }}"
                                    wire:click="toggleGroup({{ $group->id }})" @checked($groupAllChecked)>
                                <label class="form-check-label small" for="group-{{ $group->id }}">
                                    همه
                                </label>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="permissions-list">
                                @foreach ($group->permissions as $permission)
                                    <div class="form-check permission-item">
                                        <input class="form-check-input" type="checkbox" wire:model="selected"
                                            value="{{ $permission->id }}" id="permission{{ $permission->id }}">
                                        <label class="form-check-label" for="permission{{ $permission->id }}">
                                            {{ $permission->display_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach

        </div>

    </form>
</div>
