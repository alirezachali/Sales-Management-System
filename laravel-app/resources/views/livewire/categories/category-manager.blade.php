<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    {{-- نمایش پیغام‌های موفقیت / خطا --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="{{ __('categories.close') }}"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="{{ __('categories.close') }}"></button>
        </div>
    @endif

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('categories.stats.total') }}</div>
                    <div class="h1 mb-0">{{ $totalCategories }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('categories.stats.active') }}</div>
                    <div class="h1 mb-0 text-success">{{ $activeCategories }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('categories.stats.inactive') }}</div>
                    <div class="h1 mb-0 text-danger">{{ $inactiveCategories }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('categories.stats.empty') }}</div>
                    <div class="h1 mb-0 text-warning">{{ $emptyCategories }}</div>
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
                    placeholder="{{ __('categories.search_placeholder') }}">
            </div>
        </div>
    </div>

    {{-- جدول دسته‌بندی‌ها --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-tags-fill text-primary"></i>
                    {{ __('categories.manage_categories') }}
                </h3>
                <small class="text-muted">{{ __('categories.manage_subtitle') }}</small>
            </div>

            @can('categories.create')
            <button type="button" class="btn btn-primary" wire:click="openCreateModal"
                title="{{ __('categories.add_tooltip') }}">
                <i class="bi bi-plus-circle"></i>
                {{ __('categories.add_category') }}
            </button>
            @endcan
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="55">{{ __('categories.table.row') }}</th>
                            <th>{{ __('categories.table.name') }}</th>
                            <th>{{ __('categories.table.description') }}</th>
                            <th width="80">{{ __('categories.table.products_count') }}</th>
                            <th width="200">{{ __('categories.table.created_at') }}</th>
                            <th width="80">{{ __('categories.table.status') }}</th>
                            <th width="100">{{ __('categories.table.actions') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categories as $category)
                            <tr wire:key="category-{{ $category->id }}">
                                {{-- ردیف --}}
                                <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                                {{-- نام دسته‌بندی --}}
                                <td>{{ $category->name }}</td>
                                {{-- توضیحات --}}
                                <td>
                                    @if ($category->description)
                                        {{ $category->description }}
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                                {{-- تعداد کالا --}}
                                <td>
                                    @if ($category->products_count)
                                        <span class="badge bg-info text-dark">
                                            {{ $category->products_count }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-dark">0</span>
                                    @endif
                                </td>
                                {{-- تاریخ ایجاد --}}
                                <td>{{ $category->created_at ? jalaliDate($category->created_at) : '-' }}</td>
                                    {{-- وضعیت --}}
                                <td>
                                    @if ($category->is_active)
                                        <span class="badge bg-success text-dark">{{ __('categories.active') }}</span>
                                    @else
                                        <span class="badge bg-danger text-dark">{{ __('categories.inactive') }}</span>
                                    @endif
                                </td>
                                {{-- عملیات --}}
                                <td>
                                    @can('categories.edit')
                                    <button type="button" class="btn btn-sm btn-warning text-dark"
                                        wire:click="openEditModal({{ $category->id }})" title="{{ __('categories.edit_tooltip') }}">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    @endcan
                                    @can('categories.delete')
                                    <button type="button" class="btn btn-sm btn-danger text-dark"
                                        wire:click="confirmDelete({{ $category->id }})" title="{{ __('categories.delete_tooltip') }}">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 text-secondary d-block mb-3"></i>
                                    {{ __('categories.empty_state') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $categories->links() }}</div>
        </div>
    </div>

    {{-- ============================ مودال افزودن/ویرایش دسته‌بندی ============================ --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="category-form-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? __('categories.edit_modal_title') : __('categories.create_modal_title') }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="{{ __('categories.close') }}"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-8">
                                    <label class="form-label">{{ __('categories.name_label') }} <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="category-is-active">
                                        <label class="form-check-label" for="category-is-active">{{ __('categories.active') }}</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">{{ __('categories.description_label') }}</label>
                                    <textarea wire:model="description" rows="3"
                                        class="form-control @error('description') is-invalid @enderror"></textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals" title="{{ __('categories.cancel') }}">
                                {{ __('categories.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $editingId ? __('categories.save_changes') : __('categories.save_category') }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال تایید حذف ============================ --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="category-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('categories.delete_modal_title') }}</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"
                            title="{{ __('categories.close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">
                            {{ __('categories.delete_confirm', ['name' => $deletingName]) }}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">{{ __('categories.cancel') }}</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled">{{ __('categories.delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
