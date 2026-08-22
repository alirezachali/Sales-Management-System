@extends('layouts.app')
@section('title', 'مجوزهای نقش')
@section('content')

    <div class="container-fluid">
        <livewire:roles.role-permission-manager :role="$role" />
    </div>

@endsection
