<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- اعمال زودهنگام تم (روشن/تیره) از localStorage برای جلوگیری از پرش رنگ هنگام بارگذاری --}}
    <script>
        (function() {
            try {
                var t = localStorage.getItem('app-theme') || 'dark';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>

    <!-- Title -->
    <title>@yield('title', 'ورود به سیستم')</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ storeFavicon() }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <!-- Bootstrap CDN CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body x-data="{
        theme: (localStorage.getItem('app-theme') || 'dark'),
        sidebarCollapsed: (localStorage.getItem('app-sidebar-collapsed') === '1'),
        setTheme(t) {
            this.theme = t;
            localStorage.setItem('app-theme', t);
            document.documentElement.setAttribute('data-bs-theme', t);
        },
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('app-sidebar-collapsed', this.sidebarCollapsed ? '1' : '0');
        }
    }" x-init="document.documentElement.setAttribute('data-bs-theme', theme)">
    
    <div class="auth-wrapper">
        @yield('content')
    </div>
    
    <!-- Bootstrap CDN JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <!-- مربوط به اسکریپت ریکپچا -->
    {{-- @yield('script') --}}
</body>

</html>
