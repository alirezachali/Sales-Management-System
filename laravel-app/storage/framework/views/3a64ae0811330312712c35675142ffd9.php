<?php $__env->startSection('title', 'ورود به سیستم'); ?>
<?php $__env->startSection('content'); ?>

    <div class="login-card">

        <div class="text-center mb-4">
            <img src="<?php echo e(storeLogo()); ?>" class="login-logo" alt="Logo">
            <h3 class="mt-3 mb-1">
                <?php echo e(setting('store_name', 'سیستم مدیریت فروشگاه')); ?>

            </h3>
            <p class="text-secondary">
                ورود به پنل مدیریت
            </p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label class="form-label">
                    نام کاربری
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="username" class="form-control" autocomplete="username" autofocus required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    رمز عبور
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input id="password" type="password" name="password" class="form-control" autocomplete="current-password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        مرا به خاطر بسپار
                    </label>
                </div>
            </div>

            <button class="btn btn-primary w-100 login-btn">
                ورود به سیستم
            </button>

        </form>

        <div class="login-footer"> نسخه 1.0.0</div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const password = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        toggle.addEventListener('click', () => {
            if (password.type === 'password') {
                password.type = 'text';
                toggle.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                password.type = 'password';
                toggle.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/auth/login.blade.php ENDPATH**/ ?>