{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'مدیریت محصولات')

@section('content')
    <div class="container-fluid">
        <livewire:products.product-manager />
    </div>
@endsection
