@extends('layouts.app')
@section('title', 'داشبورد حسابدار')
@section('content')

    {{-- صفحه‌ی داشبورد حسابدار - یک ویوی Blade معمولی که کامپوننت Livewire
         <livewire:dashboard.accountant-overview /> را در خودش جای می‌دهد
         (دقیقاً همان الگوی داشبورد مدیریتی/صندوقدار/انباردار).
         تمام آمار مالی، سود و زیان، مانده صندوق‌ها، بدهی‌ها و نمودار فروش
         در برابر هزینه داخل همان کامپوننت زنده هستند و بدون رفرش صفحه
         به‌روزرسانی می‌شوند. --}}
    <livewire:dashboard.accountant-overview />

@endsection
@section('scripts')
    {{-- کتابخانه‌ی نمودار؛ فقط یک‌بار در سطح صفحه لود می‌شود --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
