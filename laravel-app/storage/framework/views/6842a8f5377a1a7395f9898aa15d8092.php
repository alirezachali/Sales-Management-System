<?php $__env->startSection('title', 'داشبرد مدیریتی'); ?>
<?php $__env->startSection('content'); ?>

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
                            <?php echo e(number_format($todaySales)); ?>

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
                            <?php echo e($todayInvoices); ?>

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
                            <?php echo e($productsCount); ?>

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
                            <?php echo e($lowStockProducts); ?>

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
                                    <?php $__empty_1 = true; $__currentLoopData = $latestSales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($sale->invoice_number); ?></td>
                                            <td><?php echo e($sale->user->name ?? '-'); ?></td>
                                            <td><?php echo e(number_format($sale->final_price)); ?></td>
                                            <td><?php echo e($sale->created_at->format('Y/m/d H:i')); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('invoice', $sale)); ?>" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    👁️
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                هنوز فروشی ثبت نشده است.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $lowStockList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="list-group-item d-flex justify-content-between">
                                    <span><?php echo e($product->name); ?></span>
                                    <span class="badge bg-danger">
                                        <?php echo e($product->stock); ?>

                                    </span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="list-group-item">
                                    همه کالاها موجودی مناسبی دارند.
                                </div>
                            <?php endif; ?>
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels, 15, 512) ?>,
                    datasets: [{
                        label: 'فروش',
                        data: <?php echo json_encode($data, 15, 512) ?>,
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/dashboard/index.blade.php ENDPATH**/ ?>