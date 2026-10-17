@php
    $dashboardRoute = auth()->user()->dashboardRouteName();
    $isFa = app()->getLocale() === 'fa';
@endphp

<aside class="sidebar" id="sidebar" :class="{ 'collapsed': sidebarCollapsed }">
    <nav class="sidebar-nav">

        {{-- ================= داشبورد ================= --}}
        <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
            <span>{{ __('sidebar.group_main') }}</span>
        </div>

        @if ($dashboardRoute !== 'dashboard')
            <a href="{{ route($dashboardRoute) }}" class="sidebar-link glow-btn {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}"
                title="{{ __('sidebar.dashboard') }}">
                <img src="{{ asset('images/icons/sidebar/analytics_480px.png') }}" alt="" class="sidebar-icon">
                <span>{{ __('sidebar.dashboard') }}</span>
            </a>
        @else
            @can('dashboard.view')
                <a href="{{ route('dashboard') }}" class="sidebar-link glow-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    title="{{ __('sidebar.dashboard') }}">
                    <img src="{{ asset('images/icons/sidebar/analytics_480px.png') }}" alt="" class="sidebar-icon">
                    <span>{{ __('sidebar.dashboard') }}</span>
                </a>
            @endcan
        @endif

        {{-- ================= پیک‌ها ================= --}}
        @can('couriers.view')
            <a href="{{ route('couriers.index') }}" class="sidebar-link glow-btn {{ request()->routeIs('couriers.*') ? 'active' : '' }}"
                title="{{ __('sidebar.couriers') }}">
                <img src="{{ asset('images/icons/sidebar/motorcycle_480px.png') }}" alt="" class="sidebar-icon">
                <span>{{ __('sidebar.couriers') }}</span>
            </a>
        @endcan

        {{-- ================= فروش و مشتریان ================= --}}
        @php
            $salesItems = [];
            if (auth()->user()->hasPermission('pos.view')) {
                $salesItems[] = ['route' => 'pos.index', 'label' => __('sidebar.sales_counter'), 'icon' => 'cash_register_480px.png', 'is' => 'pos.*'];
            }
            if (auth()->user()->hasPermission('online-orders.view')) {
                $salesItems[] = ['route' => 'online-orders.index', 'label' => __('sidebar.online_orders'), 'icon' => 'shopping_cart_480px.png', 'is' => 'online-orders.*'];
            }
            if (auth()->user()->hasPermission('customers.view')) {
                $salesItems[] = ['route' => 'customers.index', 'label' => __('sidebar.customer_club'), 'icon' => 'address_book_480px.png', 'is' => 'customers.index'];
                $salesItems[] = ['route' => 'online-customers.index', 'label' => __('sidebar.online_customers'), 'icon' => 'phone_480px.png', 'is' => 'online-customers.*'];
            }
            if (auth()->user()->hasPermission('loyalty.view')) {
                $salesItems[] = ['route' => 'loyalty.index', 'label' => __('sidebar.loyalty'), 'icon' => 'Gift_480px.png', 'is' => 'loyalty.*'];
            }
            if (auth()->user()->hasPermission('customers.debtors')) {
                $salesItems[] = ['route' => 'customer-debtors.index', 'label' => __('sidebar.customer_debtors'), 'icon' => 'billing_480px.png', 'is' => 'customer-debtors.*'];
            }
            if (auth()->user()->hasPermission('customers.view')) {
                $salesItems[] = ['route' => 'customer-purchases.index', 'label' => __('sidebar.customer_purchases'), 'icon' => 'basket_480px.png', 'is' => 'customer-purchases.*'];
            }
            if (auth()->user()->hasPermission('customers.roles_view')) {
                $salesItems[] = ['route' => 'customer-roles.index', 'label' => __('sidebar.customer_club_roles'), 'icon' => 'hierarchy_480px.png', 'is' => 'customer-roles.*'];
            }
        @endphp
        @if (count($salesItems))
            <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_sales') }}</span>
            </div>
            @foreach ($salesItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="sidebar-link glow-btn {{ ($item['active'] ?? request()->routeIs($item['is'])) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <img src="{{ asset('images/icons/sidebar/' . $item['icon']) }}" alt="" class="sidebar-icon">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= انبار و کالا ================= --}}
        @php
            $stockItems = [];
            if (auth()->user()->hasPermission('products.view')) {
                $stockItems[] = ['route' => 'products.index', 'label' => __('sidebar.products'), 'icon' => 'box_480px.png', 'is' => 'products.*'];
            }
            if (auth()->user()->hasPermission('categories.view')) {
                $stockItems[] = ['route' => 'categories.index', 'label' => __('sidebar.categories'), 'icon' => 'opened_folder_480px.png', 'is' => 'categories.*'];
            }
            if (auth()->user()->hasPermission('brands.view')) {
                $stockItems[] = ['route' => 'brands.index', 'label' => __('sidebar.brands'), 'icon' => 'gold_medal_480px.png', 'is' => 'brands.*'];
            }
            if (auth()->user()->hasPermission('warehouses.view')) {
                $stockItems[] = ['route' => 'warehouses.index', 'label' => __('sidebar.warehouses'), 'icon' => 'company_480px.png', 'is' => 'warehouses.*'];
            }
            if (auth()->user()->hasPermission('qrcodes.view')) {
                $stockItems[] = ['route' => 'warehouse-qrcodes.index', 'label' => __('sidebar.warehouse_qrcodes'), 'icon' => 'search_480px.png', 'is' => 'warehouse-qrcodes.*'];
            }
            if (auth()->user()->hasPermission('transfers.view')) {
                $stockItems[] = ['route' => 'transfers.index', 'label' => __('sidebar.transfers'), 'icon' => 'left_and_right_arrows_480px.png', 'is' => 'transfers.*'];
            }
            if (auth()->user()->hasPermission('counts.view')) {
                $stockItems[] = ['route' => 'counts.index', 'label' => __('sidebar.stock_counts'), 'icon' => 'clipboard_480px.png', 'is' => 'counts.*'];
            }
        @endphp
        @if (count($stockItems))
            <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_warehouse') }}</span>
            </div>
            @foreach ($stockItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <img src="{{ asset('images/icons/sidebar/' . $item['icon']) }}" alt="" class="sidebar-icon">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= خرید و تامین ================= --}}
        @php
            $purchaseItems = [];
            if (auth()->user()->hasPermission('purchases.view')) {
                $purchaseItems[] = ['route' => 'purchase-invoices.index', 'label' => __('sidebar.reg_purchase_invoice'), 'icon' => 'document_480px.png', 'is' => 'purchase-invoices.*'];
            }
            if (auth()->user()->hasPermission('suppliers.view')) {
                $purchaseItems[] = ['route' => 'suppliers.index', 'label' => __('sidebar.suppliers'), 'icon' => 'truck_480px.png', 'is' => 'suppliers.*'];
            }
        @endphp
        @if (count($purchaseItems))
            <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_purchase') }}</span>
            </div>
            @foreach ($purchaseItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <img src="{{ asset('images/icons/sidebar/' . $item['icon']) }}" alt="" class="sidebar-icon">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= مالی ================= --}}
        @php
            $financeItems = [];
            if (auth()->user()->hasPermission('financial.view')) {
                $financeItems[] = ['route' => 'financial.index', 'label' => __('sidebar.financial_manage'), 'icon' => 'bank_480px.png', 'is' => 'financial.*'];
            }
            if (auth()->user()->hasPermission('cashboxes.view')) {
                $financeItems[] = ['route' => 'cashboxes.index', 'label' => __('sidebar.cashboxes'), 'icon' => 'coins_480px.png', 'is' => 'cashboxes.*'];
            }
            if (auth()->user()->hasPermission('expenses.view')) {
                $financeItems[] = ['route' => 'expenses.index', 'label' => __('sidebar.cost_manage'), 'icon' => 'wallet_480px.png', 'is' => 'expenses.*'];
            }
            if (auth()->user()->hasPermission('debts.view')) {
                $financeItems[] = ['route' => 'debts.index', 'label' => __('sidebar.debt_manage'), 'icon' => 'money_bag_480px.png', 'is' => 'debts.*'];
            }
            if (auth()->user()->hasPermission('payrolls.view')) {
                $financeItems[] = ['route' => 'payrolls.index', 'label' => __('sidebar.payrolls'), 'icon' => 'bank_card_dollar_480px.png', 'is' => 'payrolls.*'];
            }
        @endphp
        @if (count($financeItems))
            <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_finance') }}</span>
            </div>
            @foreach ($financeItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <img src="{{ asset('images/icons/sidebar/' . $item['icon']) }}" alt="" class="sidebar-icon">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= منابع انسانی ================= --}}
        @php
            $hrItems = [];
            if (auth()->user()->hasPermission('employees.view')) {
                $hrItems[] = ['route' => 'employees.index', 'label' => __('sidebar.employee_manage'), 'icon' => 'suitcase_480px.png', 'is' => 'employees.*'];
            }
            if (auth()->user()->hasPermission('attendance.view')) {
                $hrItems[] = ['route' => 'attendance.index', 'label' => __('sidebar.attendance'), 'icon' => 'calendar_480px.png', 'is' => 'attendance.*'];
            }
        @endphp
        @if (count($hrItems))
            <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_hr') }}</span>
            </div>
            @foreach ($hrItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['is']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <img src="{{ asset('images/icons/sidebar/' . $item['icon']) }}" alt="" class="sidebar-icon">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= گزارش‌ها ================= --}}
        @php
            $reportItems = [];
            if (auth()->user()->hasPermission('reports.sales')) {
                $reportItems[] = ['route' => 'reports.sales', 'label' => __('sidebar.sales_report'), 'icon' => 'bar_chart_480px.png'];
            }
            if (auth()->user()->hasPermission('reports.purchases')) {
                $reportItems[] = ['route' => 'reports.purchases', 'label' => __('sidebar.purchases_report'), 'icon' => 'pie_chart_480px.png'];
            }
            if (auth()->user()->hasPermission('reports.view')) {
                $reportItems[] = ['route' => 'reports.stockmovements', 'label' => __('sidebar.warehouse_manage'), 'icon' => 'synchronize_480px.png'];
            }
            if (auth()->user()->hasPermission('reports.profit')) {
                $reportItems[] = ['route' => 'reports.profit', 'label' => __('sidebar.profit_report'), 'icon' => 'profit_480px.png'];
            }
            if (auth()->user()->hasPermission('todos.view')) {
                $reportItems[] = ['route' => 'todos.index', 'label' => __('sidebar.todo'), 'icon' => 'checklist_480px.png'];
            }
        @endphp
        @if (count($reportItems))
            <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_reports') }}</span>
            </div>
            @foreach ($reportItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ request()->routeIs($item['route']) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <img src="{{ asset('images/icons/sidebar/' . $item['icon']) }}" alt="" class="sidebar-icon">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

        {{-- ================= مدیریت سیستم ================= --}}
        @php
            $systemItems = [];
            if (auth()->user()->hasPermission('users.view')) {
                $systemItems[] = ['route' => 'users.index', 'label' => __('sidebar.users'), 'icon' => 'user_480px.png', 'active' => request()->routeIs('users.*')];
            }
            if (auth()->user()->hasPermission('roles.view')) {
                $systemItems[] = ['route' => 'roles.index', 'label' => __('sidebar.roles'), 'icon' => 'shield_480px.png', 'active' => request()->routeIs('roles.*')];
            }
            if (auth()->user()->hasPermission('messages.view')) {
                $systemItems[] = ['route' => 'messages.index', 'label' => __('sidebar.messages'), 'icon' => 'messages_480px.png', 'active' => request()->routeIs('messages.index')];
            }
            if (auth()->user()->hasPermission('messages.inbox')) {
                $systemItems[] = ['route' => 'messages.inbox', 'label' => __('sidebar.my_messages'), 'icon' => 'inbox_480px.png', 'active' => request()->routeIs('messages.inbox')];
            }
            if (auth()->user()->hasPermission('settings.view')) {
                $systemItems[] = ['route' => 'settings.index', 'label' => __('sidebar.settings'), 'icon' => 'settings_480px.png', 'active' => request()->routeIs('settings.*')];
            }
        @endphp
        @if (count($systemItems))
            <div class="sidebar-section text-fuchsia fw-bold fs-3" x-show="!sidebarCollapsed">
                <span>{{ __('sidebar.group_system') }}</span>
            </div>
            @foreach ($systemItems as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link glow-btn {{ ($item['active'] ?? false) ? 'active' : '' }}"
                    title="{{ $item['label'] }}">
                    <img src="{{ asset('images/icons/sidebar/' . $item['icon']) }}" alt="" class="sidebar-icon">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        @endif

    </nav>
</aside>

{{--
    منوی فعال همیشه دیده شود: بعد از رفتن به هر صفحه (حتی منوهای انتهای سایدبار)،
    سایدبار خودش را تا جایی اسکرول می‌کند که آیتم فعال در دید باشد و لازم نباشد
    دستی اسکرول کنیم. اگر آیتم فعال از قبل در دید باشد، چیزی جابه‌جا نمی‌شود.
--}}
<script>
    (function () {
        function revealActiveSidebarItem() {
            var sidebar = document.getElementById('sidebar');
            var link = sidebar && sidebar.querySelector('.sidebar-link.active');

            if (!link) {
                return;
            }

            var sidebarRect = sidebar.getBoundingClientRect();
            var linkRect = link.getBoundingClientRect();
            var pad = 12; // فاصله‌ی امن تا لبه‌های سایدبار

            if (linkRect.top < sidebarRect.top + pad) {
                // آیتم فعال بالای دید است → کمی به پایین اسکرول کن
                sidebar.scrollTop += linkRect.top - sidebarRect.top - pad;
            } else if (linkRect.bottom > sidebarRect.bottom - pad) {
                // آیتم فعال پایین‌تر از دید است → تا رسیدنش به دید اسکرول کن
                sidebar.scrollTop += linkRect.bottom - sidebarRect.bottom + pad;
            }
        }

        // بعد از رندر نهایی (و آماده شدن فونت‌ها) اجرا شود
        function schedule() {
            requestAnimationFrame(function () {
                requestAnimationFrame(revealActiveSidebarItem);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', schedule);
        } else {
            schedule();
        }

        // در ناوبری بدون بارگذاری مجدد (Livewire navigate) هم اجرا شود
        document.addEventListener('livewire:navigated', schedule);
    })();
</script>
