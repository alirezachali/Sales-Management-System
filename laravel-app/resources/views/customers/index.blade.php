@extends('layouts.app')
@section('title', 'مدیریت مشتریان')
@section('content')

    <div class="container-fluid">

        <!-- Success Alert Section -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        @endif

        <!-- Error Alert Section -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    مدیریت مشتریان
                </h3>
                <small class="text-muted">
                    مدیریت اطلاعات مشتریان فروشگاه
                </small>
            </div>

            <!-- Add New Customer Button -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCustomerModal"
                title="افزودن مشتری جدید به سیستم">
                <i class="bi bi-plus-circle"></i>
                افزودن مشتری
            </button>

        </div>

        {{-- Search Card --}}
        <div class="card glass-card mb-4">

            <!-- Search Card Body -->
            <div class="card-body">

                <form>

                    <div class="row">

                        <div class="col-lg-5">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                    placeholder="جستجو بر اساس نام یا موبایل">
                            </div>
                        </div>

                        <div class="col-lg-2">
                            <button class="btn btn-info w-100" title="برای شروع جستجو کلیک کنید">
                                جستجو
                            </button>
                        </div>

                        <div class="col-lg-5 text-end">
                            <span class="badge bg-info fs-6">
                                تعداد مشتریان :
                                {{ $customers->total() }}
                            </span>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        {{-- جدول لیست مشتریان --}}
        <div class="card glass-card">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <!-- جدول مشتریان -->
                    <table class="table table-bordered table-hover align-middle">

                        <!-- Start Customer Table Head -->
                        <thead>
                            <tr>
                                <th width="60"> ردیف</th>
                                <th> نام مشتری</th>
                                <th> موبایل</th>
                                <th> نقش</th>
                                <th> تعداد خرید</th>
                                <th> مجموع خرید</th>
                                <th> وضعیت</th>
                                <th width="140"> عملیات</th>
                            </tr>
                        </thead>
                        <!-- End Customer Table Head -->

                        <!-- Start Customer Table Body -->
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <!-- ردیف -->
                                    <td>{{ $loop->iteration }}</td>
                                    <!-- نام مشتری -->
                                    <td>
                                        <div class="fw-bold">
                                            {{ $customer->full_name }}
                                        </div>
                                    </td>
                                    <!-- موبایل -->
                                    <td>{{ $customer->mobile }}</td>
                                    <!-- نقش -->
                                    <td>
                                        @if ($customer->role)
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
                                    <!-- تعداد خرید -->
                                    <td>{{ number_format($customer->purchase_count) }}</td>
                                    <!-- مجموع خرید -->
                                    <td>{{ number_format($customer->total_purchase_amount) }}
                                        <span>
                                            <!-- واحد پولی از دیتابیس -->
                                            {{ setting('currency', '') }}
                                        </span>
                                    </td>
                                    <!-- وضعیت -->
                                    <td>
                                        @if ($customer->is_active)
                                            <span class="badge bg-success">
                                                فعال
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                غیرفعال
                                            </span>
                                        @endif
                                    </td>
                                    <!-- عملیات -->
                                    <td>
                                        <!-- Edit Customer Button -->
                                        <button class="btn btn-sm btn-warning editCustomer" data-id="{{ $customer->id }}"
                                            data-first_name="{{ $customer->first_name }}"
                                            data-last_name="{{ $customer->last_name }}"
                                            data-mobile="{{ $customer->mobile }}" data-phone="{{ $customer->phone }}"
                                            data-role="{{ $customer->customer_role_id }}"
                                            data-active="{{ $customer->is_active }}" data-notes="{{ $customer->notes }}"
                                            data-bs-toggle="modal" data-bs-target="#editCustomerModal"
                                            title="برای ویرایش این مشتری کلیک کنید">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- Delete Customer Button -->
                                        <button class="btn btn-sm btn-danger deleteCustomer" data-id="{{ $customer->id }}"
                                            data-bs-toggle="modal" data-bs-target="#deleteCustomerModal"
                                            title="برای حذف این مشتری کلیک کنید">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </td>
                                </tr>

                                <!-- اگر مشتری در دیتابیس وجود نداشته باشد اطلاعات زیر را نمایش میدهد -->
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
                        <!-- End Customer Table Body -->
                    </table>
                    <!-- End Customer Table -->
                </div>
            </div>

            <!-- Start Customer Card Footer -->
            <div class="card-footer">
                {{ $customers->links() }}
            </div>
            <!-- End Customer Card Footer -->

        </div>
    </div>

    <!-- include External Modals File -->
    @include('customers.modals.create')
    @include('customers.modals.edit')
    @include('customers.modals.delete')

@endsection
@section('scripts')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {

            // Edit Customer
            $('.editCustomer').on('click', function() {
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

            // Delete Customer
            $('.deleteCustomer').on('click', function() {
                let id = $(this).data('id');
                $('#deleteCustomerForm').attr('action', '/customers/' + id);
                let modal = new bootstrap.Modal(document.getElementById('deleteCustomerModal'));
                modal.show();
            });

            // Fix Bootstrap Backdrop
            $('.modal').on('hidden.bs.modal', function() {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css({
                    overflow: '',
                    paddingRight: ''
                });
            });

        });
    </script>
