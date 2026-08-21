{{-- resources/views/customers/roles/index.blade.php --}}
@extends('layouts.app')

@section('title', 'رده‌های باشگاه مشتریان')

@section('content')
    <div class="container-fluid">
        <livewire:customers.customer-role-manager />
    </div>
@endsection
