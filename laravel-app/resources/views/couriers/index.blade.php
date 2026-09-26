@extends('layouts.app')

@section('title', 'مدیریت پیک‌ها')

@section('content')
    <div class="container-fluid">
        @livewire('couriers.courier-manager')
    </div>
@endsection