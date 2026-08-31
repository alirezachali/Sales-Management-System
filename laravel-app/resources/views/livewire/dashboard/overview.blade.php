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
    <div class="row g-3 mb-4">

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

    
    <div class="row g-3">

        <!-- کارت آخرین فروش‌ها-->
        <div class="col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-warning text-dark opacity-70">
                    <strong>آخرین فروش‌ها</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                {{-- <th>فاکتور</th> --}}
                                <th>فروشنده</th>
                                <th>مبلغ</th>
                                <th>تاریخ</th>
                                <th width="60">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSales as $sale)
                                <tr wire:key="latest-sale-{{ $sale->id }}">
                                    {{-- <td>{{ $sale->invoice_number }}</td> --}}
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

        <div class="col-md-6">
            <div class="row g-3">

                <!-- کارت لیست کالاهای کم‌موجود-->
                <div class="col-12">
                    <div class="card dashboard-card border-3">
                        <div class="card-header bg-danger opacity-70">
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

                <!-- کارت کاربران -->
                <div class="col-12">
                    <div class="card dashboard-card border-3">
                        <div class="card-header bg-success text-dark opacity-70">
                            <strong>👤 کاربران</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>نام کاربری</th>
                                        <th>نقش</th>
                                        <th>آخرین ورود</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr wire:key="user-{{ $user->id }}">
                                            <td>{{ $user->username }}</td>
                                            <td>
                                                @if ($user->role)
                                                    <span class="badge bg-warning text-dark"
                                                        style="background-color: {{ $user->role->color ?? '#6c757d' }}">
                                                        {{ $user->role->display_name ?? $user->role->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->isOnline())
                                                    <span class="badge bg-success">آنلاین</span>
                                                @elseif($user->last_login_at)
                                                    {{ jalaliDateTime($user->last_login_at) }}
                                                @else
                                                    <span class="text-muted">هنوز وارد نشده</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                کاربری یافت نشد.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- کارت کارهای در حال انجام -->
        <div class="col-12">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center opacity-70">
                    <strong>
                        <i class="bi bi-hourglass-split text-warning"></i>
                        کارهای در حال انجام
                    </strong>
                    <a href="{{ route('todos.index') }}" class="btn btn-sm btn-primary">
                        مشاهده همه
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
                                            <span class="badge bg-secondary text-dark">{{ $todo->assignee->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $todo->priority_color }} text-dark">
                                            {{ $todo->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($todo->due_date)
                                            <span class="{{ $todo->due_date->isPast() ? 'text-danger fw-bold' : '' }}">
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
            <div class="card dashboard-card border-3">
                <div class="card-header bg-secondary text-dark opacity-70">
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
