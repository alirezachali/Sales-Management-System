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
        
            <?php if(auth()->guard()->check()): ?>

                <div class="dropdown user-menu">

                    <button class="btn user-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle user-avatar"></i>
                    </button>



                    <ul class="dropdown-menu dropdown-menu-end user-dropdown">

                        <p class="user-name">
                            <?php echo e(auth()->user()->name); ?>

                        </p>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <!-- پروفایل -->
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person"></i>
                                <span>پروفایل</span>
                            </a>
                        </li>


                        <!-- تنظیمات -->
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('settings.index')); ?>">
                                <i class="bi bi-gear"></i>
                                <span>تنظیمات</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('settings.index')); ?>">
                                <i class="bi bi-gear"></i>
                                <span>'گزینه سوم'</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('settings.index')); ?>">
                                <i class="bi bi-gear"></i>
                                <span>گزینه چهارم</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('settings.index')); ?>">
                                <i class="bi bi-gear"></i>
                                <span>گزینه پنجم</span>
                            </a>
                        </li>


                        <li>
                            <hr class="dropdown-divider">
                        </li>


                        <!-- خروج -->
                        <li>

                            <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">

                                <?php echo csrf_field(); ?>

                                <button type="submit" class="dropdown-item logout-item">

                                    <i class="bi bi-box-arrow-right"></i>

                                    <span>خروج از سیستم</span>

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            <?php endif; ?>




        </div>
    </div>
</nav>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/partials/navbar.blade.php ENDPATH**/ ?>