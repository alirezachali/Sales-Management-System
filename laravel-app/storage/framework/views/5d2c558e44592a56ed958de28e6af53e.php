<?php $__env->startSection('title', 'مدیریت نقش‌ها'); ?>
<?php $__env->startSection('content'); ?>

    <div class="container-fluid">
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

        <!-- Start Role Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    مدیریت نقش‌ها
                </h3>
                <small class="text-muted">
                    مدیریت نقش‌های کاربران سیستم
                </small>
            </div>
            
            <!-- Add New Role Button  -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal"
                title="برای افزودن نقش جدید به سیستم کلیک کنید">
                <i class="bi bi-plus-circle"></i>
                افزودن نقش جدید
            </button>
        </div>
        <!-- End Role Page Header -->

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <!-- Start Role Table -->
                    <table class="table table-bordered table-hover align-middle">

                        <!-- Role Table Head -->
                        <thead>
                            <tr>
                                <th width="50">ردیف</th>
                                <th>نام نقش</th>
                                <th width="100">شناسه</th>
                                <th>توضیحات</th>
                                <th width="105">تعداد کاربران</th>
                                <th width="130">عملیات</th>
                            </tr>
                        </thead>

                        <!-- Start Role Table Body -->
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <!-- ردیف -->
                                    <td>
                                        <?php echo e($loop->iteration + ($roles->currentPage() - 1) * $roles->perPage()); ?>

                                    </td>
                                    <!-- نام نقش -->
                                    <td>
                                        <?php echo e($role->display_name); ?>

                                    </td>
                                    <!-- شناسه -->
                                    <td>
                                        <span class="badge bg-<?php echo e($role->color); ?>">
                                            <?php echo e($role->name); ?>

                                            <i class="<?php echo e($role->icon); ?>"></i>
                                        </span>
                                    </td>
                                    <!-- توضیحات -->
                                    <td>
                                        <?php echo e($role->description); ?>

                                    </td>
                                    <!-- تعداد کاربران -->
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo e($role->users()->count()); ?>

                                        </span>
                                    </td>
                                    <!-- عملیات -->
                                    <td>
                                        <!-- دکمه ویرایش یک نقش -->
                                        <button class="btn btn-sm btn-warning editRoleBtn" data-id="<?php echo e($role->id); ?>"
                                            data-display_name="<?php echo e($role->display_name); ?>" data-name="<?php echo e($role->name); ?>"
                                            data-description="<?php echo e($role->description); ?>" data-bs-toggle="modal"
                                            data-bs-target="#editRoleModal" title="برای ویرانش این نقش کلیک کنید">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- دکمه حذف یک نقش -->
                                        <button class="btn btn-sm btn-danger deleteRoleBtn" data-id="<?php echo e($role->id); ?>"
                                            data-name="<?php echo e($role->name); ?>" data-bs-toggle="modal"
                                            data-bs-target="#deleteRoleModal" title="برای حذف این نقش کلیک کنید">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <!-- دکمه ویرایش مجوزهای یک نقش -->
                                        <a href="<?php echo e(route('roles.permissions', $role)); ?>" class="btn btn-sm btn-info"
                                            title="برای ویرایش مجوز های این نقش کلیک کنید">
                                            <i class="bi bi-shield-lock"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- اگر اطلاعاتی در دیتابیس وجود نداشت اطلاعات زیر را نمایش میدهد -->
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        هیچ نقشی ثبت نشده است.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <!-- End Role Table Body -->
                    </table>
                    <!-- End Role Table -->
                </div>
            </div>
        </div>

        <div class="mt-3"><?php echo e($roles->links()); ?></div>
    </div>

    <?php echo $__env->make('roles.modals.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('roles.modals.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('roles.modals.delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // اسکریپت مربوط به مودال ویرایش یک نقش
        document.querySelectorAll('.editRoleBtn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_name').value = this.dataset.display_name;
                document.getElementById('edit_slug').value = this.dataset.name;
                document.getElementById('edit_description').value = this.dataset.description;
                document.getElementById('editRoleForm').action = '/roles/' + this.dataset.id;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editRoleModal')).show();
            });
        });

        // اسکریپت مربوط به مودال حذف یک نقش
        document.querySelectorAll('.deleteRoleBtn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('deleteRoleName').textContent = this.dataset.name;
                document.getElementById('deleteRoleForm').action = '/roles/' + this.dataset.id;
                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById('deleteRoleModal')).show();
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/roles/index.blade.php ENDPATH**/ ?>