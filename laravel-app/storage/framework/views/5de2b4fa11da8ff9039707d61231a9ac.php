<?php $__env->startSection('title', 'مدیریت مشتریان'); ?>
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

        
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    مدیریت مشتریان
                </h3>
                <small class="text-muted">
                    مدیریت اطلاعات مشتریان فروشگاه
                </small>
            </div>

            <!-- Add New Customer Button -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCustomerModal"
                title="افزودن مشتری جدید به سیستم">
                <i class="bi bi-plus-circle"></i>
                افزودن مشتری
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
                            <span class="badge bg-primary fs-6">
                                تعداد مشتریان :
                                <?php echo e($customers->total()); ?>

                            </span>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        
        <div class="card glass-card">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <!-- جدول مشتریان -->
                    <table class="table table-bordered table-hover align-middle">

                        <!-- Start Customer Table Head -->
                        <thead>
                            <tr>
                                <th width="60"> ردیف</th>
                                <th> نام مشتری</th>
                                <th> موبایل</th>
                                <th> نقش</th>
                                <th> تعداد خرید</th>
                                <th> مجموع خرید</th>
                                <th> وضعیت</th>
                                <th width="140"> عملیات</th>
                            </tr>
                        </thead>
                        <!-- End Customer Table Head -->

                        <!-- Start Customer Table Body -->
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <!-- ردیف -->
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <!-- نام مشتری -->
                                    <td>
                                        <div class="fw-bold">
                                            <?php echo e($customer->full_name); ?>

                                        </div>
                                    </td>
                                    <!-- موبایل -->
                                    <td><?php echo e($customer->mobile); ?></td>
                                    <!-- نقش -->
                                    <td>
                                        <?php if($customer->role): ?>
                                            <span class="badge bg-<?php echo e($customer->role->color); ?>">
                                                <i class="bi <?php echo e($customer->role->icon); ?>"></i>
                                                <?php echo e($customer->role->name); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                بدون نقش
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- تعداد خرید -->
                                    <td><?php echo e(number_format($customer->purchase_count)); ?></td>
                                    <!-- مجموع خرید -->
                                    <td><?php echo e(number_format($customer->total_purchase_amount)); ?>

                                        <span>
                                            <!-- واحد پولی از دیتابیس -->
                                            <?php echo e(setting('currency', '')); ?>

                                        </span>
                                    </td>
                                    <!-- وضعیت -->
                                    <td>
                                        <?php if($customer->is_active): ?>
                                            <span class="badge bg-success">
                                                فعال
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                غیرفعال
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- عملیات -->
                                    <td>
                                        <!-- Edit Customer Button -->
                                        <button class="btn btn-sm btn-warning editCustomer" data-id="<?php echo e($customer->id); ?>"
                                            data-first_name="<?php echo e($customer->first_name); ?>"
                                            data-last_name="<?php echo e($customer->last_name); ?>"
                                            data-mobile="<?php echo e($customer->mobile); ?>" data-phone="<?php echo e($customer->phone); ?>"
                                            data-role="<?php echo e($customer->customer_role_id); ?>"
                                            data-active="<?php echo e($customer->is_active); ?>" data-notes="<?php echo e($customer->notes); ?>"
                                            data-bs-toggle="modal" data-bs-target="#editCustomerModal"
                                            title="برای ویرایش این مشتری کلیک کنید">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- Delete Customer Button -->
                                        <button class="btn btn-sm btn-danger deleteCustomer" data-id="<?php echo e($customer->id); ?>"
                                            data-bs-toggle="modal" data-bs-target="#deleteCustomerModal"
                                            title="برای حذف این مشتری کلیک کنید">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </td>
                                </tr>

                                <!-- اگر مشتری در دیتابیس وجود نداشته باشد اطلاعات زیر را نمایش میدهد -->
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <br>
                                        مشتری‌ای ثبت نشده است.
                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                        <!-- End Customer Table Body -->
                    </table>
                    <!-- End Customer Table -->
                </div>
            </div>

            <!-- Start Customer Card Footer -->
            <div class="card-footer">
                <?php echo e($customers->links()); ?>

            </div>
            <!-- End Customer Card Footer -->

        </div>
    </div>

    <!-- include External Modals File -->
    <?php echo $__env->make('customers.modals.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('customers.modals.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('customers.modals.delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {

            // Edit Customer
            $('.editCustomer').on('click', function() {
                let id = $(this).data('id');
                $('#editCustomerForm').attr('action', '/customers/' + id);
                $('#edit_first_name').val($(this).data('first_name'));
                $('#edit_last_name').val($(this).data('last_name'));
                $('#edit_mobile').val($(this).data('mobile'));
                $('#edit_phone').val($(this).data('phone'));
                $('#edit_customer_role_id').val($(this).data('role'));
                $('#edit_is_active').val($(this).data('active'));
                $('#edit_notes').val($(this).data('notes'));
                let modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
                modal.show();
            });

            // Delete Customer
            $('.deleteCustomer').on('click', function() {
                let id = $(this).data('id');
                $('#deleteCustomerForm').attr('action', '/customers/' + id);
                let modal = new bootstrap.Modal(document.getElementById('deleteCustomerModal'));
                modal.show();
            });

            // Fix Bootstrap Backdrop
            $('.modal').on('hidden.bs.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css({
                    overflow: '',
                    paddingRight: ''
                });
            });

        });
    </script>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/customers/index.blade.php ENDPATH**/ ?>