<div wire:poll.{{ $pollingSeconds }}s="$refresh">

    <div class="card shadow-sm mb-4">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-speedometer2 text-primary"></i>
                {{ __('dash.admin.title') }}
            </h3>
            <small class="text-muted d-flex align-items-center gap-1">
                <span wire:loading.flex wire:target="$refresh" class="align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm"></span>
                    {{ __('dash.admin.update') }}
                </span>
                <span wire:loading.remove wire:target="$refresh">
                    {{ __('dash.admin.update_cap') }}
                </span>
            </small>
        </div>
    </div>

    {{-- کارت‌های آماری --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <!-- کارت آمار فروش امروز-->
                    <div class="dashboard-title">
                        <h2>💰 {{ __('dash.admin.card_1') }}</h2>
                    </div>
                    <!-- فروش امروز از دیتابیس-->
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-success">{{ number_format($todaySales) }}
                            {{ setting('currency', '') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <!-- کارت آمار فاکتورهای امروز-->
                    <div class="dashboard-title">
                        <h2>🧾 {{ __('dash.admin.card_2') }}</h2>
                    </div>
                    <!-- تعداد فاکتورهای امروز از دیتابیس-->
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-info">{{ $todayInvoices }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- کارت آمار تعداد کالاها-->
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2> 📦 {{ __('dash.admin.card_3') }}</h2>
                    </div>
                    <!-- تعداد کالاها از دیتابیس-->
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-primary">{{ $productsCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- کارت آمار تعدا کالاهای کم موجود-->
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="dashboard-title">
                        <h2>⚠️ {{ __('dash.admin.card_4') }}</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-danger">{{ $lowStockProducts }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- نمودارهای تحلیلی: پرفروش‌ترین کالاها + ترکیب مالی ماه جاری --}}
    <div class="row g-3 mb-3">

        {{-- نمودار پرفروش‌ترین کالاهای این ماه (نمودار میله‌ای افقی) --}}
        <div class="col-lg-7">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>🏆 {{ __('dash.admin.chart_top_products') }}</strong>
                    <small class="text-muted">{{ __('dash.admin.chart_top_products_hint') }}</small>
                </div>
                <div class="card-body">
                    {{-- wire:ignore: کنواس دست‌نخورده می‌ماند و فقط با رویداد
                         top-products-chart-updated دوباره پر می‌شود --}}
                    <div wire:ignore style="height: 320px">
                        <canvas id="topProductsChart"
                            x-data="topProductsChart(@js($topProductLabels), @js($topProductData), @js($topProductRevenue))"
                            x-init="init()"></canvas>
                    </div>
                    @if (empty($topProductLabels))
                        <p class="text-center text-muted mb-0 mt-2">{{ __('dash.admin.chart_empty') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- چارت دایره‌ای ترکیب مالی ماه جاری --}}
        <div class="col-lg-5">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>💹 {{ __('dash.admin.chart_finance') }}</strong>
                    <small class="text-muted">{{ __('dash.admin.chart_finance_hint') }}</small>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 260px">
                        <canvas id="financeChart"
                            x-data="financeChart(@js($finance['labels']), @js($finance['data']), @js($finance['colors']), @js($finance['sales']))"
                            x-init="init()"></canvas>
                    </div>

                    {{-- خلاصه‌ی عددی ترکیب مالی --}}
                    <div class="row g-2 mt-3 text-center">
                        <div class="col-6">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="small text-muted">{{ __('dash.admin.fin_sales') }}</div>
                                <div class="fw-bold text-primary">
                                    {{ number_format($finance['sales']) }}
                                    <small>{{ setting('currency', '') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="small text-muted">{{ __('dash.admin.fin_expenses') }}</div>
                                <div class="fw-bold text-danger">
                                    {{ number_format($finance['otherExpenses']) }}
                                    <small>{{ setting('currency', '') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="small text-muted">{{ __('dash.admin.fin_salary') }}</div>
                                <div class="fw-bold text-warning-emphasis">
                                    {{ number_format($finance['salary']) }}
                                    <small>{{ setting('currency', '') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-2 h-100">
                                <div class="small text-muted">{{ $finance['isLoss'] ? __('dash.admin.fin_loss') : __('dash.admin.fin_net_profit') }}</div>
                                <div class="fw-bold {{ $finance['isLoss'] ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($finance['netProfit']) }}
                                    <small>{{ setting('currency', '') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3">

        <!-- کارت آخرین فروش‌ها-->
        <div class="col-md-6">
            <div class="card dashboard-card">
                <div class="card-header">
                    <strong>🛍️ آخـــــرین فــــاکتورهای فــــروش</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>فروشنده</th>
                                <th>مبلغ</th>
                                <th>تاریخ</th>
                                <th width="60">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSales as $sale)
                                <tr wire:key="latest-sale-{{ $sale->id }}">
                                    <td>{{ $sale->user->name ?? '-' }}</td>
                                    <td>{{ number_format($sale->final_price) }}</td>
                                    <td>{{ jalaliDateTime($sale->created_at) }}</td>
                                    <td>
                                        <a href="{{ route('invoice', $sale) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            🧾
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        هنوز فروشی ثبت نشده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row g-3">

                <!-- کارت لیست کالاهای کم‌موجود-->
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            ⚠️ لـــیست کـــالاهای درحـــال اتـــمام مـــوجودی
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($lowStockList as $product)
                                <div class="list-group-item d-flex justify-content-between"
                                    wire:key="low-stock-{{ $product->id }}">
                                    <span>{{ $product->name }}</span>
                                    <span class="badge bg-danger-subtle text-danger-emphasis">
                                        موجودی فعلی >> {{ $product->formatted_stock }}
                                        <span>{{ $product->unit }}</span>
                                    </span>
                                </div>
                            @empty
                                <div class="list-group-item">
                                    همه کالاها موجودی مناسبی دارند.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- کارت کاربران -->
                <livewire:dashboard.users-online-card />

                <!-- کارت کارهای در حال انجام -->
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>
                       ✅ لـــیست وظـــیفه‌هـــای درحـــال انـــجام
                    </strong>
                    <a href="{{ route('todos.index') }}" class="btn btn-sm btn-outline-primary">
                        مشاهده‌همه
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>انجام‌دهنده</th>
                                <th>اولویت</th>
                                <th>سررسید</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inProgressTodos as $todo)
                                <tr wire:key="in-progress-todo-{{ $todo->id }}">
                                    <td class="fw-bold">{{ $todo->title }}</td>
                                    <td>
                                        @if ($todo->assignee)
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $todo->assignee->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $todo->priority_color }}-subtle text-{{ $todo->priority_color }}-emphasis">
                                            {{ $todo->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($todo->due_date)
                                            <span class="{{ $todo->due_date->isPast() ? 'text-danger-emphasis fw-bold' : '' }}">
                                                {{ jalaliDate($todo->due_date) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        هیچ کار در حال انجامی وجود ندارد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

            </div>
        </div>

        

        <!-- کارت نمودار فروش 30 روز گذشته-->
        <div class="col-md-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <strong>
                       📈 نـــــمودار مـــــبلغ فـــــروش ۳۰ روز گـــــذشته
                    </strong>
                </div>
                {{-- wire:ignore باعث می‌شود کنواس با هر poll دوباره ساخته نشود؛
             آپدیت داده‌ها فقط از طریق رویداد sales-chart-updated انجام می‌شود --}}
                <div class="card-body" wire:ignore style="height: 340px">
                    <canvas id="salesChart" x-data="salesChart(@js($labels), @js($chartData))" x-init="init()"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

@script
    <script>
        Alpine.data('salesChart', (initialLabels, initialData) => ({
            chart: null,

            init() {
                // رنگ‌آمیزی هر میله با یک طیف از رنگین‌کمان + گرادیان عمودی،
                // تا نمودار فروش ۳۰ روزه رنگی و چشم‌نواز شود.
                const barColors = (context) => {
                    const {
                        ctx,
                        chartArea
                    } = context.chart;

                    // در اولین رندر قبل از محاسبه‌ی ابعاد، یک رنگ ساده برگردانده می‌شود
                    if (!chartArea) return 'rgba(99,102,241,.85)';

                    const total = Math.max(1, context.dataset.data.length - 1);
                    const hue = 205 + (context.dataIndex / total) * 135;
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, `hsla(${hue}, 85%, 58%, .25)`);
                    gradient.addColorStop(1, `hsla(${hue}, 90%, 58%, .95)`);

                    return gradient;
                };

                this.chart = new Chart(this.$el, {
                    type: 'bar',
                    data: {
                        labels: initialLabels,
                        datasets: [{
                            label: 'فروش',
                            data: initialData,
                            backgroundColor: barColors,
                            hoverBackgroundColor: barColors,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 26,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                rtl: true,
                                callbacks: {
                                    label: (ctx) => ` فروش: ${Number(ctx.parsed.y).toLocaleString()}`,
                                },
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: 10
                                },
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148,163,184,.2)'
                                },
                                ticks: {
                                    callback: (value) => Number(value).toLocaleString()
                                },
                            },
                        },
                    },
                });

                // با هر رندر مجدد کامپوننت (مثلاً هر بار wire:poll) این رویداد از
                // سرور با داده‌ی تازه شلیک می‌شود و فقط داده‌ی نمودار آپدیت می‌شود.
                Livewire.on('sales-chart-updated', ({
                    labels,
                    data
                }) => {
                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = data;
                    this.chart.update();
                });
            },
        }));

        Alpine.data('topProductsChart', (initialLabels, initialData, initialRevenue) => ({
            chart: null,
            revenue: initialRevenue || [],

            init() {
                // پالت رنگی؛ به تعداد میله‌ها تکرار می‌شود
                const palette = ['#6366f1', '#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#a855f7', '#14b8a6'];

                this.chart = new Chart(this.$el, {
                    type: 'bar',
                    data: {
                        labels: initialLabels,
                        datasets: [{
                            label: 'تعداد فروش',
                            data: initialData,
                            backgroundColor: initialLabels.map((_, i) => palette[i % palette.length]),
                            borderRadius: 8,
                            maxBarThickness: 34,
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                rtl: true,
                                callbacks: {
                                    label: (ctx) => ` تعداد فروش: ${Number(ctx.parsed.x).toLocaleString()}`,
                                    afterLabel: (ctx) => {
                                        const value = this.revenue?.[ctx.dataIndex] ?? 0;
                                        return ` درآمد: ${Number(value).toLocaleString()}`;
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148,163,184,.2)'
                                },
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                            },
                        },
                    },
                });

                Livewire.on('top-products-chart-updated', ({
                    labels,
                    data,
                    revenue
                }) => {
                    this.revenue = revenue || [];
                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = data;
                    this.chart.data.datasets[0].backgroundColor = labels.map((_, i) => palette[i % palette.length]);
                    this.chart.update();
                });
            },
        }));

        Alpine.data('financeChart', (initialLabels, initialData, initialColors, initialTotal) => ({
            chart: null,
            total: initialTotal,

            init() {
                // پلاگین کوچک برای نوشتن مبلغ فروش در وسط چارت دایره‌ای
                const centerText = {
                    id: 'financeCenterText',
                    afterDraw: (chart) => {
                        const {
                            ctx,
                            chartArea
                        } = chart;
                        if (!chartArea) return;

                        const x = (chartArea.left + chartArea.right) / 2;
                        const y = (chartArea.top + chartArea.bottom) / 2;

                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillStyle = '#64748b';
                        ctx.font = '600 12px Vazirmatn, Tahoma, sans-serif';
                        ctx.fillText('فروش این ماه', x, y - 12);
                        ctx.fillStyle = '#0f172a';
                        ctx.font = '800 18px Vazirmatn, Tahoma, sans-serif';
                        ctx.fillText(Number(this.total || 0).toLocaleString(), x, y + 14);
                        ctx.restore();
                    },
                };

                this.chart = new Chart(this.$el, {
                    type: 'doughnut',
                    plugins: [centerText],
                    data: {
                        labels: initialLabels,
                        datasets: [{
                            data: initialData,
                            backgroundColor: initialColors,
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverOffset: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                rtl: true,
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    padding: 14
                                },
                            },
                            tooltip: {
                                rtl: true,
                                callbacks: {
                                    label: (ctx) => ` ${ctx.label}: ${Number(ctx.parsed).toLocaleString()}`,
                                },
                            },
                        },
                    },
                });

                Livewire.on('finance-chart-updated', ({
                    labels,
                    data,
                    colors,
                    total
                }) => {
                    this.total = total;
                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = data;
                    this.chart.data.datasets[0].backgroundColor = colors;
                    this.chart.update();
                });
            },
        }));
    </script>
@endscript
