{{-- پیغام‌های موفقیت و خطا — ویرایش فقط از این فایل --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show glass-card flash-alert" role="alert"
        data-flash-alert data-notify-sound="success">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن" aria-label="بستن"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show glass-card flash-alert" role="alert"
        data-flash-alert data-notify-sound="error">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن" aria-label="بستن"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show glass-card flash-alert" role="alert"
        data-flash-alert data-notify-sound="warning">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن" aria-label="بستن"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show glass-card flash-alert" role="alert"
        data-flash-alert data-notify-sound="info">
        <i class="bi bi-info-circle-fill me-2"></i>
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن" aria-label="بستن"></button>
    </div>
@endif
