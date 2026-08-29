{{-- resources/views/todos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'لیست کارها')

@section('content')
    <div class="container-fluid">
        <livewire:todos.todo-manager />
    </div>
@endsection