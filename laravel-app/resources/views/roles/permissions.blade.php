@extends('layouts.app')
@section('title', 'مجوزهای نقش')
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

    <div class="page-header d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                مجوز های :
                <span class="badge bg-{{ $role->color }}">
                    {{ $role->name }}
                    <i class="{{ $role->icon }}"></i>
                </span>
            </h3>
        </div>

        <div class="text-end">
            <button class="btn btn-success">
                <i class="bi bi-check-circle"></i>
                ذخیره تغییرات
            </button>

            <a href="{{ route('roles.index') }}"class="btn btn-secondary" title="بازگشت به صفحه لیست نقش ها">
                <i class="bi bi-arrow-right"></i>
                بازگشت
            </a>

        </div>

    </div>

    <form action="{{ route('roles.permissions.sync', $role) }}"method="POST">
        @csrf

        <div class="permissions-grid">

            @foreach ($groups as $group)
                <div class="permission-column">

                    <div class="card shadow-sm">

                        <div class="card-header">
                            <strong>
                                <i class="bi {{ $group->icon }}"></i>
                                {{ $group->name }}
                            </strong>
                        </div>

                        <div class="card-body">

                            <div class="permissions-list">

                                @foreach ($group->permissions as $permission)
                                    <div class="form-check permission-item">

                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->id }}" id="permission{{ $permission->id }}"
                                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>

                                        <label class="form-check-label" for="permission{{ $permission->id }}">
                                            {{ $permission->display_name }}
                                        </label>

                                    </div>
                                @endforeach

                            </div>

                        </div>

                    </div>

                </div>
            @endforeach

        </div>

        <hr class="h-r">

        <div class="text-end">
            <button class="btn btn-success">
                <i class="bi bi-check-circle"></i>
                ذخیره تغییرات
            </button>
        </div>

    </form>
@endsection
