<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

{{--============ کارت‌های آماری ============--}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تـــعـــداد فـــروش</div>
                    <div class="h1 mb-0">{{ number_format($totals->count ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مـــجـــمـــوع کـــل (قبل از تخفیف)</div>
                    <div class="h1 mb-0 text-primary">
                        {{ number_format($totals->total_price ?? 0) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مـــجـــمـــوع کـــل تـــخـــفـــیـــف</div>
                    <div class="h1 mb-0 text-warning">
                        {{ number_format($totals->discount ?? 0) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مـــبـــلـــغ نـــهـــایـــی فـــروش</div>
                    <div class="h1 mb-0 text-success">
                        {{ number_format($totals->final_price ?? 0) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

{{--================== فیلترها ==================--}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">از تـــاریـــخ</label>
                    <input type="text" wire:model.live.debounce.500ms="dateFromJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/01"
                        class="form-control @if (isset($dateErrors['from'])) is-invalid @endif">
                    @if (isset($dateErrors['from']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['from'] }}</div>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">تـــا تـــاریـــخ</label>
                    <input type="text" wire:model.live.debounce.500ms="dateToJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/31"
                        class="form-control @if (isset($dateErrors['to'])) is-invalid @endif">
                    @if (isset($dateErrors['to']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['to'] }}</div>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">روش پـــرداخـــت</label>
                    <select wire:model.live="filterPaymentType" class="form-select">
                        <option value="">هـــمـــه روش‌هـــا</option>
                        @foreach ($paymentTypeLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100"
                        title="پاک کردن فیلترها">
                        پـــاک کـــردن فـــیـــلـــتـــرهـــا
                    </button>
                </div>
            </div>
            @if (isset($dateErrors['range']))
                <div class="alert alert-warning mt-2 mb-0 py-2">{{ $dateErrors['range'] }}</div>
            @endif
        </div>
    </div>

{{--======================== جدول فروش‌ها ========================--}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-clipboard-data text-primary"></i>
                    گـــزارش فـــروش
                </h3>
                <small class="text-muted">مشاهده فروش‌ها در بازه زمانی انتخابی</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-success text-dark" wire:click="exportExcel" wire:loading.attr="disabled"
                    wire:target="exportExcel" title="خروجی اکسل">
                    <span wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm me-1"></span>
                    <i class="bi bi-file-earmark-excel"></i>
                    خروجی اکسل
                </button>
                <button type="button" class="btn btn-info text-dark" wire:click="exportCsv"
                    wire:loading.attr="disabled" wire:target="exportCsv" title="خروجی CSV">
                    <span wire:loading wire:target="exportCsv" class="spinner-border spinner-border-sm me-1"></span>
                    <i class="bi bi-filetype-csv"></i>
                    خروجی CSV
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="40">ردیف</th>
                            <th>شماره فاکتور</th>
                            <th width="130">نام مشتری</th>
                            <th width="70">منبع</th>
                            <th width="140">تاریخ ثبت</th>
                            <th width="35">اقلام</th>
                            <th width="100">جمع کل</th>
                            <th width="60">تخفیف ({{ setting('currency', '') }})</th>
                            <th width="130">مبلغ نهایی ({{ setting('currency', '') }})</th>
                            <th width="70">پرداخت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr wire:key="sale-{{ $sale->id }}">
                                <td>{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                                <td class="fw-bold">{{ $sale->invoice_number }}</td>
                                <td class="text-center">
                                    @if ($sale->customer?->full_name)
                                        <span class="fw-bold text-warning">
                                            {{ $sale->customer?->full_name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle">مشتری متفرقه</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($sale->isOnline())
                                        <span class="badge bg-primary text-dark">آنلاین</span>
                                    @else
                                        <span class="badge bg-secondary-subtle">حضوری</span>
                                    @endif
                                </td>
                                <td>
                                    {{ jalaliDateTime($sale->created_at) }}
                                </td>
                                <td class="text-center fw-bold text-info">
                                    {{ number_format($sale->items_count) }}
                                </td>
                                <td class="text-center">
                                    {{ number_format($sale->total_price) }}
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-danger">
                                        {{ number_format($sale->discount) }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-success">
                                    {{ number_format($sale->final_price) }}
                                </td>
                                <td>
                                    @switch($paymentTypeLabels[$sale->payment_type] ?? $sale->payment_type)
                                        @case('نقدی')
                                            <span class="badge bg-success text-dark">نقدی</span>
                                        @break

                                        @case('کارت')
                                            <span class="badge bg-primary text-dark">کارت</span>
                                        @break

                                        @case('نسیه')
                                            <span class="badge bg-warning text-dark">نسیه</span>
                                        @break
                                    @endswitch
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        در بازه انتخاب‌شده فروشی یافت نشد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $sales->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
