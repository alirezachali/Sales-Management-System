{{-- resources/views/purchase-invoices/index.blade.php --}}
@extends('layouts.app')

@section('title', 'ثبت فاکتور خرید')

@section('content')
    <div class="container-fluid">
        <livewire:purchase-invoices.purchase-invoice-manager />
    </div>
@endsection