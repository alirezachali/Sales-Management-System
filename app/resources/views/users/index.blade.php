@extends('layouts.app')

@section('title', 'مدیریت کاربران')

@section('content')

<!-- Users Card Header -->
<div class="page-header d-flex justify-content-between align-items-center mb-4">



<!-- Success Alert Section -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                title="یستن"></button>

    </div>
@endif

<!-- Error Alert Section -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                title="یستن"></button>

    </div>
@endif




    <div>
        <h3 class="mb-1">مدیریت کاربران</h3>
        <p class="text-muted mb-0">مدیریت کاربران سیستم</p>
    </div>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createUserModal"
            title="برای افزودن کاربر جدید به سیستم کلیک کنید">

        <i class="bi bi-plus-circle"></i>
        کاربر جدید
    </button>

</div>

<!-- Users Card Body -->
<div class="card shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <!-- Users Table -->
            <table class="table table-hover align-middle mb-0">

                <!-- Start Users Table Head -->
                <thead>

                    <tr>

                        <th width="60">#</th>

                        <th>نام</th>

                        <th>نام کاربری</th>

                        <th>وضعیت</th>

                        <th>نقش</th>

                        <th>آخرین ورود</th>

                        <th width="180">عملیات</th>

                    </tr>

                </thead>
                <!-- End Users Table Head -->

                <!-- Start Users Table Body -->
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
                                {{ $user->role?->display_name ?? '-' }}
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
                                        data-bs-target="#editUserModal"
                                        title="برای ویرایش این کاربر کلیک کنید">

                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Change Password User Button -->
                                <button class="btn btn-sm btn-info changePasswordBtn"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changePasswordModal"
                                        title="برای تغییر کلمه عبور این کاربر کلیک کنید">

                                    <i class="bi bi-key"></i>
                                </button>

                                <!-- Delete User Button -->
                                <button class="btn btn-sm btn-danger deleteUserBtn"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal"
                                        title="برای حذف این کاربر کلیک کنید">

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
                <!-- End Users Table Body -->

            </table>
            <!-- End Users Table -->

        </div>
    </div>
</div>


<div class="mt-3">

    {{ $users->links() }}

</div>



<!-- include External Modals File -->
@include('users.modals.changepassword')
@include('users.modals.create')
@include('users.modals.delete')
@include('users.modals.edit')



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

        const modal = document.getElementById('deleteUserModal');

        bootstrap.Modal.getOrCreateInstance(modal).show();

    });

});
</script>
<!-- End Delete User Modal Script -->

@endpush
