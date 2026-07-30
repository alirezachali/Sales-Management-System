<aside class="sidebar" id="sidebar">

    <ul class="sidebar-menu">

        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>داشبورد</span>
            </a>
        </li>

        <li>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>کالاها</span>
            </a>
        </li>

        <li>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                <span>دسته‌بندی</span>
            </a>
        </li>

        <li>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check"></i>
                <span>صندوق فروش</span>
            </a>
        </li>

        <li>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>تنظیمات</span>
            </a>
        </li>

        <li>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>کاربران</span>
            </a>
        </li>

        <li>
            <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i>
                <span>نقش‌ ها</span>
            </a>
        </li>

    </ul>

</aside>