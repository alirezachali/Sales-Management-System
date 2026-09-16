{{-- resources/views/messages/inbox.blade.php --}}
@extends('layouts.app')

@section('title', 'پیام‌های من')

@section('content')
    <div class="container-fluid">
        <livewire:messages.message-inbox />
    </div>
@endsection
