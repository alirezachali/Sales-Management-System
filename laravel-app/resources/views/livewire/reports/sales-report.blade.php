<div dir="rtl">

    {{-- Success/Error Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد فروش</div>
                    <div class="h1 mb-0">{{ number_format($totals->count ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مجموع کل (قبل از تخفیف)</div>
                    <div class="h1 mb-0 text-primary">
                        {{ number_format($totals->total_price ?? 0) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مجموع تخفیف</div>
                    <div class="h1 mb-0 text-warning">
                        {{ number_format($totals->discount ?? 0) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مبلغ نهایی فروش</div>
                    <div class="h1 mb-0 text-success">
                        {{ number_format($totals->final_price ?? 0) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">از تاریخ</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">تا تاریخ</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">روش پرداخت</label>
                    <select wire:model.live="filterPaymentType" class="form-select">
                        <option value="">همه روش‌ها</option>
                        @foreach ($paymentTypeLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100"
                        title="پاک کردن فیلترها">
                        پاک کردن فیلترها
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول فروش‌ها --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-clipboard-data text-primary"></i>
                    گزارش فروش
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
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">ردیف</th>
                            <th>شماره فاکتور</th>
                            <th width="130">مشتری</th>
                            <th width="160">تاریخ</th>
                            <th width="40">اقلام</th>
                            <th width="100">جمع کل</th>
                            <th width="60">تخفیف</th>
                            <th width="130">مبلغ نهایی</th>
                            <th width="70">روش پرداخت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                            <tr wire:key="sale-{{ $sale->id }}">
                                <td>{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                                <td class="fw-bold">{{ $sale->invoice_number }}</td>
                                <td>
                                    @if ($sale->customer?->full_name)
                                        <span class="badge bg-warning text-dark">
                                            {{ $sale->customer?->full_name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-dark">مشتری متفرقه</span>
                                    @endif
                                </td>
                                <td>{{ jalaliDateTime($sale->created_at) }}</td>
                                <td class="text-center">{{ number_format($sale->items_count) }}</td>
                                <td>{{ number_format($sale->total_price) }}</td>
                                <td>
                                    <span class="badge bg-danger text-light">
                                        {{ number_format($sale->discount) }} %
                                    </span>
                                </td>
                                <td class="fw-bold">
                                    {{ number_format($sale->final_price) }} {{ setting('currency', '') }}
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
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        در بازه انتخاب‌شده فروشی یافت نشد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex flex-wrap justify-content-between align-items-center">
                    <div>{{ $sales->links() }}</div>
                </div>

            </div>
        </div>
    </div>
