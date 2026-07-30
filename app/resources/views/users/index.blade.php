@extends('layouts.app')

@section('title', 'مدیریت کاربران')

@section('content')


@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

    </div>

@endif


<!-- Header -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">مدیریت کاربران</h3>

        <p class="text-muted mb-0">
            مدیریت کاربران سیستم
        </p>

    </div>

    <button
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#createUserModal">

        <i class="bi bi-plus-circle"></i>

        کاربر جدید

    </button>

</div>

<!-- User Table -->
<div class="card shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>

                    <tr>

                        <th width="60">#</th>

                        <th>نام</th>

                        <th>نام کاربری</th>

                        <th>وضعیت</th>

                        <th>آخرین ورود</th>

                        <th width="180">عملیات</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($users as $user)

                        <tr>

                            <td>{{ $loop->iteration + ($users->currentPage()-1)*$users->perPage() }}</td>

                            <td>{{ $user->name }}</td>

                            <td>{{ $user->username }}</td>

                            <td>

                                @if($user->is_active)

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

                                {{ $user->last_login_at?->format('Y/m/d H:i') ?? '-' }}

                            </td>

                            <td>

                                <button class="btn btn-sm btn-warning">

                                    <i class="bi bi-pencil"></i>

                                </button>

                                <button class="btn btn-sm btn-info">

                                    <i class="bi bi-key"></i>

                                </button>

                                <button class="btn btn-sm btn-danger">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center py-5">

                                هیچ کاربری ثبت نشده است.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<div class="mt-3">

    {{ $users->links() }}

</div>


<!-- Create User Modal -->
<div
    class="modal fade"
    id="createUserModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <form
            action="{{ route('users.store') }}"
            method="POST"
            class="modal-content">

            @csrf

            <div class="modal-header">

                <h5 class="modal-title">

                    افزودن کاربر جدید

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            نام و نام خانوادگی

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            نام کاربری

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="username"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            ایمیل

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            name="email">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            موبایل

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="phone">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            رمز عبور

                        </label>

                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            تکرار رمز عبور

                        </label>

                        <input
                            type="password"
                            class="form-control"
                            name="password_confirmation"
                            required>

                    </div>

                    <div class="col-md-12">

                        <div class="form-check form-switch">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="is_active"
                                value="1"
                                checked>

                            <label class="form-check-label">

                                کاربر فعال باشد

                            </label>

                        </div>

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
                    type="submit"
                    class="btn btn-primary">

                    ذخیره

                </button>

            </div>

        </form>

    </div>

</div>

@endsection