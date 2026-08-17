@extends('layouts.app')
@section('title', 'مدیریت تامین کنندگان')
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
                    تامین‌کنندگان
                </h3>
                <small class="text-muted">
                    مدیریت اطلاعات تامین کنندگان فروشگاه
                </small>
            </div>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplierModal"
                title="برای اضافه کردن تامین کننده جدید کلیک کنید">
                <i class="bi bi-plus-lg"></i>
                افزودن تامین‌کننده
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
                            <button class="btn btn-primary w-100" title="برای شروع جستجو کلیک کنید">
                                جستجو
                            </button>
                        </div>

                        <div class="col-lg-5 text-end">
                            <span class="badge bg-info fs-6">
                                تعداد تامین کنندگان :
                                {{-- {{ $customers->total() }} --}}1
                            </span>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card glass-card">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th width="70">شناسه</th>
                                <th>نام</th>
                                <th width="130">موبایل</th>
                                <th width="150">شهر</th>
                                <th width="70">نوع</th>
                                <th width="90">وضعیت</th>
                                <th width="90">عملیات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->code }}</td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->mobile }}</td>
                                    <td>{{ $supplier->city ?? '-' }}</td>
                                    <td>{{ $supplier->type === 'company' ? 'حقوقی' : 'حقیقی' }}</td>
                                    <td>
                                        @if ($supplier->is_active)
                                            <span class="badge bg-success">فعال</span>
                                        @else
                                            <span class="badge bg-secondary">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editSupplierModal{{ $supplier->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteSupplierModal{{ $supplier->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- مودال ویرایش --}}
                                <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">ویرایش تامین‌کننده: {{ $supplier->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @include('suppliers._form', [
                                                        'supplier' => $supplier,
                                                    ])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">انصراف</button>
                                                    <button type="submit" class="btn btn-primary">ذخیره
                                                        تغییرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- مودال حذف --}}
                                <div class="modal fade" id="deleteSupplierModal{{ $supplier->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">حذف تامین‌کننده</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    آیا از حذف تامین‌کننده «{{ $supplier->name }}» مطمئن هستید؟
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">انصراف</button>
                                                    <button type="submit" class="btn btn-danger">حذف</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">هیچ تامین‌کننده‌ای ثبت نشده
                                        است.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $suppliers->links() }}
            </div>

        </div>

    </div>

    {{-- مودال ساخت تامین‌کننده جدید --}}
    <div class="modal fade" id="createSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" dir="rtl">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">افزودن تامین‌کننده جدید</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('suppliers._form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-primary">ثبت تامین‌کننده</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- اگه یکی از فرم‌ها خطای validation داشته باشه، مودال مربوطه رو خودکار باز کن --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var modalEl = document.getElementById('createSupplierModal');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
        </script>
    @endif

@endsection
