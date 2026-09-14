<div dir="rtl">

    {{--=================== پیغام موفقیت ===================--}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    <div class="row g-4">

        {{--=================== ستون تصویر و اطلاعات کلی ===================--}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-3">
                <div class="card-body text-center py-4">

                    @if ($editing)
                        {{-- آپلود تصویر در حالت ویرایش --}}
                        <div class="profile-avatar-circle mb-3 mx-auto">
                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" alt="avatar">
                            @elseif ($currentAvatar)
                                <img src="{{ asset('storage/' . $currentAvatar) }}" alt="avatar">
                            @else
                                <span class="avatar-initials">{{ $user->initials }}</span>
                            @endif
                        </div>

                        <div class="mb-2">
                            <input type="file" wire:model="avatar" accept=".png,.jpg,.jpeg,.webp"
                                class="form-control @error('avatar') is-invalid @enderror">
                            <div wire:loading wire:target="avatar" class="form-text text-info">
                                <span class="spinner-border spinner-border-sm"></span>
                                در حال بارگذاری تصویر...
                            </div>
                            @error('avatar')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">PNG / JPG / WEBP — حداکثر ۲ مگابایت</small>
                        </div>
                    @else
                        <div class="profile-avatar-circle mb-3 mx-auto">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="avatar">
                            @else
                                <span class="avatar-initials">{{ $user->initials }}</span>
                            @endif
                        </div>
                    @endif

                    <h3 class="mb-1">{{ $user->name }}</h3>
                    <div class="text-muted mb-2">
                        <i class="bi bi-at"></i>{{ $user->username }}
                    </div>
                    <span class="badge bg-primary text-dark">{{ $user->role?->display_name ?? 'کاربر' }}</span>

                    <div class="mt-4 d-flex justify-content-center">
                        @if ($user->id === auth()->id())
                            @if ($editing)
                                <button type="button" class="btn btn-outline-secondary" wire:click="cancelEditing"
                                    title="بدون ذخیره بازگشت">
                                انصراف
                                </button>
                            @else
                                <button type="button" class="btn btn-primary" wire:click="startEditing"
                                    title="ویرایش مشخصات شخصی">
                                    <i class="bi bi-pencil-fill"></i>
                                    ویرایش مشخصات
                                </button>
                            @endif
                        @else
                            <span class="text-muted small">نمایش پروفایل کاربر</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{--=================== ستون جزئیات ===================--}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-person-lines-fill text-primary"></i>
                        مشخصات کاربر
                    </h3>
                </div>

                <div class="card-body">
                    @if ($editing)
                        <form wire:submit="save">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">نام نمایشی <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">نام کاربری <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="username" dir="ltr"
                                        class="form-control text-end @error('username') is-invalid @enderror">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">فقط حروف انگلیسی، عدد و . _ -</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ایمیل</label>
                                    <input type="email" wire:model="email" dir="ltr"
                                        class="form-control text-end @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-secondary mb-0 py-2 small">
                                        <i class="bi bi-lock-fill"></i>
                                        موبایل، کد ملی و آدرس قابل ویرایش نیستند؛ برای تغییر آنها با مدیر سیستم تماس بگیرید.
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-2">
                                    <div class="text-muted mb-2">
                                        تغییر رمز عبور (اختیاری — اگر نمی‌خواهید رمز را عوض کنید خالی بگذارید)
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">رمز عبور فعلی</label>
                                    <input type="password" wire:model="current_password" dir="ltr"
                                        class="form-control text-end @error('current_password') is-invalid @enderror">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">رمز عبور جدید</label>
                                    <input type="password" wire:model="password" dir="ltr"
                                        class="form-control text-end @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تکرار رمز عبور جدید</label>
                                    <input type="password" wire:model="password_confirmation" dir="ltr"
                                        class="form-control text-end">
                                </div>

                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                        wire:target="save">
                                        <span wire:loading wire:target="save"
                                            class="spinner-border spinner-border-sm"></span>
                                        <i class="bi bi-check-lg"></i>
                                        ذخیره تغییرات
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary"
                                        wire:click="cancelEditing">
                                        انصراف
                                    </button>
                                </div>

                            </div>
                        </form>
                    @else
                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">نام نمایشی</div>
                                    <div class="profile-field-value">{{ $user->name }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">نام کاربری</div>
                                    <div class="profile-field-value" dir="ltr">{{ $user->username }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">ایمیل</div>
                                    <div class="profile-field-value" dir="ltr">{{ $user->email ?: '—' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">موبایل</div>
                                    <div class="profile-field-value" dir="ltr">{{ $user->phone ?: '—' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">کد ملی</div>
                                    <div class="profile-field-value" dir="ltr">
                                        {{ $user->employee?->national_code ?: '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">عنوان شغلی</div>
                                    <div class="profile-field-value">
                                        {{ $user->employee?->job_title ?: '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="profile-field">
                                    <div class="profile-field-label">آدرس</div>
                                    <div class="profile-field-value">
                                        {{ $user->employee?->address ?: '—' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">وضعیت حساب</div>
                                    <div class="profile-field-value">
                                        @if ($user->is_active)
                                            <span class="badge bg-success text-dark">فعال</span>
                                        @else
                                            <span class="badge bg-danger text-dark">غیرفعال</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="profile-field">
                                    <div class="profile-field-label">آخرین ورود</div>
                                    <div class="profile-field-value">
                                        {{ $user->last_login_at ? jalaliDateTime($user->last_login_at) : '—' }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
