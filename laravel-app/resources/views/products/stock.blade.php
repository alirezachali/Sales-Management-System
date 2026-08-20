{{-- resources/views/products/stock.blade.php --}}
@extends('layouts.app')

@section('title', 'گردش انبار')

@section('content')
    <div class="container-fluid">
        <livewire:products.stock-manager :product="$product" />
    </div>
@endsection
