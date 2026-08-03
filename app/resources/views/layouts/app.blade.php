<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', setting('store_name'))</title>

    <link rel="icon" href="{{ storeFavicon() }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">

    <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


</head>

<body data-bs-theme="dark">


@include('partials.navbar')

<div class="wrapper">

    @include('partials.sidebar')

    <main class="content">

        @yield('content')

    </main>

</div>

@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{ asset('js/app.js') }}"></script>



@yield('scripts')

@stack('scripts')

</body>

</html>