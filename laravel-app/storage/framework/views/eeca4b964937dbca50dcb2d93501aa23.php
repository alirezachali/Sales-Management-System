<?php $__env->startSection('title', 'خروج کالا از انبار'); ?>
<?php $__env->startSection('content'); ?>

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                خروج کالا از انبار
            </h3>
        </div>

        <div class="card-body">

            <div class="alert alert-warning">
                کالا:
                <strong>
                    <?php echo e($product->name); ?>

                </strong>
                <br>
                موجودی فعلی:
                <strong>
                    <?php echo e($product->stock); ?>

                </strong>
                <?php echo e($product->unit); ?>

            </div>

            <form method="POST" action="<?php echo e(route('products.sale.store', $product)); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label">
                        تعداد خروج
                    </label>
                    <input type="number" step="0.001" name="quantity" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        توضیحات
                    </label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <button class="btn btn-danger">
                    ثبت خروج کالا
                </button>

            </form>

        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/products/sale-create.blade.php ENDPATH**/ ?>