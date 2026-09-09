@extends('layouts.app')

@section('title', 'دسترسی غیرمجاز')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
        <div class="col-12 col-md-8 col-lg-6 text-center">
            <div class="display-1 fw-bold text-danger">403</div>
            <h2 class="mb-3">دسترسی غیرمجاز</h2>
            <p class="text-body-secondary mb-4">
                متأسفانه شما اجازه‌ی دسترسی به این بخش را ندارید.
                در صورت نیاز، با مدیر سیستم تماس بگیرید تا سطح دسترسی نقش شما تغییر کند.
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-right"></i>
                    بازگشت
                </a>
                @can('dashboard.view')
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2"></i>
                    داشبورد
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
