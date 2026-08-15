<?php $__env->startSection('title', 'مجوزهای نقش'); ?>
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

    <div class="page-header d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                مجوز های :
                <span class="badge bg-<?php echo e($role->color); ?>">
                    <?php echo e($role->name); ?>

                    <i class="<?php echo e($role->icon); ?>"></i>
                </span>
            </h3>
        </div>

        <div class="text-end">
            <button class="btn btn-success">
                <i class="bi bi-check-circle"></i>
                ذخیره تغییرات
            </button>

            <a href="<?php echo e(route('roles.index')); ?>"class="btn btn-secondary" title="بازگشت به صفحه لیست نقش ها">
                <i class="bi bi-arrow-right"></i>
                بازگشت
            </a>

        </div>

    </div>

    <form action="<?php echo e(route('roles.permissions.sync', $role)); ?>"method="POST">
        <?php echo csrf_field(); ?>

        <div class="permissions-grid">

            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="permission-column">

                    <div class="card shadow-sm">

                        <div class="card-header">
                            <strong>
                                <i class="bi <?php echo e($group->icon); ?>"></i>
                                <?php echo e($group->name); ?>

                            </strong>
                        </div>

                        <div class="card-body">

                            <div class="permissions-list">

                                <?php $__currentLoopData = $group->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check permission-item">

                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="<?php echo e($permission->id); ?>" id="permission<?php echo e($permission->id); ?>"
                                            <?php echo e($role->permissions->contains($permission->id) ? 'checked' : ''); ?>>

                                        <label class="form-check-label" for="permission<?php echo e($permission->id); ?>">
                                            <?php echo e($permission->display_name); ?>

                                        </label>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </div>

                        </div>

                    </div>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>

        <hr class="h-r">

        <div class="text-end">
            <button class="btn btn-success">
                <i class="bi bi-check-circle"></i>
                ذخیره تغییرات
            </button>
        </div>

    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/roles/permissions.blade.php ENDPATH**/ ?>