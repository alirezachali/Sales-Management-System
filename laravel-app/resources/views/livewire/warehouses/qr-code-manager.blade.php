<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">
    @include('partials.flash-messages')

    {{-- کارت‌های آمار --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card shadow-sm border-0 text-center py-3">
                <div class="fs-3 fw-bold">{{ number_format($stats['total']) }}</div>
                <div class="text-muted">کل QR Code ها</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card shadow-sm border-0 text-center py-3 text-danger">
                <div class="fs-3 fw-bold">{{ number_format($stats['expired']) }}</div>
                <div class="text-muted">منقضی شده</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card shadow-sm border-0 text-center py-3 text-warning">
                <div class="fs-3 fw-bold">{{ number_format($stats['near']) }}</div>
                <div class="text-muted">نزدیک انقضا (۳۰ روز)</div>
            </div>
        </div>
    </div>

    {{-- نوار ابزار --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-qr-code me-1"></i> چیدمان و مدیریت محصولات انبار</h5>
            <button class="btn btn-primary btn-sm" wire:click="openCreateModal">
                <i class="bi bi-plus-lg"></i> ساخت QR Code جدید
            </button>
        </div>
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="جستجو بر اساس شناسه QR، بخش، قفسه یا نام محصول...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterWarehouse" class="form-select">
                        <option value="">همه انبارها</option>
                        @foreach ($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterExpiration" class="form-select">
                        <option value="all">همه وضعیت‌ها</option>
                        <option value="expired">منقضی شده</option>
                        <option value="near">نزدیک انقضا</option>
                        <option value="no_date">بدون تاریخ انقضا</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>شناسه QR</th>
                        <th>انبار</th>
                        <th>نوع واحد</th>
                        <th>تعداد</th>
                        <th>محل قرارگیری</th>
                        <th>تاریخ انقضا</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($qrcodes as $qr)
                        <tr>
                            <td>
                                <code class="text-primary">{{ $qr->qr_identifier }}</code>
                                <div class="small text-muted">{{ optional($qr->product)->name ?? '—' }}</div>
                            </td>
                            <td>{{ $qr->warehouse->name ?? '—' }}</td>
                            <td>{{ $unitTypes[$qr->unit_type] ?? $qr->unit_type }}</td>
                            <td>{{ number_format($qr->quantity_in_unit) }}</td>
                            <td>
                                <span class="small">{{ $qr->full_location }}</span>
                            </td>
                            <td>
                                @if ($qr->is_expired)
                                    <span class="badge bg-danger">منقضی شده</span>
                                @elseif ($qr->is_near_expiration)
                                    <span class="badge bg-warning text-dark">{{ $qr->expiration_status }}</span>
                                @elseif ($qr->expiration_date)
                                    <span class="small">{{ $qr->expiration_date->format('Y/m/d') }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" wire:click="viewQr({{ $qr->id }})" title="مشاهده QR">
                                        <i class="bi bi-qr-code"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" wire:click="openEditModal({{ $qr->id }})" title="ویرایش">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" wire:click="confirmDelete({{ $qr->id }})" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-qr-code"></i> هنوز QR Code ای ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($qrcodes->hasPages())
            <div class="card-footer bg-white">
                {{ $qrcodes->links() }}
            </div>
        @endif
    </div>

    {{-- مودال فرم --}}
    @if ($showFormModal)
    <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5); overflow-y:auto;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="save">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-qr-code"></i>
                            {{ $editingId ? 'ویرایش QR Code' : 'ساخت QR Code جدید' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">انبار <span class="text-danger">*</span></label>
                                <select wire:model="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                                    <option value="">انتخاب کنید...</option>
                                    @foreach ($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }} ({{ $w->code }})</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">نوع واحد</label>
                                <select wire:model="unit_type" class="form-select">
                                    @foreach ($unitTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">تعداد محصول در واحد</label>
                                <input type="number" min="1" wire:model="quantity_in_unit"
                                    class="form-control @error('quantity_in_unit') is-invalid @enderror">
                                @error('quantity_in_unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">محصول اصلی (اختیاری)</label>
                                <select wire:model="product_id" class="form-select">
                                    <option value="">بدون محصول</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}@if($p->barcode) ({{ $p->barcode }})@endif</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاریخ ورود به انبار</label>
                                <input type="text" wire:model="entry_date_jalali" data-jdp
                                    autocomplete="off" inputmode="numeric"
                                    class="form-control" placeholder="1405/06/11">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاریخ تولید</label>
                                <input type="text" wire:model="production_date_jalali" data-jdp
                                    autocomplete="off" inputmode="numeric"
                                    class="form-control" placeholder="1405/05/10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاریخ انقضا</label>
                                <input type="text" wire:model="expiration_date_jalali" data-jdp
                                    autocomplete="off" inputmode="numeric"
                                    class="form-control" placeholder="1406/06/11">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">انجام‌دهنده عملیات</label>
                                <select wire:model="performed_by" class="form-select @error('performed_by') is-invalid @enderror">
                                    <option value="">کاربر جاری</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                                @error('performed_by') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <hr>
                                <h6 class="text-primary"><i class="bi bi-geo-alt"></i> محل قرارگیری دقیق در انبار</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">بخش (مثلاً سردخانه زیر صفر)</label>
                                <input type="text" wire:model="location_section" class="form-control" placeholder="سردخانه زیر صفر">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">قفسه</label>
                                <input type="text" wire:model="location_shelf" class="form-control" placeholder="B">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">طبقه / ردیف</label>
                                <input type="text" wire:model="location_row" class="form-control" placeholder="2">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">جایگاه</label>
                                <input type="text" wire:model="location_position" class="form-control" placeholder="10">
                            </div>

                            <div class="col-12">
                                <hr>
                                <h6 class="text-primary"><i class="bi bi-upc-scan"></i> بارکد محصولات درون این واحد</h6>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" wire:model="barcodeInput" wire:keydown.enter.prevent="addBarcode"
                                        class="form-control" placeholder="اسکن یا وارد کردن بارکد...">
                                    <button type="button" class="btn btn-outline-primary" wire:click="addBarcode">
                                        <i class="bi bi-plus"></i> افزودن
                                    </button>
                                </div>
                            </div>
                            <div class="col-12">
                                @forelse ($productsBarcode as $i => $bc)
                                    <span class="badge bg-light text-dark border me-2 mb-1 py-2 px-3">
                                        {{ $bc }}
                                        <a href="#" wire:click.prevent="removeBarcode({{ $i }})" class="text-danger ms-1">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </span>
                                @empty
                                    <span class="text-muted small">هیچ بارکدی اضافه نشده است.</span>
                                @endforelse
                            </div>

                            <div class="col-12">
                                <label class="form-label">توضیحات</label>
                                <textarea wire:model="notes" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> {{ $editingId ? 'ذخیره تغییرات' : 'ساخت QR Code' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- مودال نمایش QR --}}
    @if ($showQrModal && $viewingQr)
    <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-qr-code"></i> QR Code</h5>
                    <button type="button" class="btn-close" wire:click="closeModals"></button>
                </div>
                <div class="modal-body text-center">
                    @if ($viewingQr->qr_image_path && file_exists(storage_path('app/public/' . $viewingQr->qr_image_path)))
                        <img src="{{ asset('storage/' . $viewingQr->qr_image_path) }}" alt="QR Code"
                            class="img-fluid mb-3" style="max-width:220px;">
                    @else
                        <div class="text-muted py-4">QR هنوز تولید نشده است.</div>
                    @endif
                    <div class="small text-muted mb-3">{{ $viewingQr->qr_identifier }}</div>
                    <div class="text-start small bg-light p-3 rounded">
                        <div><strong>انبار:</strong> {{ $viewingQr->warehouse->name ?? '—' }}</div>
                        <div><strong>نوع واحد:</strong> {{ $unitTypes[$viewingQr->unit_type] ?? $viewingQr->unit_type }}</div>
                        <div><strong>تعداد:</strong> {{ number_format($viewingQr->quantity_in_unit) }}</div>
                        <div><strong>محل:</strong> {{ $viewingQr->full_location }}</div>
                        @if ($viewingQr->product)
                            <div><strong>محصول:</strong> {{ $viewingQr->product->name }}</div>
                        @endif
                        @if ($viewingQr->expiration_date)
                            <div><strong>انقضا:</strong> {{ $viewingQr->expiration_date->format('Y/m/d') }}</div>
                        @endif
                        @if ($viewingQr->entry_date)
                            <div><strong>ورود به انبار:</strong> {{ $viewingQr->entry_date->format('Y/m/d') }}</div>
                        @endif
                        @if ($viewingQr->performer)
                            <div><strong>انجام‌دهنده:</strong> {{ $viewingQr->performer->name }}</div>
                        @elseif ($viewingQr->creator)
                            <div><strong>انجام‌دهنده:</strong> {{ $viewingQr->creator->name }}</div>
                        @endif
                        @if ($viewingQr->products_barcode)
                            <div><strong>بارکدها:</strong>
                                @foreach ($viewingQr->products_barcode as $bc)
                                    <span class="badge bg-light text-dark border me-1">{{ $bc }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ asset('storage/' . $viewingQr->qr_image_path) }}" download
                        class="btn btn-primary" @if(!$viewingQr->qr_image_path || !file_exists(storage_path('app/public/' . $viewingQr->qr_image_path))) style="pointer-events:none;opacity:.5;" @endif>
                        <i class="bi bi-download"></i> دانلود
                    </a>
                    <button class="btn btn-secondary" wire:click="closeModals">بستن</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- مودال حذف --}}
    @if ($showDeleteModal)
    <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle"></i> حذف QR Code</h5>
                    <button type="button" class="btn-close" wire:click="closeModals"></button>
                </div>
                <div class="modal-body">
                    آیا از حذف این QR Code مطمئن هستید؟ این عمل قابل بازگشت نیست.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                    <button class="btn btn-danger" wire:click="delete">
                        <i class="bi bi-trash"></i> حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
