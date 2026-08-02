@extends('layouts.app')

@section('title', 'مدیریت نقش‌ها')

@section('content')


<!-- Success Notification -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Error Notification -->
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
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
    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#createRoleModal">
        <i class="bi bi-plus-circle"></i>
            افزودن نقش جدید
    </button>

</div>
<!-- End Role Page Header -->

<!-- Start Role Table -->
<div class="card shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <!-- Role Table Head -->
                <thead>
                    <tr>
                        <th width="70">#</th>
                        <th>نام نقش</th>
                        <th>شناسه</th>
                        <th>توضیحات</th>
                        <th width="120">تعداد کاربران</th>
                        <th width="180">عملیات</th>
                    </tr>
                </thead>

                <!-- Role Table Body -->
                <tbody>

                    @forelse($roles as $role)

                        <tr>
                            <td>
                                {{ $loop->iteration + ($roles->currentPage()-1) * $roles->perPage() }}
                            </td>

                            <td>
                                {{ $role->display_name }}
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $role->name }}
                                </span>
                            </td>

                            <td>
                                {{ $role->description }}
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ $role->users()->count() }}
                                </span>
                            </td>

                            <td>
                                <!-- Edit Role Button -->
                                <button class="btn btn-sm btn-warning editRoleBtn"
                                        data-id="{{ $role->id }}"
                                        data-display_name="{{ $role->display_name }}"
                                        data-name="{{ $role->name }}"
                                        data-description="{{ $role->description }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editRoleModal">

                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Delete Role Button -->
                                <button class="btn btn-sm btn-danger deleteRoleBtn"
                                        data-id="{{ $role->id }}"
                                        data-name="{{ $role->name }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteRoleModal">

                                    <i class="bi bi-trash"></i>
                                </button>

                                <a href="{{ route('roles.permissions',$role) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-shield-lock"></i>
                                </a>

                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                هیچ نقشی ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
<!-- End Role Table -->

<div class="mt-3">

    {{ $roles->links() }}

</div>

<!-- Start Add New Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">

    <div class="modal-dialog">

        <form action="{{ route('roles.store') }}" method="POST" class="modal-content">

            @csrf

            <div class="modal-header">

                <h5 class="modal-title">
                    نقش جدید
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        نام نقش
                    </label>

                    <input type="text" name="display_name" class="form-control" required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        شناسه (عنوان نقش به انگلیسی و حروف کوچک)
                    </label>

                    <input type="text" name="name" class="form-control" required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        توضیحات
                    </label>

                    <textarea name="description" class="form-control" rows="3"></textarea>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    انصراف
                </button>

                <button class="btn btn-primary">
                    ذخیره
                </button>

            </div>

        </form>

    </div>

</div>
<!-- End Add New Role Modal -->

<!-- Start Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">

    <div class="modal-dialog">

        <form id="editRoleForm" method="POST" class="modal-content">

            @csrf
            @method('PUT')

            <div class="modal-header">

                <h5 class="modal-title">
                    ویرایش نقش
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">
                        نام نقش
                    </label>
                    <input id="edit_name" name="display_name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        شناسه
                    </label>
                    <input id="edit_slug" name="name" class="form-control">

                </div>

                <div class="mb-3">
                    <label class="form-label">
                        توضیحات
                    </label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    انصراف
                </button>

                <button class="btn btn-primary">
                    ذخیره تغییرات
                </button>

            </div>

        </form>

    </div>

</div>
<!-- End Edit Role Modal -->

<!-- Start Delete Role Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1">

    <div class="modal-dialog">

        <form id="deleteRoleForm" method="POST" class="modal-content">

            @csrf
            @method('DELETE')

            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title">
                    حذف نقش
                </h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <p>
                    آیا از حذف نقش
                    <strong id="deleteRoleName"></strong>
                    اطمینان دارید؟
                </p>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    انصراف
                </button>

                <button class="btn btn-danger">
                    حذف
                </button>

            </div>

        </form>

    </div>

</div>
<!-- End Delete Role Modal -->

@endsection

@push('scripts')

<!-- Start Edit Role Modal Script -->
<script>
document.querySelectorAll('.editRoleBtn').forEach(button=>{

    button.addEventListener('click',function(){

        document.getElementById('edit_name').value=this.dataset.display_name;

        document.getElementById('edit_slug').value=this.dataset.name;

        document.getElementById('edit_description').value=this.dataset.description;

        document.getElementById('editRoleForm').action='/roles/'+this.dataset.id;

        bootstrap.Modal.getOrCreateInstance(document.getElementById('editRoleModal')).show();

    });

});
</script>
<!-- End Edit Role Modal Script -->

<!-- Start Delete Role Modal Script -->
<script>
document.querySelectorAll('.deleteRoleBtn').forEach(button=>{

    button.addEventListener('click',function(){

        document.getElementById('deleteRoleName').textContent=this.dataset.name;

        document.getElementById('deleteRoleForm').action='/roles/'+this.dataset.id;

        bootstrap.Modal
            .getOrCreateInstance(
                document.getElementById('deleteRoleModal')
            )
            .show();

    });

});
</script>
<!-- End Delete Role Modal Script -->



@endpush