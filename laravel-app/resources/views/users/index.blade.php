@extends('layouts.app')
@section('title', 'مدیریت کاربران')
@section('content')

    <div class="container-fluid">

        <!-- نمایش پیغام های موفقیت -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        @endif

        <!-- نمایش پیغام های خطا -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        @endif

        <!-- هدر کارت کاربران -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">

            <div>
                <h3 class="mb-1">مدیریت کاربران</h3>
                <p class="text-muted mb-0">مدیریت کاربران سیستم</p>
            </div>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal"
                title="برای افزودن کاربر جدید به سیستم کلیک کنید">
                <i class="bi bi-plus-circle"></i>
                کاربر جدید
            </button>

        </div>

        <!-- بدنه ی کارت کاربران -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <!-- جدول کاربران -->
                    <table class="table table-bordered table-hover align-middle">
                        <!-- هدر جدول کاربران -->
                        <thead>
                            <tr>
                                <th width="60">ردیف</th>
                                <th>نام</th>
                                <th>نام کاربری</th>
                                <th width="100">وضعیت</th>
                                <th>نقش</th>
                                <th>آخرین ورود</th>
                                <th width="135">عملیات</th>
                            </tr>
                        </thead>

                        <!-- بدنه ی جدول کاربران -->
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <!-- ردیف -->
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <!-- نام -->
                                    <td>{{ $user->name }}</td>
                                    <!-- نام کاربری -->
                                    <td>{{ $user->username }}</td>
                                    <!-- وضعیت -->
                                    <td>
                                        @if ($user->is_active)
                                            <span class="badge bg-success">
                                                فعال
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                غیرفعال
                                            </span>
                                        @endif
                                    </td>
                                    <!-- نقش -->
                                    <td>{{ $user->role?->display_name ?? '-' }}</td>
                                    <!-- آخرین ورود-->
                                    <td>{{ $user->last_login_at?->format('Y/m/d H:i') ?? '-' }}</td>
                                    <!-- عملیات -->
                                    <td>
                                        <!-- دکمه ویرایش مشخصات یک کاربر -->
                                        <button class="btn btn-sm btn-warning editUserBtn" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" data-username="{{ $user->username }}"
                                            data-email="{{ $user->email }}" data-phone="{{ $user->phone }}"
                                            data-role="{{ $user->role_id }}"
                                            data-active="{{ $user->is_active }}" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal" title="برای ویرایش این کاربر کلیک کنید">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <!-- دکمه تغییر رمز ورود یک کاربر -->
                                        <button class="btn btn-sm btn-info changePasswordBtn" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" data-bs-toggle="modal"
                                            data-bs-target="#changePasswordModal"
                                            title="برای تغییر کلمه عبور این کاربر کلیک کنید">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <!-- دکمه حذف یک کاربر -->
                                        <button class="btn btn-sm btn-danger deleteUserBtn" data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}" data-bs-toggle="modal"
                                            data-bs-target="#deleteUserModal" title="برای حذف این کاربر کلیک کنید">
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
    </div>

    <div class="mt-3">{{ $users->links() }}</div>

    <!-- وارد کردن فایل مودال ها -->
    @include('users.modals.changepassword')
    @include('users.modals.create')
    @include('users.modals.delete')
    @include('users.modals.edit')

@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // اسکریپت مربوط به مودال ویرایش مشخصات یک کاربر
            document.querySelectorAll('.editUserBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('edit_name').value = this.dataset.name;
                    document.getElementById('edit_username').value = this.dataset.username;
                    document.getElementById('edit_email').value = this.dataset.email;
                    document.getElementById('edit_phone').value = this.dataset.phone;
                    document.getElementById('edit_role').value = this.dataset.role;
                    document.getElementById('edit_active').checked = this.dataset.active == 1;
                    document.getElementById('editUserForm').action = '/users/' + this.dataset.id;
                    const modal = document.getElementById('editUserModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });

            // اسکریپت مربوط به مودال تغییر رمز ورود یک کاربر
            document.querySelectorAll('.changePasswordBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('passwordUserName').textContent = this.dataset.name;
                    document.getElementById('changePasswordForm').action = '/users/' + this.dataset
                        .id +
                        '/password';
                    const modal = document.getElementById('changePasswordModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });

            // اسکریپت مربوط به مودال حذف یک کاربر
            document.querySelectorAll('.deleteUserBtn').forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('deleteUserName').textContent = this.dataset.name;
                    document.getElementById('deleteUserForm').action = '/users/' + this.dataset.id;
                    const modal = document.getElementById('deleteUserModal');
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                });
            });

        });
    </script>
@endpush
