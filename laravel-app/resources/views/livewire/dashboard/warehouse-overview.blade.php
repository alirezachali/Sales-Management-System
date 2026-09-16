<div wire:poll.{{ $pollingSeconds }}s="$refresh">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-3 mb-4" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center mb-2">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-seam-fill text-primary"></i>
                داشبورد انباردار
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

        {{-- دکمه‌های دسترسی سریع --}}
        <div class="card-body d-flex flex-wrap gap-2">
            @can('products.view')
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="bi bi-box-seam"></i>
                    لیست محصولات
                </a>
            @endcan
            @can('categories.view')
                <a href="{{ route('categories.index') }}" class="btn btn-info text-dark">
                    <i class="bi bi-grid"></i>
                    دسته‌بندی‌ها
                </a>
            @endcan
            @can('purchases.view')
                <a href="{{ route('purchase-invoices.index') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-plus"></i>
                    ثبت فاکتور خرید
                </a>
            @endcan
        </div>
    </div>

    {{-- کارت‌های آماری --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-body">
                    <!-- تعداد کل کالاهای ثبت‌شده در سیستم -->
                    <div class="dashboard-title">
                        <h2>📦 تعداد کالاها</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-primary">{{ number_format($productsCount) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-body">
                    <!-- تعداد دسته‌بندی‌های محصولات -->
                    <div class="dashboard-title">
                        <h2>🗂️ دسته‌بندی‌ها</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-info">{{ number_format($categoriesCount) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-body">
                    <!-- تعداد کالاهای در حال اتمام -->
                    <div class="dashboard-title">
                        <h2>⚠️ کالاهای در حال اتمام</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-danger">{{ number_format($runningOutCount) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-3">

        <!-- کارت کالاهای در حال اتمام -->
        <div class="col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-danger text-dark opacity-70">
                    <strong>
                        <i class="bi bi-exclamation-triangle"></i>
                        کالاهای در حال اتمام
                        <small class="text-muted fw-normal">(موجودی ≤ {{ number_format($stockAlert, 0, '.', ',') }})</small>
                    </strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>نام کالا</th>
                                <th>موجودی</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($runningOutList as $product)
                                <tr wire:key="running-out-{{ $product->id }}">
                                    <td class="fw-bold">{{ $product->name }}</td>
                                    <td>
                                        <span class="badge bg-danger text-dark">
                                            {{ number_format((float) $product->stock, 0, '.', ',') }}
                                            <span class="fw-normal">{{ $product->unit }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        همه کالاها موجودی مناسبی دارند.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- کارت لیست کارهای من -->
        @include('livewire.dashboard.partials.todo-card')

        <!-- کارت کالاهای پرفروش -->
        <div class="col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-success text-dark opacity-70">
                    <strong>
                        <i class="bi bi-graph-up-arrow"></i>
                        کالاهای پرفروش
                    </strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>نام کالا</th>
                                <th>تعداد فروش</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bestSellers as $product)
                                <tr wire:key="best-seller-{{ $product->id }}">
                                    <td class="fw-bold">{{ $product->name }}</td>
                                    <td>
                                        <span class="badge bg-success text-dark">
                                            {{ number_format((float) $product->sold_quantity, 0, '.', ',') }}
                                            <span class="fw-normal">{{ $product->unit }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        هنوز فروشی ثبت نشده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- کارت کالاهای کم‌فروش -->
        <div class="col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-warning text-dark opacity-70">
                    <strong>
                        <i class="bi bi-graph-down-arrow"></i>
                        کالاهای کم‌فروش
                    </strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>نام کالا</th>
                                <th>تعداد فروش</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($poorSellers as $product)
                                <tr wire:key="poor-seller-{{ $product->id }}">
                                    <td class="fw-bold">{{ $product->name }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ number_format((float) $product->sold_quantity, 0, '.', ',') }}
                                            <span class="fw-normal">{{ $product->unit }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        کالایی یافت نشد.
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
