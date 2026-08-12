<?php $__env->startSection('title', 'تنظیمات سیستم'); ?>
<?php $__env->startSection('content'); ?>

    <div class="container-fluid">
        <div class="card shadow-sm border-0">

            <!-- Success Alert Section -->
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" title="یستن"></button>
                </div>
            <?php endif; ?>

            <!-- Error Alert Section -->
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" title="یستن"></button>
                </div>
            <?php endif; ?>

            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="bi bi-gear-fill text-primary"></i>
                        تنظیمات سیستم
                    </h4>
                    <small class="text-muted">
                        مدیریت اطلاعات فروشگاه و تنظیمات نرم افزار
                    </small>
                </div>
                <button type="submit" form="settingsForm" class="btn btn-success">
                    <i class="bi bi-check-circle"></i>
                    ذخیره تنظیمات
                </button>
            </div>

            <div class="card-body">

                <form id="settingsForm" action="<?php echo e(route('settings.update')); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <ul class="nav nav-tabs mb-4" id="settingsTabs">

                        <li class="nav-item">
                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#store">
                                <i class="bi bi-shop"></i>
                                اطلاعات فروشگاه
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#sales">
                                <i class="bi bi-receipt"></i>
                                فروش
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#print">
                                <i class="bi bi-printer"></i>
                                چاپ
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#barcode">
                                <i class="bi bi-upc-scan"></i>
                                بارکد و لیبل
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#system">
                                <i class="bi bi-cpu"></i>
                                سیستم
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#backup">
                                <i class="bi bi-database"></i>
                                پشتیبان گیری
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="store">
                            <?php echo $__env->make('settings.tabs.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <div class="tab-pane fade" id="sales">
                            <?php echo $__env->make('settings.tabs.sales', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <div class="tab-pane fade" id="print">
                            <?php echo $__env->make('settings.tabs.print', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <div class="tab-pane fade" id="barcode">
                            <?php echo $__env->make('settings.tabs.barcode', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                        <div class="tab-pane fade" id="system">
                            <?php echo $__env->make('settings.tabs.system', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                        
                        <div class="tab-pane fade" id="backup">
                            <?php echo $__env->make('settings.tabs.backup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // بازیابی آخرین تب باز شده
            const activeTab = localStorage.getItem('settings-tab');
            if (activeTab) {
                const trigger = document.querySelector(
                    `[data-bs-target="${activeTab}"]`
                );
                if (trigger) {
                    bootstrap.Tab.getOrCreateInstance(trigger).show();
                }
            }

            // ذخیره تب فعال
            document.querySelectorAll('#settingsTabs .nav-link').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function() {
                    localStorage.setItem(
                        'settings-tab',
                        this.dataset.bsTarget
                    );
                });
            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/settings/index.blade.php ENDPATH**/ ?>