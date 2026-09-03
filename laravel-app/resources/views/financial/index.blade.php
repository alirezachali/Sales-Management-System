{{-- resources/views/financial/index.blade.php --}}
@extends('layouts.app')

@section('title', 'مدیریت مالی')

@section('content')
    <div class="container-fluid">
        <livewire:financial.overview />
    </div>
    {{-- استایل تقویم شمسی (فقط همین صفحه) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
@endsection

@section('scripts')
    {{-- تقویم شمسی سبک و بدون وابستگی برای انتخاب تاریخ شروع/پایان --}}
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
    <script>
        function initFinancialJalaliPicker() {
            if (!window.jalaliDatepicker) return;
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                showTodayBtn: true,
                showEmptyBtn: true,
            });
        }

        document.addEventListener('DOMContentLoaded', initFinancialJalaliPicker);
        // بعد از هر به‌روزرسانی Livewire هم تقویم را دوباره فعال می‌کنیم
        document.addEventListener('livewire:init', () => {
            initFinancialJalaliPicker();
            Livewire.hook('morph.updated', initFinancialJalaliPicker);
        });
    </script>
@endsection
