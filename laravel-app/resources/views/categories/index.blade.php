@extends('layouts.app')
@section('title', __('categories.page_title'))
@section('content')

    <div class="container-fluid">
        <livewire:categories.category-manager />
    </div>

@endsection
