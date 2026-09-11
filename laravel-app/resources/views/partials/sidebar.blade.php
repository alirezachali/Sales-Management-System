<aside class="sidebar" id="sidebar" :class="{ 'collapsed': sidebarCollapsed }">
    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
        
        @php $dashboardRoute = auth()->user()->dashboardRouteName(); @endphp
        @if ($dashboardRoute !== 'dashboard')
        <!-- داشبورد اختصاصی نقش کاربر جاری (صندوقدار/حسابدار/انباردار) -->
        <li>
            <a href="{{ route($dashboardRoute) }}" class="{{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>داشبورد</span>
            </a>
        </li>
        @else
        @can('dashboard.view')
        <!-- داشبورد -->
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>داشبورد</span>
            </a>
        </li>
        @endcan
        @endif

        @can('financial.view')
        <!-- گزارش مالی -->
        <li>
            <a href="{{ route('financial.index') }}" class="{{ request()->routeIs('financial.*') ? 'active' : '' }}">
                <i class="bi bi-bank"></i>
                <span>مدیریت مالی</span>
            </a>
        </li>
        @endcan

        @can('products.view')
        <!-- محصولات -->
        <li>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>محصولات</span>
            </a>
        </li>
        @endcan

        @can('purchases.view')
        <!-- فاکتور خرید -->
        <li>
            <a href="{{ route('purchase-invoices.index') }}" class="{{ request()->routeIs('purchase-invoices.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-plus"></i>
                <span>ثبت فاکتور خرید</span>
            </a>
        </li>
        @endcan

        @can('categories.view')
        <!-- دسته بندی ها -->
        <li>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="bi bi-grid"></i>
                <span>دسته‌بندی ها</span>
            </a>
        </li>
        @endcan

        @can('pos.view')
        <!-- صندوق فروش -->
        <li>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="bi bi-cart-check"></i>
                <span>صندوق فروش</span>
            </a>
        </li>
        @endcan

        {{-- @can('settings.view')
        <!-- تنظیمات -->
        <li>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>تنظیمات</span>
            </a>
        </li>
        @endcan --}}

        @can('users.view')
        <!-- کاربران -->
        <li>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }} {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-person-video"></i>
                <span>کاربران</span>
            </a>
        </li>
        @endcan

        @can('customers.view')
        <!-- مشتریان -->
        <li>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }} {{ request()->routeIs('customer-roles.*') ? 'active' : '' }}">
                <i class="bi bi-person-standing-dress"></i>
                <span>باشگاه مشتریان</span>
            </a>
        </li>
        @endcan

        @can('brands.view')
        <!-- برندها -->
        <li>
            <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
                <i class="bi bi-bing"></i>
                <span>برندها</span>
            </a>
        </li>
        @endcan

        @can('suppliers.view')
        <!-- تامین کنندگان -->
        <li>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-bus-front-fill"></i>
                <span>تامین کنندگان</span>
            </a>
        </li>
        @endcan

        @can('employees.view')
        <!-- کارکنان -->
        <li>
            <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>مدیریت کارکنان</span>
            </a>
        </li>
        @endcan

        @can('expenses.view')
        <!-- هزینه‌ها -->
        <li>
            <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>مدیریت هزینه‌ها</span>
            </a>
        </li>
        @endcan

        @can('todos.view')
        <!-- لیست کارها-->
        <li>
            <a href="{{ route('todos.index') }}" class="{{ request()->routeIs('todos.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                <span>لیست کارها</span>
            </a>
        </li>
        @endcan

        @can('reports.sales')
        <!-- گزارش فروش-->
        <li>
            <a href="{{ route('reports.sales') }}" class="{{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data"></i>
                <span>گزارش فروش</span>
            </a>
        </li>
        @endcan

        @can('reports.purchases')
        <!-- گزارش فاکتورهای خرید-->
        <li>
            <a href="{{ route('reports.purchases') }}" class="{{ request()->routeIs('reports.purchases') ? 'active' : '' }}">
                <i class="bi bi-archive"></i>
                <span>گزارش خرید</span>
            </a>
        </li>
        @endcan

        @can('reports.view')
        <!-- گزارش ورود|خروج کالا-->
        <li>
            <a href="{{ route('reports.stockmovements') }}" class="{{ request()->routeIs('reports.stockmovements') ? 'active' : '' }}">
                <i class="bi bi-archive"></i>
                <span>انبارگردانی</span>
            </a>
        </li>
        @endcan

        @can('debts.view')
        <!-- مدیریت بدهی ها -->
        <li>
            <a href="{{ route('debts.index') }}" class="{{ request()->routeIs('debts.*') ? 'active' : '' }}">
                <i class="bi bi-journal-minus"></i>
                <span>مدیریت بدهی ها</span>
            </a>
        </li>
        @endcan
        
    </ul>
</aside>
