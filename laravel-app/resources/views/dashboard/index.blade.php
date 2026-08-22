@extends('layouts.app')
@section('title', 'داشبرد مدیریتی')
@section('content')

    {{-- صفحه‌ی داشبورد - یک ویوی Blade معمولی که کامپوننت Livewire را
         با تگ <livewire:dashboard.overview /> در خودش جای می‌دهد
         (دقیقاً همان الگوی ماژول محصولات/مشتریان/تامین‌کنندگان).
         تمام آمار، جدول آخرین فروش‌ها، لیست کم‌موجودی‌ها و نمودار فروش
         داخل همان کامپوننت زنده هستند و بدون رفرش صفحه به‌روزرسانی می‌شوند. --}}
    <livewire:dashboard.overview />

@endsection
@section('scripts')
    {{-- کتابخانه‌ی نمودار؛ فقط یک‌بار در سطح صفحه لود می‌شود --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
