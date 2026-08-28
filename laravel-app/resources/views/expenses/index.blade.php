{{-- resources/views/expenses/index.blade.php --}}
@extends('layouts.app')

@section('title', 'مدیریت هزینه‌ها')

@section('content')
    <div class="container-fluid">
        <livewire:expenses.expense-manager />
    </div>
@endsection
