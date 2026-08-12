<aside class="sidebar" id="sidebar">
    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        <!-- داشبورد -->
        <li>
            <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-speedometer2"></i>
                <span>داشبورد</span>
            </a>
        </li>
        <!-- محصولات -->
        <li>
            <a href="<?php echo e(route('products.index')); ?>" class="<?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>">
                <i class="bi bi-box-seam"></i>
                <span>محصولات</span>
            </a>
        </li>
        <!-- دسته بندی ها -->
        <li>
            <a href="<?php echo e(route('categories.index')); ?>" class="<?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
                <i class="bi bi-grid"></i>
                <span>دسته‌بندی ها</span>
            </a>
        </li>
        <!-- صندوق فروش -->
        <li>
            <a href="<?php echo e(route('pos.index')); ?>" class="<?php echo e(request()->routeIs('pos.*') ? 'active' : ''); ?>">
                <i class="bi bi-cart-check"></i>
                <span>صندوق فروش</span>
            </a>
        </li>
        <!-- تنظیمات -->
        <li>
            <a href="<?php echo e(route('settings.index')); ?>" class="<?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                <i class="bi bi-gear"></i>
                <span>تنظیمات</span>
            </a>
        </li>
        <!-- کاربران -->
        <li>
            <a href="<?php echo e(route('users.index')); ?>" class="<?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                <i class="bi bi-people"></i>
                <span>کاربران</span>
            </a>
        </li>
        <!-- نقش های کاربران -->
        <li>
            <a href="<?php echo e(route('roles.index')); ?>" class="<?php echo e(request()->routeIs('roles.*') ? 'active' : ''); ?>">
                <i class="bi bi-shield-lock"></i>
                <span>نقش‌ های کاربران</span>
            </a>
        </li>
        <!-- مشتریان -->
        <li>
            <a href="<?php echo e(route('customers.index')); ?>" class="<?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>">
                <i class="bi bi-people"></i>
                <span>مشتریان</span>
            </a>
        </li>
    </ul>
</aside>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>