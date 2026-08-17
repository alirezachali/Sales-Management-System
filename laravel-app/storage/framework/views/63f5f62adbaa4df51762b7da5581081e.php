
<?php $__env->startSection('title', 'مدیریت تامین کنندگان'); ?>
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

        
        <div class="page-header d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    تامین‌کنندگان
                </h3>
                <small class="text-muted">
                    مدیریت اطلاعات تامین کنندگان فروشگاه
                </small>
            </div>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplierModal"
                title="برای اضافه کردن تامین کننده جدید کلیک کنید">
                <i class="bi bi-plus-lg"></i>
                افزودن تامین‌کننده
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
                                تعداد تامین کنندگان :
                                1
                            </span>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card glass-card">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th width="70">شناسه</th>
                                <th>نام</th>
                                <th width="130">موبایل</th>
                                <th width="150">شهر</th>
                                <th width="70">نوع</th>
                                <th width="90">وضعیت</th>
                                <th width="90">عملیات</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($supplier->code); ?></td>
                                    <td><?php echo e($supplier->name); ?></td>
                                    <td><?php echo e($supplier->mobile); ?></td>
                                    <td><?php echo e($supplier->city ?? '-'); ?></td>
                                    <td><?php echo e($supplier->type === 'company' ? 'حقوقی' : 'حقیقی'); ?></td>
                                    <td>
                                        <?php if($supplier->is_active): ?>
                                            <span class="badge bg-success">فعال</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">غیرفعال</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editSupplierModal<?php echo e($supplier->id); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteSupplierModal<?php echo e($supplier->id); ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                
                                <div class="modal fade" id="editSupplierModal<?php echo e($supplier->id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form action="<?php echo e(route('suppliers.update', $supplier)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">ویرایش تامین‌کننده: <?php echo e($supplier->name); ?>

                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php echo $__env->make('suppliers._form', [
                                                        'supplier' => $supplier,
                                                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">انصراف</button>
                                                    <button type="submit" class="btn btn-primary">ذخیره
                                                        تغییرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="modal fade" id="deleteSupplierModal<?php echo e($supplier->id); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">حذف تامین‌کننده</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    آیا از حذف تامین‌کننده «<?php echo e($supplier->name); ?>» مطمئن هستید؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">انصراف</button>
                                                    <button type="submit" class="btn btn-danger">حذف</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">هیچ تامین‌کننده‌ای ثبت نشده
                                        است.
                                    </td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <?php echo e($suppliers->links()); ?>

            </div>

        </div>

    </div>

    
    <div class="modal fade" id="createSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" dir="rtl">
                <form action="<?php echo e(route('suppliers.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title">افزودن تامین‌کننده جدید</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php echo $__env->make('suppliers._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-primary">ثبت تامین‌کننده</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <?php if($errors->any()): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalEl = document.getElementById('createSupplierModal');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
        </script>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/suppliers/index.blade.php ENDPATH**/ ?>