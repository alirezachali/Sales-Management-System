<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="stat-icon" style="--icon-bg: rgba(59,130,246,.15); --icon-color:#3b82f6">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <div class="subheader">تعداد انبارها</div>
                    <div class="h2 mb-0 fw-bold">{{ $warehouses->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="stat-icon" style="--icon-bg: rgba(34,197,94,.15); --icon-color:#22c55e">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="subheader">انبارهای فعال</div>
                    <div class="h2 mb-0 fw-bold">{{ $activeCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="stat-icon" style="--icon-bg: rgba(168,85,247,.15); --icon-color:#a855f7">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="subheader">جمع موجودی کل</div>
                    <div class="h2 mb-0 fw-bold">{{ number_format((float) $totalStockUnits, 1) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 d-flex align-items-end">
            @can('warehouses.create')
                <button class="btn btn-primary w-100 glow-btn" wire:click="openCreateModal">
                    <i class="bi bi-plus-circle"></i> انبار جدید
                </button>
            @endcan
        </div>
    </div>

    <div class="card glass-card mb-4 border-0">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" wire:model.live.debounce.400ms="search"
                    placeholder="جستجو در نام یا کد انبار...">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-buildings text-primary"></i> مدیریت انبارها</h3>
                <small class="text-muted">لیست انبارهای سازمان و مشاهده چیدمان کالا در هر انبار</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>نام انبار</th>
                            <th>کد</th>
                            <th>مسئول</th>
                            <th>تماس</th>
                            <th>قلم موجودی</th>
                            <th width="90">وضعیت</th>
                            <th width="220" class="text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($warehouses as $warehouse)
                            <tr wire:key="wh-{{ $warehouse->id }}">
                                <td>{{ $loop->iteration + ($warehouses->currentPage() - 1) * $warehouses->perPage() }}</td>
                                <td class="fw-semibold">
                                    {{ $warehouse->name }}
                                    @if ($warehouse->is_default)
                                        <span class="badge bg-warning text-dark ms-1">پیش‌فرض</span>
                                    @endif
                                </td>
                                <td><code>{{ $warehouse->code }}</code></td>
                                <td>{{ $warehouse->manager_name ?: '-' }}</td>
                                <td>{{ $warehouse->phone ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info-emphasis">
                                        {{ number_format((float) $warehouse->stocks_sum_quantity, 1) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($warehouse->is_active)
                                        <span class="badge bg-success-subtle text-success-emphasis">فعال</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">غیرفعال</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                        wire:click="viewInventory({{ $warehouse->id }})" title="مشاهده چیدمان کالا">
                                        <i class="bi bi-boxes"></i> چیدمان
                                    </button>
                                    @can('warehouses.edit')
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            wire:click="openEditModal({{ $warehouse->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endcan
                                    @can('warehouses.delete')
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="confirmDelete({{ $warehouse->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">هنوز انباری ثبت نشده است.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $warehouses->links() }}</div>
        </div>
    </div>

    {{-- چیدمان کالا در انبار --}}
    @if ($viewing)
        <div class="card border-0 shadow-sm mt-4" wire:key="inventory-{{ $viewing->id }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-1"><i class="bi bi-boxes text-info"></i> چیدمان کالا — {{ $viewing->name }}</h3>
                    <small class="text-muted">فقط محصولاتی که در این انبار رکورد دارند نمایش داده می‌شوند.</small>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <input type="text" class="form-control form-control-sm" style="width:220px"
                        wire:model.live.debounce.400ms="viewSearch" placeholder="جستجوی کالا...">
                    <button class="btn btn-sm btn-secondary" wire:click="closeInventory">بستن</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="60">#</th>
                                <th>کالا</th>
                                <th>بارکد</th>
                                <th>موجودی در این انبار</th>
                                <th>موجودی کل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventory as $row)
                                <tr wire:key="inv-{{ $row->id }}">
                                    <td>{{ $loop->iteration + ($inventory->currentPage() - 1) * $inventory->perPage() }}</td>
                                    <td class="fw-semibold">{{ $row->product?->name ?? '—' }}</td>
                                    <td><code>{{ $row->product?->barcode }}</code></td>
                                    <td>
                                        <span class="badge {{ $row->quantity > 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                            {{ number_format((float) $row->quantity, 1) }} {{ $row->product?->unit }}
                                        </span>
                                    </td>
                                    <td>{{ number_format((float) ($row->product?->stock ?? 0), 1) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">کالایی در این انبار ثبت نشده است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">{{ $inventory->links() }}</div>
            </div>
        </div>
    @endif

    {{-- مودال فرم --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="wh-form-modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $warehouseId ? 'ویرایش انبار' : 'انبار جدید' }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">نام انبار <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">کد انبار <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="WH-02">
                                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">مسئول انبار</label>
                                    <input type="text" class="form-control" wire:model="manager_name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">تلفن</label>
                                    <input type="text" class="form-control" wire:model="phone">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">آدرس</label>
                                    <input type="text" class="form-control" wire:model="address">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">یادداشت</label>
                                    <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="wh_default" wire:model="is_default">
                                        <label class="form-check-label" for="wh_default">انبار پیش‌فرض</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="wh_active" wire:model="is_active">
                                        <label class="form-check-label" for="wh_active">فعال</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">ذخیره</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال حذف --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="wh-delete-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">تأیید حذف انبار</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">آیا از حذف این انبار مطمئن هستید؟ این عمل قابل بازگشت نیست.</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
