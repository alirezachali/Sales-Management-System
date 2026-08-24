<nav class="top-navbar" dir="rtl">
    <div class="navbar-inner">

        
        <div class="navbar-section">

            
            <button type="button" class="nav-icon-btn" @click="toggleSidebar()"
                :class="{ 'is-active': sidebarCollapsed }" title="باز و بستن منو" aria-label="باز و بستن منو">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand" href="<?php echo e(route('dashboard')); ?>">
                <img class="nav-logo" src="<?php echo e(storeLogo()); ?>" alt="Logo">
                <span class="brand-name"><?php echo e(setting('store_name', 'فروشگاه')); ?></span>
            </a>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <div class="navbar-section">
                <?php $avatarUrl = auth()->user()->avatar_url; ?>

                <div class="user-menu" x-data="{ open: false }" @keydown.escape.window="open = false">

                    
                    <button type="button" class="user-menu-toggle" @click="open = !open"
                        :class="{ 'is-open': open }" aria-haspopup="true" :aria-expanded="open.toString()">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($avatarUrl): ?>
                            <img src="<?php echo e($avatarUrl); ?>" class="user-avatar-img" alt="avatar">
                        <?php else: ?>
                            <span class="user-avatar-fallback"><i class="bi bi-person"></i></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="user-menu-name d-none d-md-inline"><?php echo e(auth()->user()->name); ?></span>
                        <i class="bi bi-chevron-down user-menu-caret"></i>
                    </button>

                    
                    <div class="user-dropdown" x-show="open" x-cloak x-transition
                        @click.outside="open = false">

                        
                        <div class="user-dropdown-head">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($avatarUrl): ?>
                                <img src="<?php echo e($avatarUrl); ?>" class="dropdown-avatar" alt="avatar">
                            <?php else: ?>
                                <span class="dropdown-avatar dropdown-avatar-fallback">
                                    <i class="bi bi-person"></i>
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="dropdown-user-meta">
                                <div class="dropdown-user-name"><?php echo e(auth()->user()->name); ?></div>
                                <div class="dropdown-user-role">
                                    <?php echo e(auth()->user()->role?->display_name ?? 'کاربر'); ?>

                                </div>
                            </div>
                        </div>

                        
                        <div class="theme-switch">
                            <span class="theme-switch-label">حالت نمایش</span>
                            <div class="theme-switch-btns" role="group" aria-label="تغییر تم">
                                <button type="button" class="theme-btn" @click="setTheme('light')"
                                    :class="{ 'active': theme === 'light' }" title="حالت روشن"
                                    aria-label="حالت روشن">
                                    <i class="bi bi-sun-fill"></i>
                                </button>
                                <button type="button" class="theme-btn" @click="setTheme('dark')"
                                    :class="{ 'active': theme === 'dark' }" title="حالت تیره"
                                    aria-label="حالت تیره">
                                    <i class="bi bi-moon-stars-fill"></i>
                                </button>
                            </div>
                        </div>

                        <div class="dropdown-divider-line"></div>

                        
                        <a class="user-dropdown-item" href="#">
                            <i class="bi bi-person"></i>
                            <span>پروفایل</span>
                        </a>

                        
                        <a class="user-dropdown-item" href="<?php echo e(route('settings.index')); ?>">
                            <i class="bi bi-gear"></i>
                            <span>تنظیمات</span>
                        </a>

                        <div class="dropdown-divider-line"></div>

                        
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="user-dropdown-item logout-item">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>خروج از سیستم</span>
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
</nav>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/partials/navbar.blade.php ENDPATH**/ ?>