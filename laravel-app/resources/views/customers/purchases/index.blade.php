{{-- resources/views/customers/purchases/index.blade.php --}}
@extends('layouts.app')

@section('title', 'خریدهای مشتریان')

@section('content')
    <div class="container-fluid">
        <livewire:customers.customer-purchase-manager />
    </div>
@endsection
