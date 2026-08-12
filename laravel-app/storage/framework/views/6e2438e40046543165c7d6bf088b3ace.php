<nav class="navbar navbar-expand-lg top-navbar">
    <div class="container-fluid">
        <!-- دکمه منو همبرگری -->
        <button class="btn me-3" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
        
        <a class="navbar-brand d-flex align-items-center" href="<?php echo e(route('dashboard')); ?>">
            <!-- لوگو فروشگاه -->
            <img src="<?php echo e(storeLogo()); ?>" alt="Logo" height="45">
            <!-- نام فروشگاه -->
            <span class="ms-2 fw-bold">
                <?php echo e(setting('store_name', '')); ?>

            </span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <!-- ساعت -->
            <span class="me-4" id="liveClock"></span>
            <?php if(auth()->guard()->check()): ?>
                
                <span class="me-4">
                    <!-- تصویر پروفایل -->
                    <i class="bi bi-person-circle"></i>
                    <!-- نام کاربر -->
                    <?php echo e(auth()->user()->name); ?>

                </span>
                
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <!-- دکمه خروج از سیستم -->
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i>
                        خروج
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/partials/navbar.blade.php ENDPATH**/ ?>