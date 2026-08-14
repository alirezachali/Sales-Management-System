<?php $__env->startSection('title', 'مدیریت کاربران'); ?>
<?php $__env->startSection('content'); ?>

    <!-- Users Card Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">

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

        <div>
            <h3 class="mb-1">مدیریت کاربران</h3>
            <p class="text-muted mb-0">مدیریت کاربران سیستم</p>
        </div>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal"
            title="برای افزودن کاربر جدید به سیستم کلیک کنید">
            <i class="bi bi-plus-circle"></i>
            کاربر جدید
        </button>

    </div>

    <!-- Users Card Body -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">

                <!-- Users Table -->
                <table class="table table-hover align-middle mb-0">
                    <!-- Start Users Table Head -->
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>نام</th>
                            <th>نام کاربری</th>
                            <th>وضعیت</th>
                            <th>نقش</th>
                            <th>آخرین ورود</th>
                            <th width="180">عملیات</th>
                        </tr>
                    </thead>
                    <!-- End Users Table Head -->
                    <!-- Start Users Table Body -->
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($loop->iteration + ($users->currentPage() - 1) * $users->perPage()); ?></td>
                                <td><?php echo e($user->name); ?></td>
                                <td><?php echo e($user->username); ?></td>
                                <td>
                                    <?php if($user->is_active): ?>
                                        <span class="badge bg-success">
                                            فعال
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            غیرفعال
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($user->role?->display_name ?? '-'); ?>

                                </td>
                                <td>
                                    <?php echo e($user->last_login_at?->format('Y/m/d H:i') ?? '-'); ?>

                                </td>
                                <td>
                                    <!-- Edit User Button -->
                                    <button class="btn btn-sm btn-warning editUserBtn" data-id="<?php echo e($user->id); ?>"
                                        data-name="<?php echo e($user->name); ?>" data-username="<?php echo e($user->username); ?>"
                                        data-email="<?php echo e($user->email); ?>" data-phone="<?php echo e($user->phone); ?>"
                                        data-active="<?php echo e($user->is_active); ?>" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal" title="برای ویرایش این کاربر کلیک کنید">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <!-- Change Password User Button -->
                                    <button class="btn btn-sm btn-info changePasswordBtn" data-id="<?php echo e($user->id); ?>"
                                        data-name="<?php echo e($user->name); ?>" data-bs-toggle="modal"
                                        data-bs-target="#changePasswordModal"
                                        title="برای تغییر کلمه عبور این کاربر کلیک کنید">
                                        <i class="bi bi-key"></i>
                                    </button>
                                    <!-- Delete User Button -->
                                    <button class="btn btn-sm btn-danger deleteUserBtn" data-id="<?php echo e($user->id); ?>"
                                        data-name="<?php echo e($user->name); ?>" data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal" title="برای حذف این کاربر کلیک کنید">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    هیچ کاربری ثبت نشده است.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <!-- End Users Table Body -->
                </table>
                <!-- End Users Table -->

            </div>
        </div>
    </div>

    <div class="mt-3"><?php echo e($users->links()); ?></div>

    <!-- include External Modals File -->
    <?php echo $__env->make('users.modals.changepassword', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('users.modals.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('users.modals.delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('users.modals.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Start Edit User Modal Script
            document.querySelectorAll('.editUserBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('edit_name').value = this.dataset.name;
                    document.getElementById('edit_username').value = this.dataset.username;
                    document.getElementById('edit_email').value = this.dataset.email;
                    document.getElementById('edit_phone').value = this.dataset.phone;
                    document.getElementById('edit_active').checked = this.dataset.active == 1;
                    document.getElementById('editUserForm').action = '/users/' + this.dataset.id;
                    const modal = document.getElementById('editUserModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });
            // End Edit User Modal Script

            // Start Change Password User Modal Script
            document.querySelectorAll('.changePasswordBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('passwordUserName').textContent = this.dataset.name;
                    document.getElementById('changePasswordForm').action = '/users/' + this.dataset
                        .id +
                        '/password';
                    const modal = document.getElementById('changePasswordModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });
            // End Change Password User Modal Script

            // Start Delete User Modal Script
            document.querySelectorAll('.deleteUserBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('deleteUserName').textContent = this.dataset.name;
                    document.getElementById('deleteUserForm').action = '/users/' + this.dataset.id;
                    const modal = document.getElementById('deleteUserModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });
            // End Delete User Modal Script

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/users/index.blade.php ENDPATH**/ ?>