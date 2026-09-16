@extends('layouts.app')
@section('title', 'داشبورد صندوقدار')
@section('content')

    <livewire:dashboard.cashier-overview />

    {{-- استایل تقویم شمسی (فقط همین صفحه) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
    <style>
        /* تقویم باید بالای مودال بوت‌استرپ (z-index 1055) قرار بگیرد */
        .jdp-container { z-index: 1080 !important; }
    </style>
@endsection
@section('scripts')
    {{-- تقویم شمسی سبک و بدون وابستگی برای انتخاب سررسید کار --}}
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
    <script>
        function initCashierTodoJalaliPicker() {
            if (!window.jalaliDatepicker) return;
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                showTodayBtn: true,
                showEmptyBtn: true,
                zIndex: 1080,
            });
        }

        document.addEventListener('DOMContentLoaded', initCashierTodoJalaliPicker);
        document.addEventListener('livewire:init', () => {
            initCashierTodoJalaliPicker();
            Livewire.hook('morph.updated', initCashierTodoJalaliPicker);
        });
    </script>
@endsection
