<!DOCTYPE html>
<html lang="fa" dir="rtl" data-bs-theme="dark">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>

        @yield('title', 'ورود به سیستم')

    </title>

    <link rel="icon"
          href="{{ storeFavicon() }}">

    <link href="{{ asset('css/bootstrap.rtl.min.css') }}"
          rel="stylesheet">

    <link href="{{ asset('css/bootstrap-icons.css') }}"
          rel="stylesheet">

    <link href="{{ asset('css/auth.css') }}"
          rel="stylesheet">

</head>

<body>

    <div class="auth-wrapper">

        @yield('content')

    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')

</body>

</html>