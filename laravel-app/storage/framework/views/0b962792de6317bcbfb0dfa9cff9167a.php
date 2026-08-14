<?php $__env->startSection('title', 'گردش انبار'); ?>
<?php $__env->startSection('content'); ?>

    <!-- Success Alert Section -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    <?php endif; ?>

    <!-- Error Alert Section -->
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    <?php endif; ?>

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="card-title">
                گردش کالا:
                <?php echo e($product->name); ?>

            </h3>
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary" title="بازگشت به صفحه لیست محصولات">
                بازگشت
            </a>
        </div>

        <div class="card-body">

            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="alert alert-info">
                    موجودی فعلی:
                    <strong>
                        <?php echo e($product->stock); ?>

                    </strong>
                    <?php echo e($product->unit); ?>

                </div>
                <div class="export-btn d-flex justify-content-evenly">
                    <button type="button" class="btn btn-sm btn-light print-label-btn" data-id="<?php echo e($product->id); ?>"
                        title="گرفتن خروجی اکسل">
                        <i class="bi bi-filetype-xls"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light print-label-btn" data-id="<?php echo e($product->id); ?>"
                        title="گرفتن خروجی CSV">
                        <i class="bi bi-filetype-csv"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter">

                    <thead>
                        <tr>
                            <th> تاریخ</th>
                            <th> نوع عملیات</th>
                            <th> مقدار</th>
                            <th> توضیحات</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <!-- تاریخ -->
                                <td><?php echo e($movement->created_at); ?></td>
                                <!-- نوع عملیات -->
                                <td>
                                    <?php switch($movement->type):
                                        case ('initial'): ?>
                                            <span class="badge bg-info">
                                                موجودی اولیه
                                            </span>
                                        <?php break; ?>

                                        <?php case ('purchase'): ?>
                                            <span class="badge bg-success">
                                                خرید
                                            </span>
                                        <?php break; ?>

                                        <?php case ('sale'): ?>
                                            <span class="badge bg-danger">
                                                فروش
                                            </span>
                                        <?php break; ?>

                                        <?php case ('adjust'): ?>
                                            <span class="badge bg-warning">
                                                اصلاح
                                            </span>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <!-- مقدار -->
                                <td><?php echo e($movement->quantity); ?></td>
                                <!-- توضیحات -->
                                <td><?php echo e($movement->description); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>

                </table>

            </div>

            <div class="mt-3"><?php echo e($movements->links()); ?></div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/products/stock.blade.php ENDPATH**/ ?>