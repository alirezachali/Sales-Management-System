<div wire:poll.{{ $pollingSeconds }}s="$refresh">

    <div class="card shadow-sm border-3 mb-4" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center mb-2">
            <h3 class="fw-bold mb-1">
                    <i class="bi bi-speedometer2 text-primary"></i>
                    داشبورد مدیریتی
                </h3>
            <small class="text-muted d-flex align-items-center gap-1">
                <span wire:loading.flex wire:target="$refresh" class="align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm"></span>
                    در حال به‌روزرسانی...
                </span>
                <span wire:loading.remove wire:target="$refresh">
                    به‌صورت خودکار هر {{ $pollingSeconds }} ثانیه به‌روزرسانی می‌شود
                </span>
            </small>
        </div>
        </div>

        {{-- کارت‌های آماری --}}
        <div class="row g-3 mb-2">
            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card border-3">
                    <div class="card-body">
                        <!-- کارت آمار فروش امروز-->
                        <div class="dashboard-title">
                            <h2>💰 فروش امروز</h2>
                        </div>
                        <!-- فروش امروز از دیتابیس-->
                        <div class="dashboard-number">
                            <div class="h1 mb-0 text-success">{{ number_format($todaySales) }}
                                {{ setting('currency', '') }}</div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card border-3">
                    <div class="card-body">
                        <!-- کارت آمار فاکتورهای امروز-->
                        <div class="dashboard-title">
                            <h2>🧾 فاکتورهای امروز</h2>
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
                <div class="card dashboard-card border-3">
                    <div class="card-body">
                        <div class="dashboard-title">
                            <h2> 📦 تعداد کالاها</h2>
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
                <div class="card dashboard-card border-3">
                    <div class="card-body">
                        <div class="dashboard-title">
                            <h2>⚠️ کالاهای کم موجود</h2>
                        </div>
                        <div class="dashboard-number">
                            <div class="h1 mb-0 text-danger">{{ $lowStockProducts }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- کارت آخرین فروش‌ها-->
        <div class="row mt-4 mb-4">
            <div class="col-lg-8">
                <div class="card dashboard-card border-3">
                    <div class="card-header">
                        <strong>آخرین فروش‌ها</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>فاکتور</th>
                                    <th>صندوق‌دار</th>
                                    <th>مبلغ</th>
                                    <th>تاریخ</th>
                                    <th width="120">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSales as $sale)
                                    <tr wire:key="latest-sale-{{ $sale->id }}">
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->user->name ?? '-' }}</td>
                                        <td>{{ number_format($sale->final_price) }}</td>
                                        <td>{{ jalaliDateTime($sale->created_at) }}</td>
                                        <td>
                                            <a href="{{ route('invoice', $sale) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                👁️
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

            <!-- کارت لیست کالاهای کم‌موجود-->
            <div class="col-lg-4">
                <div class="card dashboard-card border-3">
                    <div class="card-header bg-danger text-white">
                        ⚠️ لیست کالاهای کم‌موجودی
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($lowStockList as $product)
                            <div class="list-group-item d-flex justify-content-between"
                                wire:key="low-stock-{{ $product->id }}">
                                <span>{{ $product->name }}</span>
                                <span class="badge bg-danger text-dark">
                                    {{ $product->formatted_stock }}
                                    <span class="text-dark">{{ $product->unit }}</span>
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
        </div>

        
        <!-- کارت نمودار فروش 30 روز گذشته-->
        <div class="card dashboard-card mt-4  border-3">
            <div class="card-header">
                <strong>
                    نمودار فروش ۳۰ روز اخیر
                </strong>
            </div>
            {{-- wire:ignore باعث می‌شود کنواس با هر poll دوباره ساخته نشود؛
             آپدیت داده‌ها فقط از طریق رویداد sales-chart-updated انجام می‌شود --}}
            <div class="card-body" wire:ignore>
                <canvas id="salesChart" x-data="salesChart(@js($labels), @js($chartData))" x-init="init()"></canvas>
            </div>
        </div>
    

</div>

@script
    <script>
        Alpine.data('salesChart', (initialLabels, initialData) => ({
            chart: null,

            init() {
                this.chart = new Chart(this.$el, {
                    type: 'line',
                    data: {
                        labels: initialLabels,
                        datasets: [{
                            label: 'فروش',
                            data: initialData,
                            borderWidth: 3,
                            fill: true,
                            tension: .4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
    </script>
@endscript
