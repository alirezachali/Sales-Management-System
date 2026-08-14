@extends('layouts.app')
@section('title', 'مدیریت نقش‌ها')
@section('content')

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

    <!-- Start Role Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                مدیریت نقش‌ها
            </h3>
            <p class="text-muted mb-0">
                مدیریت نقش‌های کاربران سیستم
            </p>
        </div>
        <!-- Add New Role Button  -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal"
            title="برای افزودن نقش جدید به سیستم کلیک کنید">
            <i class="bi bi-plus-circle"></i>
            افزودن نقش جدید
        </button>
    </div>
    <!-- End Role Page Header -->

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">

                <!-- Start Role Table -->
                <table class="table table-hover align-middle mb-0">

                    <!-- Role Table Head -->
                    <thead>
                        <tr>
                            <th width="70">ردیف</th>
                            <th>نام نقش</th>
                            <th>شناسه</th>
                            <th>توضیحات</th>
                            <th width="120">تعداد کاربران</th>
                            <th width="180">عملیات</th>
                        </tr>
                    </thead>

                    <!-- Start Role Table Body -->
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <!-- ردیف -->
                                <td>
                                    {{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}
                                </td>
                                <!-- نام نقش -->
                                <td>
                                    {{ $role->display_name }}
                                </td>
                                <!-- شناسه -->
                                <td>
                                    <span class="badge bg-{{ $role->color }}">
                                        {{ $role->name }}
                                        <i class="{{ $role->icon }}"></i>
                                    </span>
                                </td>
                                <!-- توضیحات -->
                                <td>
                                    {{ $role->description }}
                                </td>
                                <!-- تعداد کاربران -->
                                <td>
                                    <span class="badge bg-info">
                                        {{ $role->users()->count() }}
                                    </span>
                                </td>
                                <!-- عملیات -->
                                <td>
                                    <!-- Edit Role Button -->
                                    <button class="btn btn-sm btn-warning editRoleBtn" data-id="{{ $role->id }}"
                                        data-display_name="{{ $role->display_name }}" data-name="{{ $role->name }}"
                                        data-description="{{ $role->description }}" data-bs-toggle="modal"
                                        data-bs-target="#editRoleModal" title="برای ویرانش این نقش کلیک کنید">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Delete Role Button -->
                                    <button class="btn btn-sm btn-danger deleteRoleBtn" data-id="{{ $role->id }}"
                                        data-name="{{ $role->name }}" data-bs-toggle="modal"
                                        data-bs-target="#deleteRoleModal" title="برای حذف این نقش کلیک کنید">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- Edit Permissions for Role Button -->
                                    <a href="{{ route('roles.permissions', $role) }}" class="btn btn-sm btn-info"
                                        title="برای ویرایش مجوز های این نقش کلیک کنید">
                                        <i class="bi bi-shield-lock"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- اگر اطلاعاتی در دیتابیس وجود نداشت اطلاعات زیر را نمایش میدهد -->
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    هیچ نقشی ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- End Role Table Body -->
                </table>
                <!-- End Role Table -->
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $roles->links() }}</div>

    @include('roles.modals.create')
    @include('roles.modals.edit')
    @include('roles.modals.delete')

@endsection

@push('scripts')
    <!-- Start Edit Role Modal Script -->
    <script>
        document.querySelectorAll('.editRoleBtn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_name').value = this.dataset.display_name;
                document.getElementById('edit_slug').value = this.dataset.name;
                document.getElementById('edit_description').value = this.dataset.description;
                document.getElementById('editRoleForm').action = '/roles/' + this.dataset.id;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('editRoleModal')).show();
            });
        });
    </script>
    <!-- End Edit Role Modal Script -->

    <!-- Start Delete Role Modal Script -->
    <script>
        document.querySelectorAll('.deleteRoleBtn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('deleteRoleName').textContent = this.dataset.name;
                document.getElementById('deleteRoleForm').action = '/roles/' + this.dataset.id;
                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById('deleteRoleModal')).show();
            });
        });
    </script>
    <!-- End Delete Role Modal Script -->
@endpush
