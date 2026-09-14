@extends('layouts.app')
@section('title', 'پروفایل کاربر')
@section('content')

    <div class="container-fluid">
        <livewire:profile.profile-page :username="$username" />
    </div>

@endsection
