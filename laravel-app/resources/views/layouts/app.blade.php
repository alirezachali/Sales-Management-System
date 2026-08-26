<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- اعمال زودهنگام تم (روشن/تیره) از localStorage برای جلوگیری از پرش رنگ هنگام بارگذاری --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('app-theme') || 'dark';
                document.documentElement.setAttribute('data-bs-theme', t);
            } catch (e) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>

    <!-- Title -->
    <title>@yield('title', setting('store_name'))</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ storeFavicon() }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom CSS -->
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
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

<!-- Navbar -->
@include('partials.navbar')

<div class="wrapper">

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Content -->
    <main class="content">
        @yield('content')
    </main>

</div>

<!-- Footer -->
{{-- @include('partials.footer') --}}


@yield('scripts')
@stack('scripts')
@livewireScripts
</body>
</html>