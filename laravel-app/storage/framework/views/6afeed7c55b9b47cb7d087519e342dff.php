<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title><?php echo $__env->yieldContent('title', 'ورود به سیستم'); ?></title>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(storeFavicon()); ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/auth.css')); ?>">
    <!-- Bootstrap CDN CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body data-bs-theme="dark">
    <div class="auth-wrapper">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <!-- Bootstrap CDN JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/layouts/auth.blade.php ENDPATH**/ ?>