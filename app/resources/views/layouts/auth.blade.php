<!DOCTYPE html>
<html lang="fa" dir="rtl" data-bs-theme="dark">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'ورود به سیستم')
    </title>

    <link rel="icon" href="{{ storeFavicon() }}">

    <link rel="stylesheet" href="{{ asset('css/bootstrap.rtl.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

    <div class="auth-wrapper">

        @yield('content')

    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>