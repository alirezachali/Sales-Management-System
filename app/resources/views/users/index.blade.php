@extends('layouts.app')

@section('title', 'مدیریت کاربران')

@section('content')


@if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

    </div>

@endif

@if(session('error'))

<div class="alert alert-danger alert-dismissible fade show">

    {{ session('error') }}

    <button
        class="btn-close"
        data-bs-dismiss="alert"></button>

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

                                <!-- Edit User Button -->
                                <button class="btn btn-sm btn-warning editUserBtn"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone }}"
                                        data-active="{{ $user->is_active }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal">

                                    <i class="bi bi-pencil"></i>

                                </button>

                                <!-- Change Password User Button -->
                                <button class="btn btn-sm btn-info changePasswordBtn"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changePasswordModal">

                                    <i class="bi bi-key"></i>

                                </button>

                                <!-- Delete User Button -->
                                <button class="btn btn-sm btn-danger deleteUserBtn"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal">

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


<!-- Start Add User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg">

        @if ($errors->any())

            <div class="alert alert-danger">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif

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
                            required
                            value="{{ old('name') }}">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            نام کاربری

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="username"
                            required
                            value="{{ old('username') }}">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            ایمیل

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            value="{{ old('email') }}">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            موبایل

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            name="phone"
                            value="{{ old('phone') }}">

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
                                checked
                                value="{{ old('is_active', true) ? 'checked' : '' }}">

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
<!-- End Add User Modal -->

<!-- Start Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <form
            id="editUserForm"
            method="POST"
            class="modal-content">

            @csrf
            @method('PUT')

            <div class="modal-header">

                <h5 class="modal-title">

                    ویرایش کاربر

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

                            نام

                        </label>

                        <input
                            id="edit_name"
                            name="name"
                            class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            نام کاربری

                        </label>

                        <input
                            id="edit_username"
                            name="username"
                            class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            ایمیل

                        </label>

                        <input
                            id="edit_email"
                            name="email"
                            class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            موبایل

                        </label>

                        <input
                            id="edit_phone"
                            name="phone"
                            class="form-control">

                    </div>

                    <div class="col-12">

                        <div class="form-check form-switch">

                            <input
                                id="edit_active"
                                type="checkbox"
                                class="form-check-input"
                                name="is_active"
                                value="1">

                            <label class="form-check-label">

                                کاربر فعال باشد

                            </label>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-primary">

                    ذخیره تغییرات

                </button>

            </div>

        </form>

    </div>

</div>
<!-- End Edit User Modal -->


<!-- Start Change Password User Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">

    <div class="modal-dialog">

        <form
            id="changePasswordForm"
            method="POST"
            class="modal-content">

            @csrf
            @method('PUT')

            <div class="modal-header">

                <h5 class="modal-title">

                    تغییر رمز عبور

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <p class="text-muted">

                    تغییر رمز عبور کاربر

                    <strong id="passwordUserName"></strong>

                </p>

                <div class="mb-3">

                    <label class="form-label">

                        رمز عبور جدید

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        تکرار رمز عبور

                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        required>

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

        </form>

    </div>

</div>
<!-- End Change Password User Modal -->


<!-- Start Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">

    <div class="modal-dialog">

        <form
            id="deleteUserForm"
            method="POST"
            class="modal-content">

            @csrf
            @method('DELETE')

            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title">

                    حذف کاربر

                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <p>

                    آیا از حذف کاربر

                    <strong id="deleteUserName"></strong>

                    اطمینان دارید؟

                </p>

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

        </form>

    </div>

</div>
<!-- End Delete User Modal -->


@endsection


@push('scripts')

<!-- Start Add User Modal Script -->
@if ($errors->any())
<script>

document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('createUserModal');

    bootstrap.Modal.getOrCreateInstance(modal).show();

});

</script>
@endif
<!-- End Add User Modal Script -->


<!-- Start Edit User Modal Script -->
<script>

document.querySelectorAll('.editUserBtn').forEach(button=>{

    button.addEventListener('click',function(){

        document.getElementById('edit_name').value=this.dataset.name;

        document.getElementById('edit_username').value=this.dataset.username;

        document.getElementById('edit_email').value=this.dataset.email;

        document.getElementById('edit_phone').value=this.dataset.phone;

        document.getElementById('edit_active').checked=this.dataset.active==1;

        document.getElementById('editUserForm').action='/users/'+this.dataset.id;

        const modal = document.getElementById('editUserModal');

        bootstrap.Modal.getOrCreateInstance(modal).show();

    });

});

</script>
<!-- End Edit User Modal Script -->

<!-- Start Change Password User Modal Script -->
<script>
document.querySelectorAll('.changePasswordBtn').forEach(button=>{

    button.addEventListener('click',function(){

        document.getElementById('passwordUserName').textContent=this.dataset.name;

        document.getElementById('changePasswordForm').action='/users/'+this.dataset.id+'/password';

        const modal = document.getElementById('changePasswordModal');

        bootstrap.Modal.getOrCreateInstance(modal).show();

    });

});
</script>
<!-- End Change Password User Modal Script -->

<!-- Start Delete User Modal Script -->
<script>
document.querySelectorAll('.deleteUserBtn').forEach(button=>{

    button.addEventListener('click',function(){

        document.getElementById('deleteUserName').textContent=this.dataset.name;

        document.getElementById('deleteUserForm').action='/users/'+this.dataset.id;

        new bootstrap.Modal(
            document.getElementById('deleteUserModal')
        ).show();

    });

});
</script>
<!-- End Delete User Modal Script -->

@endpush
