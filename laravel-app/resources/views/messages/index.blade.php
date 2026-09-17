{{-- resources/views/messages/index.blade.php --}}
@extends('layouts.app')

@section('title', 'مدیریت پیام‌ها')

@section('content')
    <div class="container-fluid">
        <livewire:messages.message-manager />
    </div>
@endsection
