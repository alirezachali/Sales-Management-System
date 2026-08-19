<?php $__env->startSection('title', 'مدیریت کاربران'); ?>
<?php $__env->startSection('content'); ?>

    <div class="container-fluid">

        <!-- نمایش پیغام های موفقیت -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- نمایش پیغام های خطا -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- هدر کارت کاربران -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    مدیریت کاربران
                </h3>
                <small class="text-muted">
                    مدیریت کاربران سیستم
                </small>
            </div>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal"
                title="برای افزودن کاربر جدید به سیستم کلیک کنید">
                <i class="bi bi-plus-circle"></i>
                افزودن کاربر
            </button>

        </div>


        
        <div class="card glass-card mb-4">

            <!-- Search Card Body -->
            <div class="card-body">

                <form>

                    <div class="row">

                        <div class="col-lg-5">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>"
                                    placeholder="جستجو بر اساس نام یا موبایل">
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <button class="btn btn-primary w-100" title="برای شروع جستجو کلیک کنید">
                                جستجو
                            </button>
                        </div>

                        <div class="col-lg-5 text-end">
                            <span class="badge bg-info fs-6">
                                تعداد کاربران :
                                2
                            </span>
                        </div>

                    </div>
                </form>

            </div>
        </div>


        <!-- بدنه ی کارت کاربران -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <!-- جدول کاربران -->
                    <table class="table table-bordered table-hover align-middle">
                        <!-- هدر جدول کاربران -->
                        <thead>
                            <tr>
                                <th width="60">ردیف</th>
                                <th>نام</th>
                                <th>نام کاربری</th>
                                <th width="100">وضعیت</th>
                                <th width="122">نقش</th>
                                <th width="150">آخرین ورود</th>
                                <th width="130">عملیات</th>
                            </tr>
                        </thead>

                        <!-- بدنه ی جدول کاربران -->
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr>
                                    <!-- ردیف -->
                                    <td><?php echo e($loop->iteration + ($users->currentPage() - 1) * $users->perPage()); ?></td>
                                    <!-- نام -->
                                    <td><?php echo e($user->name); ?></td>
                                    <!-- نام کاربری -->
                                    <td><?php echo e($user->username); ?></td>
                                    <!-- وضعیت -->
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->is_active): ?>
                                            <span class="badge bg-success">
                                                فعال
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                غیرفعال
                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <!-- نقش -->
                                    <td><?php echo e($user->role?->display_name ?? '-'); ?></td>
                                    <!-- آخرین ورود-->
                                    <td><?php echo e($user->last_login_at?->format('Y/m/d H:i') ?? '-'); ?></td>
                                    <!-- عملیات -->
                                    <td>
                                        <!-- دکمه ویرایش مشخصات یک کاربر -->
                                        <button class="btn btn-sm btn-warning editUserBtn" data-id="<?php echo e($user->id); ?>"
                                            data-name="<?php echo e($user->name); ?>" data-username="<?php echo e($user->username); ?>"
                                            data-email="<?php echo e($user->email); ?>" data-phone="<?php echo e($user->phone); ?>"
                                            data-role="<?php echo e($user->role_id); ?>" data-active="<?php echo e($user->is_active); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal"
                                            title="برای ویرایش این کاربر کلیک کنید">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- دکمه تغییر رمز ورود یک کاربر -->
                                        <button class="btn btn-sm btn-info changePasswordBtn" data-id="<?php echo e($user->id); ?>"
                                            data-name="<?php echo e($user->name); ?>" data-bs-toggle="modal"
                                            data-bs-target="#changePasswordModal"
                                            title="برای تغییر کلمه عبور این کاربر کلیک کنید">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <!-- دکمه حذف یک کاربر -->
                                        <button class="btn btn-sm btn-danger deleteUserBtn" data-id="<?php echo e($user->id); ?>"
                                            data-name="<?php echo e($user->name); ?>" data-bs-toggle="modal"
                                            data-bs-target="#deleteUserModal" title="برای حذف این کاربر کلیک کنید">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        هیچ کاربری ثبت نشده است.
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="mt-3"><?php echo e($users->links()); ?></div>

    <!-- وارد کردن فایل مودال ها -->
    <?php echo $__env->make('users.modals.changepassword', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('users.modals.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('users.modals.delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('users.modals.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // اسکریپت مربوط به مودال ویرایش مشخصات یک کاربر
            document.querySelectorAll('.editUserBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('edit_name').value = this.dataset.name;
                    document.getElementById('edit_username').value = this.dataset.username;
                    document.getElementById('edit_email').value = this.dataset.email;
                    document.getElementById('edit_phone').value = this.dataset.phone;
                    document.getElementById('edit_role').value = this.dataset.role;
                    document.getElementById('edit_active').checked = this.dataset.active == 1;
                    document.getElementById('editUserForm').action = '/users/' + this.dataset.id;
                    const modal = document.getElementById('editUserModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });

            // اسکریپت مربوط به مودال تغییر رمز ورود یک کاربر
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

            // اسکریپت مربوط به مودال حذف یک کاربر
            document.querySelectorAll('.deleteUserBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('deleteUserName').textContent = this.dataset.name;
                    document.getElementById('deleteUserForm').action = '/users/' + this.dataset.id;
                    const modal = document.getElementById('deleteUserModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/users/index.blade.php ENDPATH**/ ?>