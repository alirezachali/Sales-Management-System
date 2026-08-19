<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Title -->
    <title>@yield('title', setting('store_name'))</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ storeFavicon() }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom CSS -->
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/footer.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/pos.css') }}" rel="stylesheet">
    <link href="{{ asset('css/label-print.css') }}" rel="stylesheet">
    <link href="{{ asset('css/roles.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
</head>
<body data-bs-theme="dark">
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