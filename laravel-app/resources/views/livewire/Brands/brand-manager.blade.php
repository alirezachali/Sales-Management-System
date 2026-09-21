<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

    {{-- =================== کارت‌های آماری =================== --}}
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


    {{-- =================== کارت جستجو =================== --}}
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


    {{-- ================================ جدول لیست برندها ================================ --}}
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
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="55">{{ __('brands.table.row') }}</th>
                            <th width="80"></th>
                            <th>{{ __('brands.table.name') }}</th>
                            <th width="90">{{ __('brands.table.products') }}</th>
                            <th width="120">{{ __('brands.table.suppliers') }}</th>
                            <th width="80">{{ __('brands.table.status') }}</th>
                            <th width="130">{{ __('brands.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($brands as $brand)
                            <tr wire:key="brand-{{ $brand->id }}">
                                <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                                <td>
                                    @if ($brand->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($brand->logo))
                                        <img src="{{ asset('storage/' . $brand->logo) }}" style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;" alt="{{ $brand->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                        <div class="d-none align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-25" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-secondary bg-opacity-25" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $brand->name }}</td>
                                <td>
                                    {{ $brand->products_count }}
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
                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                            wire:click="openEditModal({{ $brand->id }})"
                                            title="{{ __('brands.edit_tooltip') }}">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info"
                                            wire:click="openDetailsModal({{ $brand->id }})" title="جزئیات برند">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    @endcan
                                    @can('brands.delete')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="confirmDelete({{ $brand->id }})"
                                            title="{{ __('brands.delete_tooltip') }}">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
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
            <div class="card-footer">
                {{ $brands->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- =================================== مودال ساخت / ویرایش برند =================================== --}}
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
                                    <label class="form-label">{{ __('brands.name_label') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('brands.logo_label') }}</label>
                                    <input type="file" class="form-control @error('logoFile') is-invalid @enderror"
                                        wire:model="logoFile" accept=".svg,image/svg+xml,.png,image/png,.jpg,.jpeg">
                                    @error('logoFile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('brands.logo_helper') }}</small>
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
                                    <label class="form-check-label"
                                        for="brand_is_active">{{ __('brands.active') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeModals">{{ __('brands.cancel') }}</button>
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

    {{-- =================================== مودال تایید حذف =================================== --}}
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
                        <button type="button" class="btn btn-secondary"
                            wire:click="closeModals">{{ __('brands.cancel') }}</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled">
                            {{ __('brands.table.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{--======================== مودال جزئیات برند =========================--}}
    @if ($showDetailsModal && $detailBrand)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="brand-details-modal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content overflow-hidden">
 
                    {{-- هدر مودال --}}
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm bg-primary-lt">
                                <i class="bi bi-bing"></i>
                            </span>
                            <h5 class="modal-title fw-bold mb-0">
                                جـــزئـــیـــات بـــرنـــد {{ $detailBrand->name }}
                            </h5>
                        </div>
                        <button type="button" class="btn-close" wire:click="$set('showDetailsModal', false)" title="بستن">
                        </button>
                    </div>
 
                    <div class="modal-body pt-3 d-flex flex-column gap-3">
 
                        {{-- ============ کارت ۱: معرفی برند ============ --}}
                        <div class="card border-3 mb-0 shadow-sm">
                            <div class="row g-0">
                                <div class="col-auto d-flex align-items-center justify-content-center"
                                    style="background: linear-gradient(135deg, var(--tblr-primary-rgb, 66,99,235) 0%, rgba(66,99,235,.06) 100%); min-width: 160px; padding: 1.5rem;">
                                    @if ($detailBrand->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($detailBrand->logo))
                                        <img src="{{ asset('storage/' . $detailBrand->logo) }}"
                                            alt="{{ $detailBrand->name }}"
                                            style="width: 110px; height: 110px; border-radius: 18px; object-fit: cover; box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); background:#fff; padding:6px;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center rounded-4"
                                            style="width: 110px; height: 110px; background: rgba(255,255,255,.6); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);">
                                            <i class="bi bi-image text-secondary" style="font-size: 2.5rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="card-body py-4">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <h3 class="fw-bold mb-0">
                                                <i class="bi bi-award text-primary me-1"></i>
                                                {{ $detailBrand->name }}
                                            </h3>
                                            @if ($detailBrand->is_active)
                                                <span class="badge bg-success text-dark"><i class="bi bi-check-circle me-1"></i>فــعــال</span>
                                            @else
                                                <span class="badge bg-secondary text-dark"><i class="bi bi-x-circle me-1"></i>غــیــرفــعــال</span>
                                            @endif
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex flex-wrap gap-3 mb-3">
                                            <div class="text-muted d-flex align-items-center gap-1">
                                                <i class="bi bi-box-seam"></i>
                                                <span>{{ $detailBrand->products_count ?? $detailBrand->products->count() }} مـحـصـول</span>
                                            </div>
                                            <div class="text-muted d-flex align-items-center gap-1">
                                                <i class="bi bi-truck"></i>
                                                <span>{{ $detailBrand->suppliers_count ?? $detailBrand->suppliers->count() }} تـامـیـن‌کـنـنـده</span>
                                            </div>
                                        </div>
                                        <div class="p-3 rounded-3" style="background: rgba(66,99,235,.06); border-right: 4px solid var(--tblr-primary);">
                                            <i class="bi bi-chat-left-quote text-primary me-1"></i>
                                            <span class="text-muted">{{ $detailBrand->description ?: 'توضیحاتی برای این برند ثبت نشده است.' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
 
                        {{-- ============ کارت ۲: تامین‌کنندگان برند ============ --}}
                        <div class="card border-3 mb-0 shadow-sm">
                            <div class="card-header bg-primary-lt py-2">
                                <h4 class="card-title mb-0 fw-bold">
                                    <i class="bi bi-truck text-primary me-2"></i>
                                    تــامــیــن‌کــنــنــدگــان ایــن بــرنــد
                                </h4>&emsp;
                                <span class="badge bg-primary">
                                    {{ $detailBrand->suppliers->count() }}
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">ردیف</th>
                                                <th>نام تامین‌کننده</th>
                                                <th>نام شرکت</th>
                                                <th>موبایل</th>
                                                <th>شهر</th>
                                                <th width="70">نوع</th>
                                                <th width="80">وضعیت</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($detailBrand->suppliers as $supplier)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="fw-semibold">
                                                        <span class="avatar avatar-xs me-2" style="background: rgba(66,99,235,.08);">
                                                            <i class="bi bi-person text-primary"></i>
                                                        </span>
                                                        {{ $supplier->name }}
                                                    </td>
                                                    <td>{{ $supplier->company_name ?: '—' }}</td>
                                                    <td dir="ltr" class="text-end">{{ $supplier->mobile ?: '—' }}</td>
                                                    <td>{{ $supplier->city ?: '—' }}</td>
                                                    <td>
                                                        <span class="badge {{ $supplier->type === 'company' ? 'bg-purple-subtle text-purple' : 'bg-azure-subtle text-azure' }}">
                                                            {{ $supplier->type === 'company' ? 'حقوقی' : 'حقیقی' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($supplier->is_active)
                                                            <span class="badge bg-success-subtle text-success-emphasis">فعال</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">غیرفعال</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4 text-muted">
                                                        <i class="bi bi-truck me-1"></i>هیچ تامین‌کننده‌ای برای این برند ثبت نشده است.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
 
                        {{-- ============ کارت ۳: محصولات برند ============ --}}
                        <div class="card border-3 mb-0 shadow-sm">
                            <div class="card-header bg-primary-lt py-2">
                                <h4 class="card-title mb-0 fw-bold">
                                    <i class="bi bi-box-seam text-primary me-2"></i>
                                    مــحــصــولات ایــن بــرنــد
                                </h4>&emsp;
                                <span class="badge bg-primary">
                                    {{ $detailBrand->products->count() }}
                                </span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">ردیف</th>
                                                <th width="140">بارکد</th>
                                                <th>نام محصول</th>
                                                <th>دسته‌بندی</th>
                                                <th>قیمت فروش</th>
                                                <th width="100">موجودی</th>
                                                <th width="80">وضعیت</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($detailBrand->products as $product)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td dir="ltr" class="text-end">
                                                        <span class="badge bg-secondary-subtle text-secondary">{{ $product->barcode ?: '—' }}</span>
                                                    </td>
                                                    <td class="fw-semibold">
                                                        <i class="bi bi-upc-scan text-muted me-1"></i>
                                                        {{ $product->name }}
                                                    </td>
                                                    <td>{{ $product->category?->name ?: '—' }}</td>
                                                    <td class="text-success fw-semibold">{{ number_format($product->sell_price) }}</td>
                                                    <td>
                                                        <span class="badge {{ $product->stock > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                                            {{ $product->formatted_stock }} {{ $product->unit }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($product->is_active)
                                                            <span class="badge bg-success-subtle text-success-emphasis">فعال</span>
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">غیرفعال</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-4 text-muted">
                                                        <i class="bi bi-box-seam me-1"></i>هیچ محصولی برای این برند ثبت نشده است.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
 
                    </div>
 
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showDetailsModal', false)" title="بستن">
                            <i class="bi bi-x-lg me-1"></i>بستن
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
