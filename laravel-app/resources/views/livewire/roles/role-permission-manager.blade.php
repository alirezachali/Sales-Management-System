<div dir="rtl">

    {{-- نمایش پیغام موفقیت --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    <form wire:submit="save">

        {{-- هدر صفحه --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">
                    مجوز های :
                    <span class="badge bg-{{ $role->color ?? 'secondary' }}">
                        {{ $role->name }}
                        <i class="{{ $role->icon }}"></i>
                    </span>
                </h3>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                    <i class="bi bi-check-circle"></i>
                    ذخیره تغییرات
                </button>

                <a href="{{ route('roles.index') }}" class="btn btn-secondary" title="بازگشت به صفحه لیست نقش ها">
                    <i class="bi bi-arrow-right"></i>
                    بازگشت
                </a>
            </div>
        </div>

        <div class="permissions-grid">

            @foreach ($groups as $group)
                <div class="permission-column">
                    <div class="card shadow-sm">

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>
                                <i class="bi {{ $group->icon }}"></i>
                                {{ $group->name }}
                            </strong>

                            @php
                                $groupPermissionIds = $group->permissions->pluck('id')->map(fn ($id) => (string) $id)->all();
                                $groupAllChecked = count($groupPermissionIds) > 0
                                    && count(array_intersect($groupPermissionIds, $selected)) === count($groupPermissionIds);
                            @endphp

                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox"
                                    id="group-{{ $group->id }}" wire:click="toggleGroup({{ $group->id }})"
                                    @checked($groupAllChecked)>
                                <label class="form-check-label small text-muted" for="group-{{ $group->id }}">
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

        <hr class="h-r">

        <div class="text-end">
            <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                <i class="bi bi-check-circle"></i>
                ذخیره تغییرات
            </button>
        </div>

    </form>
</div>
