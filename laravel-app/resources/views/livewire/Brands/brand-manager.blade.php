<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

{{--=================== نمایش پیغام‌های موفقیت ===================--}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="{{ __('brands.close') }}"></button>
        </div>
    @endif

{{--=================== نمایش پیغام‌های خطا ===================--}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="{{ __('brands.close') }}"></button>
        </div>
    @endif

{{--=================== کارت‌های آماری ===================--}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('brands.stats.total') }}</div>
                    <div class="h1 mb-0">
                        {{ $brands->total() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('brands.stats.active') }}</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $activeCategories }} --}}1
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('brands.stats.inactive') }}</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $inactiveCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">{{ __('brands.stats.not_supplier') }}</div>
                    <div class="h1 mb-0 text-warning">
                        {{-- {{ $emptyCategories }} --}}0
                    </div>
                </div>
            </div>
        </div>
    </div>


{{--=================== کارت جستجو ===================--}}
    <div class="card glass-card mb-4 border-3">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" wire:model.live.debounce.400ms="search"
                            placeholder="{{ __('brands.search_placeholder') }}">
                    </div>
                </div>

            </div>
        </div>
    </div>


{{--================================ جدول لیست برندها ================================--}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-bing text-primary"></i>
                    {{ __('brands.manage_brands') }}
                </h3>
                <small class="text-muted">{{ __('brands.manage_subtitle') }}</small>
            </div>

            @can('brands.create')
            <button class="btn btn-primary" wire:click="openCreateModal" title="{{ __('brands.add_tooltip') }}">
                <i class="bi bi-plus-circle"></i>
                {{ __('brands.add_brand') }}
            </button>
            @endcan
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="55">{{ __('brands.table.row') }}</th>
                            <th>{{ __('brands.table.name') }}</th>
                            <th>{{ __('brands.table.description') }}</th>
                            <th >{{ __('brands.table.suppliers') }}</th>
                            <th width="80">{{ __('brands.table.status') }}</th>
                            <th width="170">{{ __('brands.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr wire:key="brand-{{ $brand->id }}">
                                <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                                <td class="fw-semibold">{{ $brand->name }}</td>
                                <td class="text-muted small">
                                    {{ $brand->description ? \Illuminate\Support\Str::limit($brand->description, 50) : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis">
                                        {{ $brand->suppliers_count }} {{ __('brands.table.suppliers') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($brand->is_active)
                                        <span class="badge bg-success text-dark">{{ __('brands.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary text-dark">{{ __('brands.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @can('brands.edit')
                                    <button type="button" class="btn btn-sm btn-primary text-dark"
                                        wire:click="openEditModal({{ $brand->id }})" title="{{ __('brands.edit_tooltip') }}">
                                        <i class="bi bi-pencil-fill"></i>
                                        {{ __('brands.table.edit') }}
                                    </button>
                                    @endcan
                                    @can('brands.delete')
                                    <button type="button" class="btn btn-sm btn-danger text-dark"
                                        wire:click="confirmDelete({{ $brand->id }})" title="{{ __('brands.delete_tooltip') }}">
                                        <i class="bi bi-trash-fill"></i>
                                        {{ __('brands.table.delete') }}
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    @if ($search)
                                        نتیجه‌ای برای «{{ $search }}» پیدا نشد.
                                    @else
                                        {{ __('brands.empty_state') }}
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $brands->links() }}</div>
        </div>
    </div>

{{--=================================== مودال ساخت / ویرایش برند ===================================--}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="brand-form-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $brandId ? __('brands.edit_modal_title') : __('brands.create_modal_title') }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('brands.name_label') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('brands.logo_label') }}</label>
                                    <input type="text" class="form-control" wire:model="logo"
                                        placeholder="مثلاً: logos/brand.png">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('brands.description_label') }}</label>
                                    <textarea class="form-control" rows="2" wire:model="description"></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">{{ __('brands.suppliers_label') }}</label>
                                    <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                                        @forelse ($allSuppliers as $supplier)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="supplier_{{ $supplier->id }}" wire:model="selectedSuppliers"
                                                    value="{{ $supplier->id }}">
                                                <label class="form-check-label" for="supplier_{{ $supplier->id }}">
                                                    {{ $supplier->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0">
                                                {{ __('brands.supplier_empty') }}
                                            </p>
                                        @endforelse
                                    </div>
                                    <small class="text-muted">{{ __('brands.supplier_roll') }}</small>
                                </div>

                                <div class="col-12 form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="brand_is_active"
                                        wire:model="is_active">
                                    <label class="form-check-label" for="brand_is_active">{{ __('brands.active') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">{{ __('brands.cancel') }}</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $brandId ? __('brands.save_changes') : __('brands.save_brand') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

{{--=================================== مودال تایید حذف ===================================--}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="brand-delete-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-dark">
                        <h5 class="modal-title">{{ __('brands.delete_modal_title') }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        {{ __('brands.delete_confirm') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">{{ __('brands.cancel') }}</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled">
                            {{ __('brands.table.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
