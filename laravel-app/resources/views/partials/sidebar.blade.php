<aside class="sidebar" id="sidebar" :class="{ 'collapsed': sidebarCollapsed }">
    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        <!-- داشبورد -->
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>داشبورد</span>
            </a>
        </li>
        <!-- محصولات -->
        <li>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>محصولات</span>
            </a>
        </li>
        <!-- فاکتور خرید -->
        <li>
            <a href="{{ route('purchase-invoices.index') }}"
                class="{{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-plus"></i>
                <span>ثبت فاکتور خرید</span>
            </a>
        </li>
        <!-- دسته بندی ها -->
        <li>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                <span>دسته‌بندی ها</span>
            </a>
        </li>
        <!-- صندوق فروش -->
        <li>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check"></i>
                <span>صندوق فروش</span>
            </a>
        </li>
        <!-- تنظیمات -->
        <li>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>تنظیمات</span>
            </a>
        </li>
        <!-- کاربران -->
        <li>
            <a href="{{ route('users.index') }}"
                class="{{ request()->routeIs('users.*') ? 'active' : '' }} {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-person-video"></i>
                <span>کاربران</span>
            </a>
        </li>
        <!-- مشتریان -->
        <li>
            <a href="{{ route('customers.index') }}"
                class="{{ request()->routeIs('customers.*') ? 'active' : '' }} {{ request()->routeIs('customer-roles.*') ? 'active' : '' }}">
                <i class="bi bi-person-standing-dress"></i>
                <span>باشگاه مشتریان</span>
            </a>
        </li>
        <!-- برندها -->
        <li>
            <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
                <i class="bi bi-bing"></i>
                <span>برندها</span>
            </a>
        </li>
        <!-- تامین کنندگان -->
        <li>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-bus-front-fill"></i>
                <span>تامین کنندگان</span>
            </a>
        </li>
        <!-- کارکنان -->
        <li>
            <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>مدیریت کارکنان</span>
            </a>
        </li>
        <!-- هزینه‌ها -->
        <li>
            <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>مدیریت هزینه‌ها</span>
            </a>
        </li>
        <!-- لیست کارها-->
        <li>
            <a href="{{ route('todos.index') }}" class="{{ request()->routeIs('todos.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                <span>لیست کارها</span>
            </a>
        </li>
        <!-- گزارش فروش-->
        <li>
            <a href="{{ route('reports.sales') }}" class="{{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data"></i>
                <span>گزارش فروش</span>
            </a>
        </li>
        <!-- گزارش خرید -->
        <li>
            <a href="{{ route('reports.purchases') }}" class="{{ request()->routeIs('reports.purchases') ? 'active' : '' }}">
                <i class="bi bi-archive"></i>
                <span>گزارش ورود/خروج کالا</span>
            </a>
        </li>
        <!-- گزارش مالی -->
        <li>
            <a href="{{ route('financial.index') }}" class="{{ request()->routeIs('financial.*') ? 'active' : '' }}">
                <i class="bi bi-bank"></i>
                <span>مدیریت مالی</span>
            </a>
        </li>
    </ul>
</aside>
