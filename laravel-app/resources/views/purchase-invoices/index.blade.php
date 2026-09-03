{{-- resources/views/purchase-invoices/index.blade.php --}}
@extends('layouts.app')

@section('title', 'ثبت فاکتور خرید')

@section('content')
    <div class="container-fluid">
        <livewire:purchase-invoices.purchase-invoice-manager />
    </div>
    {{-- استایل تقویم شمسی (فقط همین صفحه) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
@endsection

@section('scripts')
    {{-- تقویم شمسی سبک و بدون وابستگی برای انتخاب تاریخ خرید --}}
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
    <script>
        function initPurchaseJalaliPicker() {
            if (!window.jalaliDatepicker) return;
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                showTodayBtn: true,
                showEmptyBtn: true,
            });
        }

        document.addEventListener('DOMContentLoaded', initPurchaseJalaliPicker);
        // بعد از هر به‌روزرسانی Livewire هم تقویم را دوباره فعال می‌کنیم
        document.addEventListener('livewire:init', () => {
            initPurchaseJalaliPicker();
            Livewire.hook('morph.updated', initPurchaseJalaliPicker);
        });
    </script>
@endsection
