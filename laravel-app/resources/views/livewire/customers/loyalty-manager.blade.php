<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="stat-icon" style="--icon-bg: rgba(217,70,239,.15); --icon-color:#d946ef">
                        <i class="bi bi-gem"></i>
                    </div>
                    <div class="subheader">جمع امتیازهای فعال</div>
                    <div class="h2 mb-0 fw-bold">{{ number_format($totalPoints) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-9">
            <div class="card stat-card border-0 h-100">
                <div class="card-body">
                    <div class="subheader mb-2"><i class="bi bi-trophy"></i> صدرنشینان امتیاز</div>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse ($topEarners as $c)
                            <span class="badge bg-primary-subtle text-primary-emphasis fs-6">
                                {{ $c->full_name }} — {{ number_format($c->points) }}
                            </span>
                        @empty
                            <span class="text-muted small">هنوز امتیازی ثبت نشده.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card mb-4 border-0">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" wire:model.live.debounce.400ms="search" placeholder="جستجوی مشتری...">
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" wire:loading.class="opacity-50">
        <div class="card-header">
            <h3 class="fw-bold mb-1"><i class="bi bi-gem text-fuchsia"></i> باشگاه امتیازات مشتریان</h3>
            <small class="text-muted">امتیازها پس از هر فروش به‌صورت خودکار اضافه می‌شوند و در صندوق قابل تبدیل به تخفیف هستند.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>مشتری</th>
                            <th>رده</th>
                            <th>جمع خرید</th>
                            <th>امتیاز فعلی</th>
                            <th>مصرف‌شده</th>
                            <th width="190" class="text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr wire:key="loy-{{ $customer->id }}">
                                <td class="fw-semibold">
                                    {{ $customer->full_name }}
                                    <div class="text-muted small">{{ $customer->mobile }}</div>
                                </td>
                                <td>
                                    @if ($customer->role)
                                        <span class="badge bg-{{ $customer->role->color }}-subtle text-{{ $customer->role->color }}-emphasis">
                                            <i class="bi {{ $customer->role->icon }}"></i> {{ $customer->role->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small">{{ number_format((float) $customer->total_purchase_amount) }}</td>
                                <td class="fw-bold text-primary">{{ number_format($customer->points) }}</td>
                                <td class="small">{{ number_format($customer->spent_points) }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info" wire:click="openHistory({{ $customer->id }})">
                                        <i class="bi bi-clock-history"></i> گردش
                                    </button>
                                    @can('loyalty.adjust')
                                        <button class="btn btn-sm btn-outline-warning" wire:click="openAdjust({{ $customer->id }})">
                                            <i class="bi bi-sliders"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">مشتری‌ای یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $customers->links() }}</div>
        </div>
    </div>

    {{-- مودال گردش امتیاز --}}
    @if ($showHistoryModal && $historyCustomer)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="loy-history-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">گردش امتیاز — {{ $historyCustomer->full_name }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>تاریخ</th><th>نوع</th><th>امتیاز</th><th>مانده</th><th>شرح</th></tr></thead>
                                <tbody>
                                    @forelse ($history as $tx)
                                        <tr>
                                            <td class="small text-muted">{{ jalaliDateTime($tx->created_at) }}</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $tx->typeText() }}</span></td>
                                            <td class="fw-bold {{ $tx->points >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $tx->points >= 0 ? '+' : '' }}{{ number_format($tx->points) }}
                                            </td>
                                            <td>{{ number_format($tx->balance_after) }}</td>
                                            <td class="small">{{ $tx->description }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted">گردشی وجود ندارد.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال تنظیم دستی --}}
    @if ($showAdjustModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="loy-adjust-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="saveAdjust">
                        <div class="modal-header">
                            <h5 class="modal-title">تنظیم دستی امتیاز</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">مقدار امتیاز (مثبت یا منفی)</label>
                                <input type="number" class="form-control @error('adjustPoints') is-invalid @enderror" wire:model="adjustPoints">
                                @error('adjustPoints')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label">دلیل</label>
                                <input type="text" class="form-control @error('adjustReason') is-invalid @enderror" wire:model="adjustReason" placeholder="مثلاً: هدیه تولد / اصلاح خطا">
                                @error('adjustReason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary">ثبت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
