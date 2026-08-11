<!DOCTYPE html>
<html lang="fa" dir="rtl">
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
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pos.css') }}" rel="stylesheet">
    <link href="{{ asset('css/label-print.css') }}" rel="stylesheet">
    <!-- Bootstrap CDN CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
@include('partials.footer')
<!-- Bootstrap CDN JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="{{ asset('js/app.js') }}"></script>
@yield('scripts')
@stack('scripts')
</body>
</html>