{{-- resources/views/reports/sales.blade.php --}}
@extends('layouts.app')

@section('title', 'گزارش فروش')

@section('content')
    <div class="container-fluid">
        <livewire:reports.sales-report />
    </div>
@endsection
