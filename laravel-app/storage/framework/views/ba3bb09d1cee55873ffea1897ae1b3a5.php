
<?php $__env->startSection('title', 'مدیریت برندها'); ?>
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
                    مدیریت برندها
                </h3>
                <small class="text-muted">
                    مدیریت اطلاعات برندهای فروشگاه
                </small>
            </div>

            <!-- Add New Customer Button -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCustomerModal"
                title="افزودن مشتری جدید به سیستم">
                <i class="bi bi-plus-circle"></i>
                افزودن برند
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
                                تعداد برندها :

                            </span>
                        </div>

                    </div>
                </form>

            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/brands/index.blade.php ENDPATH**/ ?>