
@extends('layouts.app')

@section('title', 'مدیریت دسته‌بندی محصولات')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

    </div>
@endif

@section('content')

<div class="container">

    <div class="card dashboard-card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                <i class="bi bi-tags-fill me-2"></i>
                مدیریت دسته‌بندی محصولات
            </h4>

            <button class="btn btn-success" id="btnAddCategory">
                <i class="bi bi-plus-circle"></i>
                افزودن دسته‌بندی
            </button>

        </div>

        <div class="card-body">

            <div class="row mb-4">

                <div class="col-md-4">

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text"
                               class="form-control"
                               placeholder="جستجوی دسته‌بندی...">

                    </div>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle text-center">

                    <thead class="table">

                    <tr>

                        <th width="70">ردیف</th>

                        <th>نام دسته‌بندی</th>

                        <th>توضیحات</th>

                        <th width="140">تعداد کالا</th>

                        <th width="180">تاریخ ایجاد</th>

                        <th>وضعیت</th>

                        <th width="140">عملیات</th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($categories as $category)

                        <tr>

                            <td>
                                {{ $loop->iteration + (($categories->currentPage()-1) * $categories->perPage()) }}
                            </td>

                            <td class="text-start">
                                {{ $category->name }}
                            </td>

                            <td>

                                @if($category->description)

                                    {{ $category->description }}

                                @else

                                    <span class="text-muted">
                                        ---
                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($category->products_count)

                                    <span class="badge bg-primary">

                                        {{ $category->products_count }}

                                    </span>

                                @else

                                    <span class="badge bg-secondary">

                                        0

                                    </span>

                                @endif

                            </td>

                            <td>

                                {{ $category->created_at->format('Y/m/d') }}

                            </td>

                            <td>

                                @if($category->is_active)

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

                                <div class="btn-group">
                                
                                <button type="button"
                                       class="btn btn-sm btn-primary btn-edit"
                                       data-id="{{ $category->id }}"
                                       data-name="{{ $category->name }}"
                                       data-description="{{ $category->description }}"
                                       data-active="{{ $category->is_active }}">

                                    <i class="bi bi-pencil-square"></i>

                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-danger btn-delete"
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}">

                                    <i class="bi bi-trash"></i>

                                </button>
                                
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="py-5">

                                <i class="bi bi-inbox fs-1 text-secondary"></i>

                                <div class="mt-3">

                                    هنوز هیچ دسته‌بندی ثبت نشده است.

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-4 d-flex justify-content-center">

                {{ $categories->links() }}

            </div>

        </div>

    </div>

</div>

@include('categories.modals.create')

@include('categories.modals.edit')

@include('categories.modals.delete')

@endsection

@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const createModal = new bootstrap.Modal(
        document.getElementById('createCategoryModal')
    );

    document.getElementById('btnAddCategory')
        .addEventListener('click', function () {

            document.getElementById('createCategoryForm').reset();

            createModal.show();

        });



     const editModal = new bootstrap.Modal(
    document.getElementById('editCategoryModal')
);

document.querySelectorAll('.btn-edit').forEach(btn => {

    btn.addEventListener('click', function () {

        document.getElementById('edit_id').value =
            this.dataset.id;


        document.getElementById('editCategoryForm').action =
            '/categories/' + this.dataset.id;


        document.getElementById('edit_name').value =
            this.dataset.name;

        document.getElementById('edit_description').value =
            this.dataset.description ?? '';

        document.getElementById('edit_is_active').checked =
            this.dataset.active == 1;

        editModal.show();

    });

});



const deleteModal = new bootstrap.Modal(
    document.getElementById('deleteCategoryModal')
);

document.querySelectorAll('.btn-delete').forEach(btn => {

    btn.addEventListener('click', function () {

        document.getElementById('delete_id').value =
            this.dataset.id;

        document.getElementById('deleteCategoryForm').action =
        '/categories/' + this.dataset.id;


        document.getElementById('delete_category_name').innerText =
            this.dataset.name;

        deleteModal.show();

    });

});   

});





</script>


