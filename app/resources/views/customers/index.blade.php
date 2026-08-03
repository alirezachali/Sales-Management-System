@extends('layouts.app')

@section('title','مشتریان')

@section('content')

<div class="card glass-card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h5 class="mb-0">
            <i class="bi bi-people"></i>
            مشتریان
        </h5>

        <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createCustomerModal">

            <i class="bi bi-plus-circle"></i>

            افزودن مشتری

        </button>

    </div>

    <div class="card-body">

        {{-- سرچ --}}

        {{-- جدول --}}

        {{-- pagination --}}

    </div>

</div>

@include('customers.modals.create')
@include('customers.modals.edit')
@include('customers.modals.delete')

@endsection