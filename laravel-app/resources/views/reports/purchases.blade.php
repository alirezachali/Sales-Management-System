@extends('layouts.app')

@section('title', 'گزارش خرید')

@section('content')
    <div class="container-fluid">
        <livewire:reports.purchase-report />
    </div>

    {{-- استایل تقویم شمسی (فقط همین صفحه) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
@endsection

@section('scripts')
    {{-- تقویم شمسی سبک و بدون وابستگی برای انتخاب بازه گزارش --}}
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
    <script>
        function initPurchaseReportJalaliPicker() {
            if (!window.jalaliDatepicker) return;
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                showTodayBtn: true,
                showEmptyBtn: true,
            });
        }

        document.addEventListener('DOMContentLoaded', initPurchaseReportJalaliPicker);
        // بعد از هر به‌روزرسانی Livewire هم تقویم را دوباره فعال می‌کنیم
        document.addEventListener('livewire:init', () => {
            initPurchaseReportJalaliPicker();
            Livewire.hook('morph.updated', initPurchaseReportJalaliPicker);
        });
    </script>
@endsection
