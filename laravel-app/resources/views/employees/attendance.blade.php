@extends('layouts.app')

@section('title', 'حضور و غیاب کارکنان')

@section('content')
    <div class="container-fluid">
        <livewire:employees.attendance-manager />
    </div>
    {{-- استایل تقویم شمسی (فقط همین صفحه) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
    <style>
        /* تقویم باید بالای مودال بوت‌استرپ (z-index 1055) قرار بگیرد */
        .jdp-container { z-index: 1080 !important; }
    </style>
@endsection

@section('scripts')
    {{-- تقویم شمسی سبک و بدون وابستگی برای انتخاب تاریخ سابقه --}}
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
    <script>
        function initAttendanceJalaliPicker() {
            if (!window.jalaliDatepicker) return;
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                showTodayBtn: true,
                showEmptyBtn: true,
                zIndex: 1080,
            });
        }

        document.addEventListener('DOMContentLoaded', initAttendanceJalaliPicker);
        document.addEventListener('livewire:init', () => {
            initAttendanceJalaliPicker();
            Livewire.hook('morph.updated', initAttendanceJalaliPicker);
        });

        // ورودی ساعت ۲۴ ساعته: فقط رقم و دونقطه، درج خودکار :
        function bindTime24(el) {
            if (el.dataset.time24Bound) return;
            el.dataset.time24Bound = '1';
            el.addEventListener('input', () => {
                let v = el.value.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[^\d:]/g, '').replace(/:/g, '');
                if (v.length >= 5) v = v.slice(0, 5);
                el.value = v.length > 2 ? v.slice(0, 2) + ':' + v.slice(2) : v;
            });
        }

        document.querySelectorAll('.time-24').forEach(bindTime24);
        document.addEventListener('livewire:navigated', () =>
            document.querySelectorAll('.time-24').forEach(bindTime24));
        Livewire.on('att-modal-open', () =>
            document.querySelectorAll('.time-24').forEach(bindTime24));
        const attObserver = new MutationObserver(() =>
            document.querySelectorAll('.time-24').forEach(bindTime24));
        attObserver.observe(document.body, { childList: true, subtree: true });
    </script>
@endsection
