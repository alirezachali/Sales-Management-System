@extends('layouts.app')
@section('title', 'داشبرد مدیریتی')
@section('content')

    <div class="container bg-dark">

        <h2 class="mb-4 bg-dark">
            داشبورد مدیریت
        </h2>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">

                        <div class="dashboard-icon bg-sales">
                            💰
                        </div>

                        <div class="dashboard-title">
                            فروش امروز
                        </div>

                        <div class="dashboard-number">
                            {{ number_format($todaySales) }}
                        </div>

                        <small>
                            تومان
                        </small>

                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">

                        <div class="dashboard-icon bg-invoice">
                            🧾
                        </div>

                        <div class="dashboard-title">
                            فاکتورهای امروز
                        </div>

                        <div class="dashboard-number">
                            {{ $todayInvoices }}
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">

                        <div class="dashboard-icon bg-product">
                            📦
                        </div>

                        <div class="dashboard-title">
                            تعداد کالاها
                        </div>

                        <div class="dashboard-number">
                            {{ $productsCount }}
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="card-body">

                        <div class="dashboard-icon bg-stock">
                            ⚠️
                        </div>

                        <div class="dashboard-title">
                            کالاهای کم موجود
                        </div>

                        <div class="dashboard-number">
                            {{ $lowStockProducts }}
                        </div>

                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <div class="card dashboard-card">

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
                                        <tr>
                                            <td>{{ $sale->invoice_number }}</td>
                                            <td>{{ $sale->user->name ?? '-' }}</td>
                                            <td>{{ number_format($sale->final_price) }}</td>
                                            <td>{{ $sale->created_at->format('Y/m/d H:i') }}</td>
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

                <div class="col-lg-4">
                    <div class="card dashboard-card">

                        <div class="card-header bg-danger text-white">
                            ⚠️ کالاهای کم‌موجودی
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($lowStockList as $product)
                                <div class="list-group-item d-flex justify-content-between">
                                    <span>{{ $product->name }}</span>
                                    <span class="badge bg-danger">
                                        {{ $product->stock }}
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

                <div class="card dashboard-card mt-4">
                    <div class="card-header">
                        <strong>
                            نمودار فروش ۳۰ روز اخیر
                        </strong>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'فروش',
                        data: @json($data),
                        borderWidth: 3,
                        fill: true,
                        tension: .4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    </script>
@endsection
