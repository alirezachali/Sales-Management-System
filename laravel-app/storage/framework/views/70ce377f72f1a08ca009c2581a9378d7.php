<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title><?php echo $__env->yieldContent('title', setting('store_name')); ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(storeFavicon()); ?>">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- Custom CSS -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/sidebar.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/navbar.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/pos.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/label-print.css')); ?>" rel="stylesheet">
    <!-- Bootstrap CDN CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body data-bs-theme="dark">
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
<?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!-- Bootstrap CDN JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<?php echo $__env->yieldContent('scripts'); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/layouts/app.blade.php ENDPATH**/ ?>