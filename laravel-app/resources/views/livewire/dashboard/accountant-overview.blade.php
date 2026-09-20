<div wire:poll.{{ $pollingSeconds }}s="$refresh" dir="rtl">

    @include('partials.flash-messages')

    {{-- ================= هدر داشبورد ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-calculator text-primary"></i>
                داشبورد حسابدار
            </h3>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted">
                    دوره مالی: {{ $monthStartJalali }} تا {{ $monthEndJalali }}
                    @if ($isDefaultMonth)
                        • پیش‌فرض: ماه جاری
                    @endif
                </small>
                <small class="text-muted d-flex align-items-center gap-1">
                    <span wire:loading.flex wire:target="$refresh" class="align-items-center gap-1">
                        <span class="spinner-border spinner-border-sm"></span>
                        در حال به‌روزرسانی...
                    </span>
                    <span wire:loading.remove wire:target="$refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                        به‌صورت خودکار هر {{ $pollingSeconds }} ثانیه به‌روزرسانی می‌شود
                    </span>
                </small>
            </div>
        </div>
        <div class="card-body py-2 d-flex flex-wrap gap-2">
            @can('financial.view')
                <a href="{{ route('financial.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-bank"></i> مدیریت مالی
                </a>
            @endcan
            @can('reports.profit')
                <a href="{{ route('reports.profit') }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-graph-up-arrow"></i> گزارش سود و زیان
                </a>
            @endcan
            @can('cashboxes.view')
                <a href="{{ route('cashboxes.index') }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-safe"></i> صندوق‌ها
                </a>
            @endcan
            @can('debts.view')
                <a href="{{ route('debts.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-journal-minus"></i> بدهی‌ها
                </a>
            @endcan
            @can('payrolls.view')
                <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-cash-coin"></i> حقوق‌ها
                </a>
            @endcan
            @can('expenses.view')
                <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-wallet2"></i> هزینه‌ها
                </a>
            @endcan
        </div>
    </div>

    {{-- ================= فیلتر بازه‌ی شمسی ================= --}}
    <div class="card border-3 mb-4">
        <div class="card-body">
            <div class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="accountant-date-from">از تاریخ (شمسی)</label>
                    <input type="text" id="accountant-date-from" wire:model="dateFromJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/01"
                        class="form-control @if (isset($dateErrors['from'])) is-invalid @endif">
                    @if (isset($dateErrors['from']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['from'] }}</div>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="accountant-date-to">تا تاریخ (شمسی)</label>
                    <input type="text" id="accountant-date-to" wire:model="dateToJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/31"
                        class="form-control @if (isset($dateErrors['to'])) is-invalid @endif">
                    @if (isset($dateErrors['to']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['to'] }}</div>
                    @endif
                </div>
                <div class="col-md-6 d-flex gap-2 flex-wrap">
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

    {{-- ================= کارت‌های آماری لحظه‌ای ================= --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-3 col-md-6">
            @can('reports.sales')
                <a href="{{ route('reports.sales') }}" class="text-decoration-none">
            @endcan
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>💰 فروش امروز</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-success">{{ number_format($todaySales) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                        <small class="text-muted">{{ number_format($todayInvoices) }} فاکتور</small>
                    </div>
                </div>
            </div>
            @can('reports.sales')
                </a>
            @endcan
        </div>

        <div class="col-lg-3 col-md-6">
            @can('expenses.view')
                <a href="{{ route('expenses.index') }}" class="text-decoration-none">
            @endcan
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>🧾 هزینه‌های امروز</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-warning">{{ number_format($todayExpenses) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @can('expenses.view')
                </a>
            @endcan
        </div>

        <div class="col-lg-3 col-md-6">
            @can('cashboxes.view')
                <a href="{{ route('cashboxes.index') }}" class="text-decoration-none">
            @endcan
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>🏦 مانده صندوق‌ها</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-info">{{ number_format($totalCashboxBalance) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                        <small class="text-muted">{{ $cashboxes->count() }} صندوق فعال</small>
                    </div>
                </div>
            </div>
            @can('cashboxes.view')
                </a>
            @endcan
        </div>

        <div class="col-lg-3 col-md-6">
            @can('debts.view')
                <a href="{{ route('debts.index') }}" class="text-decoration-none">
            @endcan
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>🛡 مانده بدهی‌ها</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-danger">{{ number_format($totalDebtPayable) }}
                            <small class="fs-6">{{ setting('currency', '') }}</small>
                        </div>
                        <small class="text-muted">
                            @if ($overdueDebtCount > 0)
                                <span class="badge bg-danger">{{ $overdueDebtCount }} مورد سررسید گذشته</span>
                            @endif
                            طلب از مشتریان: {{ number_format($totalCustomerReceivable) }} {{ setting('currency', '') }}
                        </small>
                    </div>
                </div>
            </div>
            @can('debts.view')
                </a>
            @endcan
        </div>

    </div>

    {{-- ================= سود و زیان ماه جاری ================= --}}
    <div class="card dashboard-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong>
                <i class="bi bi-graph-up-arrow text-success"></i>
                خلاصه سود و زیان ({{ $monthStartJalali }} تا {{ $monthEndJalali }})
            </strong>
            @can('reports.profit')
                <a href="{{ route('reports.profit') }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-graph-up-arrow"></i> گزارش کامل سود و زیان
                </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="row g-3 text-center">

                <div class="col-md-3">
                    <div class="subheader">درآمد فروش</div>
                    <div class="h4 mb-0 text-success">{{ number_format($revenue) }}</div>
                    <small class="text-muted">{{ $invoiceCount }} فاکتور</small>
                </div>

                <div class="col-md-1 d-flex align-items-center justify-content-center">
                    <span class="h3 mb-0 text-danger">−</span>
                </div>

                <div class="col-md-3">
                    <div class="subheader">بهای تمام‌شده</div>
                    <div class="h4 mb-0 text-secondary">{{ number_format($cogs) }}</div>
                    <small class="text-muted">تخفیف: {{ number_format($discounts) }}</small>
                </div>

                <div class="col-md-1 d-flex align-items-center justify-content-center">
                    <span class="h3 mb-0 text-danger">−</span>
                </div>

                <div class="col-md-3">
                    <div class="subheader">هزینه‌های دوره</div>
                    <div class="h4 mb-0 text-warning">{{ number_format($operatingExpenses + $payrollMonth) }}</div>
                    <small class="text-muted">جاری: {{ number_format($operatingExpenses) }} • حقوق: {{ number_format($payrollMonth) }}</small>
                </div>

            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>سود ناخالص:</strong>
                    <span class="fs-5 text-success">{{ number_format($grossProfit) }} {{ setting('currency', '') }}</span>
                </div>
                <div>
                    <strong>سود خالص:</strong>
                    <span class="h3 mb-0 {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ number_format($netProfit) }} {{ setting('currency', '') }}
                    </span>
                    <small class="text-muted">(حاشیه {{ $margin }}٪)</small>
                </div>
                <div>
                    <strong>ارزش موجودی انبار:</strong>
                    <span class="fs-5 text-secondary">{{ number_format($inventoryValue) }} {{ setting('currency', '') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= صندوق‌ها و حقوق‌ها ================= --}}
    <div class="row g-3 mb-4">

        {{-- مانده صندوق‌ها --}}
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header">
                    <strong><i class="bi bi-safe text-warning"></i> مانده صندوق‌ها</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>صندوق</th>
                                <th>نوع</th>
                                <th class="text-end">مانده</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cashboxes as $cashbox)
                                <tr wire:key="cashbox-{{ $cashbox->id }}">
                                    <td class="fw-bold">
                                        {{ $cashbox->name }}
                                        @if ($cashbox->is_default)
                                            <span class="badge bg-primary-subtle text-primary-emphasis">پیش‌فرض</span>
                                        @endif
                                    </td>
                                    <td>{{ $cashbox->typeText() }}</td>
                                    <td class="text-end fw-bold text-info">{{ number_format($cashbox->balance) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">صندوق فعالی وجود ندارد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- حقوق‌های در انتظار پرداخت --}}
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-cash-coin text-info"></i> حقوق‌های در انتظار پرداخت (بازه انتخابی)</strong>
                    @can('payrolls.view')
                        <a href="{{ route('payrolls.index') }}" class="btn btn-sm btn-outline-info">مشاهده همه</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>کارمند</th>
                                <th class="text-end">خالص</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPayrolls as $payroll)
                                <tr wire:key="payroll-{{ $payroll->id }}">
                                    <td>{{ $payroll->employee->full_name ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($payroll->net_pay) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payroll->statusColor() }} text-dark">{{ $payroll->statusText() }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">حقوق پرداخت‌نشده‌ای در ماه جاری نیست.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= بدهی‌ها، طلب‌ها و نمودار ================= --}}
    <div class="row g-3">

        {{-- بدهی‌های پرداخت‌نی --}}
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-journal-minus text-danger"></i> بدهی‌های پرداخت‌نی</strong>
                    @can('debts.view')
                        <a href="{{ route('debts.index') }}" class="btn btn-sm btn-outline-danger">مشاهده همه</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>طلبکار</th>
                                <th class="text-end">مانده</th>
                                <th>سررسید</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($debts as $debt)
                                @if ($debt->remaining_amount > 0)
                                    <tr wire:key="debt-{{ $debt->id }}" class="{{ $debt->due_date_status === 'overdue' ? 'table-danger' : '' }}">
                                        <td class="fw-bold">
                                            {{ $debt->creditor_name }}
                                            @if ($debt->creditor_type === 'supplier')
                                                <span class="badge bg-blue-lt">تامین‌کننده</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-danger">{{ number_format($debt->remaining_amount) }}</td>
                                        <td>
                                            @if ($debt->due_date)
                                                <span class="{{ $debt->due_date_status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                                    {{ jalaliDate($debt->due_date) }}
                                                </span>
                                                @if ($debt->due_date_status === 'overdue')
                                                    <i class="bi bi-exclamation-triangle text-danger" title="سررسید گذشته"></i>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">بدهی پرداخت‌نشده‌ای وجود ندارد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- طلب از مشتریان --}}
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-person-exclamation text-primary"></i> طلب از مشتریان (حساب بستانکار)</strong>
                    @can('customers.debtors')
                        <a href="{{ route('customer-debtors.index') }}" class="btn btn-sm btn-outline-primary">مشاهده همه</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>مشتری</th>
                                <th>شماره تماس</th>
                                <th class="text-end">مانده</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customerReceivables as $row)
                                <tr wire:key="receivable-{{ $row->customer_id }}">
                                    <td class="fw-bold">{{ $row->customer->full_name }}</td>
                                    <td>
                                        @if ($row->customer->mobile)
                                            <span class="text-muted">{{ $row->customer->mobile }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-danger-subtle text-danger-emphasis fs-6">
                                            {{ number_format($row->receivable) }} {{ setting('currency', '') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">بدهکاری از مشتریان وجود ندارد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- نمودار ۳۰ روز اخیر --}}
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <strong>
                        <i class="bi bi-bar-chart-line text-success"></i>
                        نمودار فروش در برابر هزینه — ۳۰ روز گذشته
                    </strong>
                </div>
                <div class="card-body" wire:ignore>
                    <canvas id="accountantChart" x-data="accountantChart(@js($chartLabels), @js($chartSales), @js($chartExpenses))" x-init="init()" style="min-height:300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- آخرین فروش‌ها --}}
        <div class="col-md-6">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-receipt text-success"></i> آخرین فروش‌ها</strong>
                    @can('reports.sales')
                        <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-success">گزارش فروش</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>مشتری / فروشنده</th>
                                <th class="text-end">مبلغ</th>
                                <th>تاریخ</th>
                                <th width="60">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSales as $sale)
                                <tr wire:key="latest-sale-{{ $sale->id }}">
                                    <td>
                                        {{ $sale->customer->full_name ?? '-' }}
                                        <small class="text-muted d-block">{{ $sale->user->name ?? '' }}</small>
                                    </td>
                                    <td class="text-end">{{ number_format($sale->final_price) }}</td>
                                    <td>{{ jalaliDateTime($sale->created_at) }}</td>
                                    <td>
                                        <a href="{{ route('invoice', $sale) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">هنوز فروشی ثبت نشده است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- آخرین هزینه‌ها --}}
        <div class="col-md-6">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-wallet2 text-warning"></i> آخرین هزینه‌ها</strong>
                    @can('expenses.view')
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-warning">هزینه‌ها</a>
                    @endcan
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>دسته</th>
                                <th class="text-end">مبلغ</th>
                                <th>تاریخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestExpenses as $expense)
                                <tr wire:key="latest-expense-{{ $expense->id }}">
                                    <td class="fw-bold">{{ $expense->title }}</td>
                                    <td>{{ $expense->category->name ?? '-' }}</td>
                                    <td class="text-end text-warning">{{ number_format($expense->amount) }}</td>
                                    <td>{{ jalaliDate($expense->expense_date) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">هزینه‌ای ثبت نشده است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- کارت لیست کارهای من --}}
        @include('livewire.dashboard.partials.todo-card')

    </div>

</div>

@script
    <script>
        Alpine.data('accountantChart', (initialLabels, initialSales, initialExpenses) => ({
            chart: null,

            init() {
                this.chart = new Chart(this.$el, {
                    type: 'line',
                    data: {
                        labels: initialLabels,
                        datasets: [{
                            label: 'فروش',
                            data: initialSales,
                            borderColor: '#2fb344',
                            backgroundColor: 'rgba(47,179,68,.15)',
                            borderWidth: 2,
                            fill: true,
                            tension: .4,
                            yAxisID: 'y',
                        }, {
                            label: 'هزینه',
                            data: initialExpenses,
                            borderColor: '#f59f00',
                            backgroundColor: 'rgba(245,159,0,.15)',
                            borderWidth: 2,
                            fill: true,
                            tension: .4,
                            yAxisID: 'y',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                    },
                });

                Livewire.on('accountant-chart-updated', ({ labels, sales, expenses }) => {
                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = sales;
                    this.chart.data.datasets[1].data = expenses;
                    this.chart.update();
                });
            },
        }));
    </script>
@endscript
