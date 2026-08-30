@extends('layouts.auth')
@section('title', 'ورود به سیستم')

{{-- اسکریپت مربوط به گوگل ری کپچا --}}
{{-- @section('script')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection --}}

@section('content')

    <div class="login-card">

        <div class="text-center mb-4">
            <img src="{{ storeLogo() }}" class="login-logo" alt="Logo">
            <h3 class="mt-3 mb-2">
                {{ setting('store_name', 'سیستم مدیریت فروشگاه') }}
            </h3>
            <p class="text-secondary">
                ورود به پنل مدیریت
            </p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">
                    نام کاربری
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="username" class="form-control" autocomplete="username" autofocus required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    رمز عبور
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input id="password" type="password" name="password" class="form-control"
                        autocomplete="current-password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            {{-- تگ نمایش گوگل ری کپچا --}}
            {{-- <div class="form-group">
                <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITE_KEY') }}"></div>
            </div> --}}

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">
                        مرا به خاطر بسپار
                    </label>
                </div>
            </div>
            <button class="btn btn-warning w-100 login-btn">
                ورود به سیستم
            </button>
        </form>

        <div class="login-footer">
            <div class="badge bg-secondary">
                نسخه 1.0.0
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const password = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        toggle.addEventListener('click', () => {
            if (password.type === 'password') {
                password.type = 'text';
                toggle.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                password.type = 'password';
                toggle.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    </script>
@endpush
