{{-- resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('title', 'مدیریت کارکنان')

@section('content')
    <div class="container-fluid">
        <livewire:employees.employee-manager />
    </div>
@endsection
