{{-- resources/views/reports/sales.blade.php --}}
@extends('layouts.app')

@section('title', 'گزارش فروش')

@section('content')
    <div class="container-fluid">
        <livewire:reports.sales-report />
    </div>
    {{-- استایل تقویم شمسی (فقط همین صفحه) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
@endsection

@section('scripts')
    {{-- تقویم شمسی سبک و بدون وابستگی برای انتخاب بازه گزارش --}}
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
    <script>
        function initSalesReportJalaliPicker() {
            if (!window.jalaliDatepicker) return;
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                showTodayBtn: true,
                showEmptyBtn: true,
            });
        }

        document.addEventListener('DOMContentLoaded', initSalesReportJalaliPicker);
        // بعد از هر به‌روزرسانی Livewire هم تقویم را دوباره فعال می‌کنیم
        document.addEventListener('livewire:init', () => {
            initSalesReportJalaliPicker();
            Livewire.hook('morph.updated', initSalesReportJalaliPicker);
        });
    </script>
@endsection
