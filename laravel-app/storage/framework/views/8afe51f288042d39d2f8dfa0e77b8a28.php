<nav class="navbar navbar-expand-lg top-navbar">
    <div class="container-fluid">
        <!-- List Button -->
        <button class="btn me-3" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
        <!-- Brand Logo & Name -->
        <a class="navbar-brand d-flex align-items-center" href="<?php echo e(route('dashboard')); ?>">
            <img src="<?php echo e(storeLogo()); ?>" alt="Logo" height="45">
            <!-- Store Name -->
            <span class="ms-2 fw-bold">
                <?php echo e(setting('store_name', '')); ?>

            </span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <!-- Live Clock -->
            <span class="me-4" id="liveClock"></span>
            <?php if(auth()->guard()->check()): ?>
                <!-- User Name & Profile icon -->
                <span class="me-4">
                    <!-- Profile icon -->
                    <i class="bi bi-person-circle"></i>
                    <!-- User name -->
                    <?php echo e(auth()->user()->name); ?>

                </span>
                <!-- Logout Request Form -->
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <!-- Logout Button -->
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i>
                        خروج
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/partials/navbar.blade.php ENDPATH**/ ?>