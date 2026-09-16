@extends('layouts.app')
@section('title', __('brands.page_title'))
@section('content')

    <div class="container-fluid">
        <livewire:brands.brand-manager />
    </div>

@endsection

