@extends('layouts.app')

@section('title', 'مدیریت مشتریان')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3 class="fw-bold mb-1">

                <i class="bi bi-people-fill text-primary"></i>

                مدیریت مشتریان

            </h3>

            <small class="text-muted">

                مدیریت اطلاعات مشتریان فروشگاه

            </small>

        </div>

        <button
            class="btn btn-primary"

            data-bs-toggle="modal"

            data-bs-target="#createCustomerModal">

            <i class="bi bi-plus-circle"></i>

            افزودن مشتری

        </button>

    </div>


    {{-- Search Card --}}

    <div class="card glass-card mb-4">

        <div class="card-body">

            <form>

                <div class="row">

                    <div class="col-lg-5">

                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-search"></i>

                            </span>

                            <input

                                type="text"

                                class="form-control"

                                name="search"

                                value="{{ request('search') }}"

                                placeholder="جستجو بر اساس نام یا موبایل">

                        </div>

                    </div>

                    <div class="col-lg-2">

                        <button class="btn btn-primary w-100">

                            جستجو

                        </button>

                    </div>

                    <div class="col-lg-5 text-end">

                        <span class="badge bg-primary fs-6">

                            تعداد مشتریان :

                            {{ $customers->total() }}

                        </span>

                    </div>

                </div>

            </form>

        </div>

    </div>



    {{-- Customers Table --}}

    <div class="card glass-card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>

                    <tr>

                        <th width="60">

                            #

                        </th>

                        <th>

                            نام مشتری

                        </th>

                        <th>

                            موبایل

                        </th>

                        <th>

                            نقش

                        </th>

                        <th>

                            تعداد خرید

                        </th>

                        <th>

                            مجموع خرید

                        </th>

                        <th>

                            وضعیت

                        </th>

                        <th width="140">

                            عملیات

                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($customers as $customer)

                        <tr>

                            <td>

                                {{ $loop->iteration }}

                            </td>

                            <td>

                                <div class="fw-bold">

                                    {{ $customer->full_name }}

                                </div>

                            </td>

                            <td>

                                {{ $customer->mobile }}

                            </td>

                            <td>

                                @if($customer->role)

                                    <span class="badge bg-{{ $customer->role->color }}">

                                        <i class="bi {{ $customer->role->icon }}"></i>

                                        {{ $customer->role->name }}

                                    </span>

                                @else

                                    <span class="badge bg-secondary">

                                        بدون نقش

                                    </span>

                                @endif

                            </td>

                            <td>

                                {{ number_format($customer->purchase_count) }}

                            </td>

                            <td>

                                {{ number_format($customer->total_purchase_amount) }}

                                تومان

                            </td>

                            <td>

                                @if($customer->is_active)

                                    <span class="badge bg-success">

                                        فعال

                                    </span>

                                @else

                                    <span class="badge bg-danger">

                                        غیرفعال

                                    </span>

                                @endif

                            </td>

                            <td>

                                <button

                                    class="btn btn-sm btn-warning editCustomer"

                                    data-id="{{ $customer->id }}"

                                    data-first_name="{{ $customer->first_name }}"

                                    data-last_name="{{ $customer->last_name }}"

                                    data-mobile="{{ $customer->mobile }}"

                                    data-phone="{{ $customer->phone }}"

                                    data-role="{{ $customer->customer_role_id }}"

                                    data-active="{{ $customer->is_active }}"

                                    data-notes="{{ $customer->notes }}">

                                    <i class="bi bi-pencil"></i>

                                </button>

                                <button

                                    class="btn btn-sm btn-danger deleteCustomer"

                                    data-id="{{ $customer->id }}">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="text-center py-5">

                                <i class="bi bi-inbox fs-1 text-muted"></i>

                                <br>

                                مشتری‌ای ثبت نشده است.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="card-footer">

            {{ $customers->links() }}

        </div>

    </div>

</div>

{{-- ===========================
      CREATE MODAL
============================ --}}

<div class="modal fade" id="createCustomerModal" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form action="{{ route('customers.store') }}" method="POST">

            @csrf

            <div class="modal-content glass-card">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-person-plus-fill"></i>

                        افزودن مشتری

                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">

                                نام

                            </label>

                            <input type="text"
                                   name="first_name"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                نام خانوادگی

                            </label>

                            <input type="text"
                                   name="last_name"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                موبایل

                            </label>

                            <input type="text"
                                   name="mobile"
                                   class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                تلفن

                            </label>

                            <input type="text"
                                   name="phone"
                                   class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                نقش مشتری

                            </label>

                            <select
                                class="form-select"
                                name="customer_role_id">

                                @foreach($roles as $role)

                                    <option
                                        value="{{ $role->id }}"
                                        {{ $role->is_default ? 'selected' : '' }}>

                                        {{ $role->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">

                                وضعیت

                            </label>

                            <select
                                class="form-select"
                                name="is_active">

                                <option value="1">

                                    فعال

                                </option>

                                <option value="0">

                                    غیرفعال

                                </option>

                            </select>

                        </div>

                        <div class="col-12">

                            <label class="form-label">

                                توضیحات

                            </label>

                            <textarea
                                class="form-control"
                                rows="3"
                                name="notes"></textarea>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        انصراف

                    </button>

                    <button
                        class="btn btn-primary">

                        ذخیره

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>



{{-- ===========================
      EDIT MODAL
============================ --}}

<div class="modal fade" id="editCustomerModal" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form
            id="editCustomerForm"
            method="POST">

            @csrf

            @method('PUT')

            <div class="modal-content glass-card">

                <div class="modal-header">

                    <h5 class="modal-title">

                        ویرایش مشتری

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label>

                                نام

                            </label>

                            <input
                                id="edit_first_name"
                                name="first_name"
                                class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label>

                                نام خانوادگی

                            </label>

                            <input
                                id="edit_last_name"
                                name="last_name"
                                class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label>

                                موبایل

                            </label>

                            <input
                                id="edit_mobile"
                                name="mobile"
                                class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label>

                                تلفن

                            </label>

                            <input
                                id="edit_phone"
                                name="phone"
                                class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label>

                                نقش

                            </label>

                            <select
                                id="edit_customer_role_id"
                                name="customer_role_id"
                                class="form-select">

                                @foreach($roles as $role)

                                    <option value="{{ $role->id }}">

                                        {{ $role->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label>

                                وضعیت

                            </label>

                            <select
                                id="edit_is_active"
                                name="is_active"
                                class="form-select">

                                <option value="1">

                                    فعال

                                </option>

                                <option value="0">

                                    غیرفعال

                                </option>

                            </select>

                        </div>

                        <div class="col-12">

                            <label>

                                توضیحات

                            </label>

                            <textarea
                                id="edit_notes"
                                name="notes"
                                rows="3"
                                class="form-control"></textarea>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        انصراف

                    </button>

                    <button
                        class="btn btn-warning">

                        ذخیره تغییرات

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>



{{-- ===========================
      DELETE MODAL
============================ --}}

<div class="modal fade" id="deleteCustomerModal">

    <div class="modal-dialog modal-dialog-centered">

        <form
            id="deleteCustomerForm"
            method="POST">

            @csrf

            @method('DELETE')

            <div class="modal-content">

                <div class="modal-header">

                    <h5>

                        حذف مشتری

                    </h5>

                </div>

                <div class="modal-body">

                    آیا از حذف این مشتری مطمئن هستید؟

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        انصراف

                    </button>

                    <button
                        class="btn btn-danger">

                        حذف

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@section('scripts')

<script>

$(document).ready(function () {

    // ==========================
    // Edit Customer
    // ==========================

    $('.editCustomer').on('click', function () {

        let id = $(this).data('id');

        $('#editCustomerForm').attr('action', '/customers/' + id);

        $('#edit_first_name').val($(this).data('first_name'));

        $('#edit_last_name').val($(this).data('last_name'));

        $('#edit_mobile').val($(this).data('mobile'));

        $('#edit_phone').val($(this).data('phone'));

        $('#edit_customer_role_id').val($(this).data('role'));

        $('#edit_is_active').val($(this).data('active'));

        $('#edit_notes').val($(this).data('notes'));

        let modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));

        modal.show();

    });


    // ==========================
    // Delete Customer
    // ==========================

    $('.deleteCustomer').on('click', function () {

        let id = $(this).data('id');

        $('#deleteCustomerForm').attr('action', '/customers/' + id);

        let modal = new bootstrap.Modal(document.getElementById('deleteCustomerModal'));

        modal.show();

    });


    // ==========================
    // Fix Bootstrap Backdrop
    // ==========================

    $('.modal').on('hidden.bs.modal', function () {

        $('.modal-backdrop').remove();

        $('body').removeClass('modal-open');

        $('body').css({

            overflow: '',

            paddingRight: ''

        });

    });

});

</script>

@endsection