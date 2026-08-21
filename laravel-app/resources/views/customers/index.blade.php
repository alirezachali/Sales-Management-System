{{-- resources/views/customers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'باشگاه مشتریان')

@section('content')
    <div class="container-fluid">
        <livewire:customers.customer-manager />
    </div>
@endsection
