{{-- پیغام‌های موفقیت و خطا — ویرایش فقط از این فایل --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show glass-card flash-alert" role="alert" data-flash-alert>
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن" aria-label="بستن"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show glass-card flash-alert" role="alert" data-flash-alert>
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن" aria-label="بستن"></button>
    </div>
@endif
