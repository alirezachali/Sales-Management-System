<div dir="rtl">

    <div class="card shadow-sm border-3 mb-4" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-bank text-primary"></i>
                مدیریت مالی
            </h3>
            <small class="text-muted d-flex align-items-center gap-1">
                <span wire:loading.flex class="align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm"></span>
                    در حال به‌روزرسانی...
                </span>
                <span wire:loading.remove>
                    آمار بازه ({{ $displayFrom }} تا {{ $displayTo }})
                    @if ($isDefaultMonth)
                        • پیش‌فرض: ماه جاری
                    @endif
                </span>
            </small>
        </div>
    </div>

    {{-- فیلتر بازه‌ی شمسی --}}
    <div class="card border-3 mb-4">
        <div class="card-body">
            <div class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="financial-date-from">از تاریخ (شمسی)</label>
                    <input type="text" id="financial-date-from" wire:model="dateFromJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/01"
                        class="form-control @if (isset($dateErrors['from'])) is-invalid @endif">
                    @if (isset($dateErrors['from']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['from'] }}</div>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="financial-date-to">تا تاریخ (شمسی)</label>
                    <input type="text" id="financial-date-to" wire:model="dateToJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/31"
                        class="form-control @if (isset($dateErrors['to'])) is-invalid @endif">
                    @if (isset($dateErrors['to']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['to'] }}</div>
                    @endif
                </div>
                <div class="col-md-6 d-flex gap-6 flex-wrap">
                    <button type="button" class="btn btn-primary" wire:click="applyDates" wire:loading.attr="disabled">
                        <span wire:loading wire:target="applyDates" class="spinner-border spinner-border-sm"></span>
                        <i class="bi bi-funnel"></i>
                        اعمال بازه
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="resetToCurrentMonth"
                        title="بازگشت به ماه جاری">
                        <i class="bi bi-calendar-month"></i>
                        ماه جاری
                    </button>
                    <button type="button" class="btn btn-info" wire:click="$refresh"
                        title="به‌روزرسانی آمار">
                        <i class="bi bi-arrow-clockwise"></i>
                        به‌روزرسانی
                    </button>
                </div>
            </div>
            @if (isset($dateErrors['range']))
                <div class="alert alert-warning mt-2 mb-0 py-2">{{ $dateErrors['range'] }}</div>
            @endif
            @if ($isFallback && empty($dateErrors))
                <div class="alert alert-info mt-2 mb-0 py-2">بازه نامعتبر بود؛ آمار ماه جاری نمایش داده شد.</div>
            @endif
        </div>
    </div>

    {{-- کارت‌های مالی --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3 h-100">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>💰 فروش بازه انتخابی</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-success">{{ number_format($salesMonth) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                    </div>
                    <small class="text-muted">جمع مبلغ نهایی فاکتورها ({{ $displayFrom }} تا {{ $displayTo }})</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3 h-100">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>📈 سود خالص بازه</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($netProfit) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                    </div>
                    <small class="text-muted">سود ناخالص ({{ number_format($grossProfit) }}) منهای هزینه‌ها ({{ number_format($totalExpensesMonth) }})</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3 h-100">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>👥 حقوق پرداختی کارکنان</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-info">{{ number_format($salaryMonth) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                    </div>
                    <small class="text-muted">هزینه‌های بازه که به کارمند وصل‌اند</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="card dashboard-card border-3 h-100">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>🧾 هزینه‌های بازه</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-warning">{{ number_format($otherExpensesMonth) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                    </div>
                    <small class="text-muted">بدون احتساب حقوق • مجموع کل با حقوق: {{ number_format($totalExpensesMonth) }} {{ setting('currency', '') }}</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card dashboard-card border-3 h-100">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>📦 ارزش محصولات موجود در انبار</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-secondary">{{ number_format($inventoryValue) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                    </div>
                    <small class="text-muted">Σ (موجودی × قیمت خرید) همه محصولات • لحظه‌ای، مستقل از بازه</small>
                </div>
            </div>
        </div>

    </div>

    {{-- جزئیات فرمول سود --}}
    <div class="card border-3">
        <div class="card-header bg-secondary">
            <strong><i class="bi bi-calculator text-primary"></i> نحوه محاسبه سود خالص ({{ $displayFrom }} تا {{ $displayTo }})</strong>
        </div>
        <div class="card-body">
            <div class="row g-3 text-center">
                <div class="col-md-3">
                    <div class="subheader">سود ناخالص فروش</div>
                    <div class="h4 mb-0 text-success">{{ number_format($grossProfit) }}</div>
                    <small class="text-muted">Σ (قیمت فروش − قیمت خرید)</small>
                </div>
                <div class="col-md-1 d-flex align-items-center justify-content-center">
                    <span class="h3 mb-0">−</span>
                </div>
                <div class="col-md-3">
                    <div class="subheader">حقوق کارکنان</div>
                    <div class="h4 mb-0 text-info">{{ number_format($salaryMonth) }}</div>
                    <small class="text-muted">هزینه‌های دارای کارمند</small>
                </div>
                <div class="col-md-1 d-flex align-items-center justify-content-center">
                    <span class="h3 mb-0">−</span>
                </div>
                <div class="col-md-3">
                    <div class="subheader">سایر هزینه‌ها</div>
                    <div class="h4 mb-0 text-warning">{{ number_format($otherExpensesMonth) }}</div>
                    <small class="text-muted">هزینه‌های بدون کارمند</small>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <strong>سود خالص بازه:</strong>
                <span class="h3 mb-0 {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }}">{{ number_format($netProfit) }} {{ setting('currency', '') }}</span>
            </div>
        </div>
    </div>

</div>
