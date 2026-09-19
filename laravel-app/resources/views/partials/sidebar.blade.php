@php
    $dashboardRoute = auth()->user()->dashboardRouteName();
    $isFa = app()->getLocale() === 'fa';
@endphp

<aside class="sidebar" id="sidebar" :class="{ 'collapsed': sidebarCollapsed }">
    <nav class="sidebar-nav">

        {{-- ================= داشبورد ================= --}}
        <div class="sidebar-section" x-show="!sidebarCollapsed">
            <span>{{ __('sidebar.group_main') }}</span>
        </div>

        @if ($dashboardRoute !== 'dashboard')
            <a href="{{ route($dashboardRoute) }}" class="sidebar-link glow-btn {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}"
                title="{{ __('sidebar.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>{{ __('sidebar.dashboard') }}</span>
            </a>
        @else
            @can('dashboard.view')
                <a href="{{ route('dashboard') }}" class="sidebar-link glow-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    title="{{ __('sidebar.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>{{ __('sidebar.dashboard') }}</span>
                </a>
            @endcan
        @endif

        {{-- ================= فروش و مشتریان ================= --}}
        @php
            $salesItems = [];
            if (auth()->user()->hasPermission('pos.view')) {
                $salesItems[] = ['route' => 'pos.index', 'label' => __('sidebar.sales_counter'), 'icon' => 'bi-cash-stack', 'is' => 'pos.*'];
            }
            if (auth()->user()->hasPermission('customers.view')) {
                $salesItems[] = ['route' => 'customers.index', 'label' => __('sidebar.customer_club'), 'icon' => 'bi-person-vcard', 'is' => 'customers.*'];
            }
            if (auth()->user()->hasPermission('loyalty.view')) {
                $salesItems[] = ['route' => 'loyalty.index', 'label' => __('sidebar.loyalty'), 'icon' => 'bi-gem', 'is' => 'loyalty.*'];
            }
            if (auth()->user()->hasPermission('customers.debtors')) {
                $salesItems[] = ['route' => 'customer-debtors.index', 'label' => __('sidebar.customer_debtors'), 'icon' => 'bi-person-exclamation', 'is' => 'customer-debtors.*'];
            }
            if (auth()->user()->hasPermission('customers.view')) {
                $salesItems[] = ['route' => 'customer-purchases.index', 'label' => __('sidebar.customer_purchases'), 'icon' => 'bi-bag-check', 'is' => 'customer-purchases.*'];
            }
            if (auth()->user()->hasPermission('customers.roles_view')) {
                $salesItems[] = ['route' => 'customer-roles.index', 'label' => __('sidebar.customer_club_roles'), 'icon' => 'bi-award', 'is' => 'customer-roles.*'];
            }
        @endphp
        @if (count($salesItems))
            <div class="sidebar-section" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_sales') }}</span>
            </div>
            @foreach ($salesItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="sidebar-link glow-btn {{ ($item['active'] ?? request()->routeIs($item['is'])) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= انبار و کالا ================= --}}
        @php
            $stockItems = [];
            if (auth()->user()->hasPermission('products.view')) {
                $stockItems[] = ['route' => 'products.index', 'label' => __('sidebar.products'), 'icon' => 'bi-box-seam', 'is' => 'products.*'];
            }
            if (auth()->user()->hasPermission('categories.view')) {
                $stockItems[] = ['route' => 'categories.index', 'label' => __('sidebar.categories'), 'icon' => 'bi-grid', 'is' => 'categories.*'];
            }
            if (auth()->user()->hasPermission('brands.view')) {
                $stockItems[] = ['route' => 'brands.index', 'label' => __('sidebar.brands'), 'icon' => 'bi-award', 'is' => 'brands.*'];
            }
            if (auth()->user()->hasPermission('warehouses.view')) {
                $stockItems[] = ['route' => 'warehouses.index', 'label' => __('sidebar.warehouses'), 'icon' => 'bi-buildings', 'is' => 'warehouses.*'];
            }
            if (auth()->user()->hasPermission('transfers.view')) {
                $stockItems[] = ['route' => 'transfers.index', 'label' => __('sidebar.transfers'), 'icon' => 'bi-arrow-left-right', 'is' => 'transfers.*'];
            }
            if (auth()->user()->hasPermission('counts.view')) {
                $stockItems[] = ['route' => 'counts.index', 'label' => __('sidebar.stock_counts'), 'icon' => 'bi-clipboard-check', 'is' => 'counts.*'];
            }
        @endphp
        @if (count($stockItems))
            <div class="sidebar-section" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_warehouse') }}</span>
            </div>
            @foreach ($stockItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= خرید و تامین ================= --}}
        @php
            $purchaseItems = [];
            if (auth()->user()->hasPermission('purchases.view')) {
                $purchaseItems[] = ['route' => 'purchase-invoices.index', 'label' => __('sidebar.reg_purchase_invoice'), 'icon' => 'bi-file-earmark-plus', 'is' => 'purchase-invoices.*'];
            }
            if (auth()->user()->hasPermission('suppliers.view')) {
                $purchaseItems[] = ['route' => 'suppliers.index', 'label' => __('sidebar.suppliers'), 'icon' => 'bi-truck', 'is' => 'suppliers.*'];
            }
        @endphp
        @if (count($purchaseItems))
            <div class="sidebar-section" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_purchase') }}</span>
            </div>
            @foreach ($purchaseItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= مالی ================= --}}
        @php
            $financeItems = [];
            if (auth()->user()->hasPermission('financial.view')) {
                $financeItems[] = ['route' => 'financial.index', 'label' => __('sidebar.financial_manage'), 'icon' => 'bi-bank', 'is' => 'financial.*'];
            }
            if (auth()->user()->hasPermission('cashboxes.view')) {
                $financeItems[] = ['route' => 'cashboxes.index', 'label' => __('sidebar.cashboxes'), 'icon' => 'bi-safe', 'is' => 'cashboxes.*'];
            }
            if (auth()->user()->hasPermission('expenses.view')) {
                $financeItems[] = ['route' => 'expenses.index', 'label' => __('sidebar.cost_manage'), 'icon' => 'bi-wallet2', 'is' => 'expenses.*'];
            }
            if (auth()->user()->hasPermission('debts.view')) {
                $financeItems[] = ['route' => 'debts.index', 'label' => __('sidebar.debt_manage'), 'icon' => 'bi-journal-minus', 'is' => 'debts.*'];
            }
            if (auth()->user()->hasPermission('payrolls.view')) {
                $financeItems[] = ['route' => 'payrolls.index', 'label' => __('sidebar.payrolls'), 'icon' => 'bi-cash-coin', 'is' => 'payrolls.*'];
            }
        @endphp
        @if (count($financeItems))
            <div class="sidebar-section" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_finance') }}</span>
            </div>
            @foreach ($financeItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= منابع انسانی ================= --}}
        @php
            $hrItems = [];
            if (auth()->user()->hasPermission('employees.view')) {
                $hrItems[] = ['route' => 'employees.index', 'label' => __('sidebar.employee_manage'), 'icon' => 'bi-person-badge', 'is' => 'employees.*'];
            }
            if (auth()->user()->hasPermission('attendance.view')) {
                $hrItems[] = ['route' => 'attendance.index', 'label' => __('sidebar.attendance'), 'icon' => 'bi-calendar-check', 'is' => 'attendance.*'];
            }
        @endphp
        @if (count($hrItems))
            <div class="sidebar-section" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_hr') }}</span>
            </div>
            @foreach ($hrItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= گزارش‌ها ================= --}}
        @php
            $reportItems = [];
            if (auth()->user()->hasPermission('reports.sales')) {
                $reportItems[] = ['route' => 'reports.sales', 'label' => __('sidebar.sales_report'), 'icon' => 'bi-clipboard-data'];
            }
            if (auth()->user()->hasPermission('reports.purchases')) {
                $reportItems[] = ['route' => 'reports.purchases', 'label' => __('sidebar.purchases_report'), 'icon' => 'bi-archive'];
            }
            if (auth()->user()->hasPermission('reports.view')) {
                $reportItems[] = ['route' => 'reports.stockmovements', 'label' => __('sidebar.warehouse_manage'), 'icon' => 'bi-boxes'];
            }
            if (auth()->user()->hasPermission('reports.profit')) {
                $reportItems[] = ['route' => 'reports.profit', 'label' => __('sidebar.profit_report'), 'icon' => 'bi-graph-up-arrow'];
            }
            if (auth()->user()->hasPermission('todos.view')) {
                $reportItems[] = ['route' => 'todos.index', 'label' => __('sidebar.todo'), 'icon' => 'bi-check2-square'];
            }
        @endphp
        @if (count($reportItems))
            <div class="sidebar-section" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_reports') }}</span>
            </div>
            @foreach ($reportItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= مدیریت سیستم ================= --}}
        @php
            $systemItems = [];
            if (auth()->user()->hasPermission('users.view')) {
                $systemItems[] = ['route' => 'users.index', 'label' => __('sidebar.users'), 'icon' => 'bi-people', 'active' => request()->routeIs('users.*')];
            }
            if (auth()->user()->hasPermission('roles.view')) {
                $systemItems[] = ['route' => 'roles.index', 'label' => __('sidebar.roles'), 'icon' => 'bi-shield-lock', 'active' => request()->routeIs('roles.*')];
            }
            if (auth()->user()->hasPermission('messages.view')) {
                $systemItems[] = ['route' => 'messages.index', 'label' => __('sidebar.messages'), 'icon' => 'bi-envelope-paper', 'active' => request()->routeIs('messages.index')];
            }
            if (auth()->user()->hasPermission('settings.view')) {
                $systemItems[] = ['route' => 'settings.index', 'label' => __('sidebar.settings'), 'icon' => 'bi-gear', 'active' => request()->routeIs('settings.*')];
            }
        @endphp
        @if (count($systemItems))
            <div class="sidebar-section" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_system') }}</span>
            </div>
            @foreach ($systemItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ ($item['active'] ?? false) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

    </nav>
</aside>
