@extends('layouts.app')

@section('title', 'تنظیمات سیستم')

@section('content')
    <div class="container-fluid">
        <livewire:settings.settings-manager />
    </div>
@endsection

{{-- پیمایش و نشانگر متحرک نوار تب‌های تنظیمات --}}
@push('scripts')
    <script src="{{ asset('js/settings-tabs.js') }}"></script>
@endpush
