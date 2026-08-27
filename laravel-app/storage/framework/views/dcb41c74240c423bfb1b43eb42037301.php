<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="rtl">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    
    <script>
        (function () {
            try {
                var t = localStorage.getItem('app-theme') || 'dark';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>

    <!-- Title -->
    <title><?php echo $__env->yieldContent('title', setting('store_name')); ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(storeFavicon()); ?>">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- Custom CSS -->
    <link href="<?php echo e(asset('css/sidebar.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/navbar.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/roles.css')); ?>" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body x-data="{
        theme: (localStorage.getItem('app-theme') || 'dark'),
        sidebarCollapsed: (localStorage.getItem('app-sidebar-collapsed') === '1'),
        setTheme(t) {
            this.theme = t;
            localStorage.setItem('app-theme', t);
            document.documentElement.setAttribute('data-bs-theme', t);
        },
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('app-sidebar-collapsed', this.sidebarCollapsed ? '1' : '0');
        }
    }" x-init="document.documentElement.setAttribute('data-bs-theme', theme)">

<!-- Navbar -->
<?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="wrapper">

    <!-- Sidebar -->
    <?php echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Content -->
    <main class="content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

</div>

<!-- Footer -->



<?php echo $__env->yieldContent('scripts'); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/layouts/app.blade.php ENDPATH**/ ?>