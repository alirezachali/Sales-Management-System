<div dir="rtl">

    {{-- Success Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{-- Error Alert --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{-- خطاهای اعتبارسنجی --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            لطفاً خطاهای زیر را برطرف کنید:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0" wire:loading.class="opacity-75">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-gear-fill text-primary"></i>
                    تنظیمات سیستم
                </h3>
                <small class="text-muted">
                    مدیریت اطلاعات فروشگاه و تنظیمات نرم افزار
                </small>
            </div>
            <button type="button" wire:click="save" class="btn btn-success" wire:loading.attr="disabled"
                wire:target="save">
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                <i class="bi bi-check-circle" wire:loading.remove wire:target="save"></i>
                ذخیره تغییرات
            </button>
        </div>

        <div class="card-body">

            <form wire:submit="save" enctype="multipart/form-data">

                <ul class="nav nav-tabs mb-4">

                    <li class="nav-item">
                        <button type="button" class="nav-link @if ($activeTab === 'store') active @endif"
                            wire:click="selectTab('store')">
                            <i class="bi bi-shop"></i>
                            اطلاعات فروشگاه
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link @if ($activeTab === 'sales') active @endif"
                            wire:click="selectTab('sales')">
                            <i class="bi bi-receipt"></i>
                            فروش
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link @if ($activeTab === 'print') active @endif"
                            wire:click="selectTab('print')">
                            <i class="bi bi-printer"></i>
                            چاپ
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link @if ($activeTab === 'barcode') active @endif"
                            wire:click="selectTab('barcode')">
                            <i class="bi bi-upc-scan"></i>
                            بارکد و لیبل
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link @if ($activeTab === 'system') active @endif"
                            wire:click="selectTab('system')">
                            <i class="bi bi-cpu"></i>
                            سیستم
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link @if ($activeTab === 'backup') active @endif"
                            wire:click="selectTab('backup')">
                            <i class="bi bi-database"></i>
                            پشتیبان گیری
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- ============================ اطلاعات فروشگاه ============================ --}}
                    @if ($activeTab === 'store')
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header">
                                        <strong>
                                            <i class="bi bi-shop"></i>
                                            اطلاعات فروشگاه
                                        </strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">

                                            <div class="col-md-6">
                                                <label class="form-label">نام فروشگاه</label>
                                                <input type="text" class="form-control" wire:model="data.store_name">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">نام مدیر</label>
                                                <input type="text" class="form-control" wire:model="data.manager_name">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">تلفن</label>
                                                <input type="text" class="form-control" wire:model="data.phone">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">موبایل</label>
                                                <input type="text" class="form-control" wire:model="data.mobile">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">ایمیل</label>
                                                <input type="email"
                                                    class="form-control @error('data.email') is-invalid @enderror"
                                                    wire:model="data.email">
                                                @error('data.email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">وب سایت</label>
                                                <input type="text" class="form-control" wire:model="data.website">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">آدرس</label>
                                                <textarea class="form-control" rows="3" wire:model="data.address"></textarea>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">

                                <div class="card shadow-sm border-0 mb-4">
                                    <div class="card-header">
                                        <strong>لوگوی فروشگاه</strong>
                                    </div>
                                    <div class="card-body text-center">
                                        <img src="{{ $store_logo && $store_logo->isPreviewable() ? $store_logo->temporaryUrl() : storeLogo() }}"
                                            class="img-fluid rounded border mb-3" style="max-height:180px">
                                        <input
                                            class="form-control @error('store_logo') is-invalid @enderror"
                                            type="file" wire:model="store_logo" accept=".png,.jpg,.jpeg,.webp">
                                        <div wire:loading wire:target="store_logo" class="text-muted small mt-1">
                                            در حال بارگذاری...
                                        </div>
                                        @error('store_logo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-2">PNG / JPG / WEBP</small>
                                    </div>
                                </div>

                                <div class="card shadow-sm border-0">
                                    <div class="card-header">
                                        <strong>Favicon</strong>
                                    </div>
                                    <div class="card-body text-center">
                                        <img src="{{ $store_favicon && $store_favicon->isPreviewable() ? $store_favicon->temporaryUrl() : storeFavicon() }}"
                                            class="rounded border p-2 mb-3" width="64" height="64">
                                        @if ($store_favicon)
                                            <div class="text-success small mb-2">
                                                <i class="bi bi-check-circle"></i>
                                                فایل انتخاب شد ({{ $store_favicon->getClientOriginalName() }})
                                            </div>
                                        @endif
                                        <input
                                            class="form-control @error('store_favicon') is-invalid @enderror"
                                            type="file" wire:model="store_favicon" accept=".png,.ico">
                                        <div wire:loading wire:target="store_favicon" class="text-muted small mt-1">
                                            در حال بارگذاری...
                                        </div>
                                        @error('store_favicon')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-2">PNG / ICO</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                    {{-- ============================ فروش ============================ --}}
                    @if ($activeTab === 'sales')
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <strong>
                                    <i class="bi bi-receipt"></i>
                                    تنظیمات فروش
                                </strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">

                                    <div class="col-md-4">
                                        <label class="form-label">پیشوند شماره فاکتور</label>
                                        <input type="text" class="form-control" wire:model="data.invoice_prefix">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">شماره شروع فاکتور</label>
                                        <input type="number" class="form-control" wire:model="data.invoice_start">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">تعداد ارقام شماره فاکتور</label>
                                        <select class="form-select" wire:model="data.invoice_digits">
                                            @for ($i = 4; $i <= 10; $i++)
                                                <option value="{{ $i }}">{{ $i }} رقم</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">واحد پول</label>
                                        <input type="text" class="form-control" wire:model="data.currency">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">تعداد اعشار قیمت</label>
                                        <select class="form-select" wire:model="data.price_decimal">
                                            @for ($i = 0; $i <= 4; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">نرخ مالیات (%)</label>
                                        <input type="number" class="form-control" wire:model="data.tax_percent">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">تخفیف پیش فرض (%)</label>
                                        <input type="number" class="form-control" wire:model="data.default_discount">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">هشدار اتمام موجودی</label>
                                        <input type="number" class="form-control" wire:model="data.stock_alert">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">حداکثر اقلام هر فاکتور</label>
                                        <input type="number" class="form-control" wire:model="data.max_invoice_items">
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.allow_negative_stock" id="allow_negative_stock">
                                            <label class="form-check-label" for="allow_negative_stock">
                                                اجازه فروش با موجودی منفی
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.auto_print_invoice" id="auto_print_invoice">
                                            <label class="form-check-label" for="auto_print_invoice">
                                                چاپ خودکار فاکتور بعد از ثبت
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.barcode_sound" id="barcode_sound">
                                            <label class="form-check-label" for="barcode_sound">
                                                پخش صدای اسکن بارکد
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.confirm_delete_invoice" id="confirm_delete_invoice">
                                            <label class="form-check-label" for="confirm_delete_invoice">
                                                تایید قبل از حذف فاکتور
                                            </label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ============================ چاپ ============================ --}}
                    @if ($activeTab === 'print')
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <strong>
                                    <i class="bi bi-printer"></i>
                                    تنظیمات چاپ
                                </strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">

                                    <div class="col-md-4">
                                        <label class="form-label">اندازه کاغذ</label>
                                        <select class="form-select" wire:model="data.paper_size">
                                            <option value="58">58 میلی‌متر</option>
                                            <option value="80">80 میلی‌متر</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">تعداد نسخه چاپ</label>
                                        <input type="number" class="form-control" wire:model="data.print_copies">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">چاپ خودکار</label>
                                        <select class="form-select" wire:model="data.auto_print">
                                            <option value="1">فعال</option>
                                            <option value="0">غیرفعال</option>
                                        </select>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.print_logo" id="print_logo">
                                            <label class="form-check-label" for="print_logo">چاپ لوگوی فروشگاه</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.print_address" id="print_address">
                                            <label class="form-check-label" for="print_address">چاپ آدرس فروشگاه</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.print_phone" id="print_phone">
                                            <label class="form-check-label" for="print_phone">چاپ شماره تلفن</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.print_barcode" id="print_barcode">
                                            <label class="form-check-label" for="print_barcode">چاپ بارکد روی فاکتور</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.print_qrcode" id="print_qrcode">
                                            <label class="form-check-label" for="print_qrcode">چاپ QR Code</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.print_datetime" id="print_datetime">
                                            <label class="form-check-label" for="print_datetime">چاپ تاریخ و ساعت</label>
                                        </div>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label">متن پایین فاکتور</label>
                                        <textarea class="form-control" rows="4" wire:model="data.receipt_footer"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                    {{-- ============================ بارکد و لیبل ============================ --}}
                    @if ($activeTab === 'barcode')
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <strong>
                                    <i class="bi bi-upc-scan"></i>
                                    تنظیمات بارکد و چاپ لیبل
                                </strong>
                            </div>

                            <div class="card-body">
                                <div class="row g-4">

                                    <div class="col-md-4">
                                        <label class="form-label">پیشوند بارکد داخلی</label>
                                        <input type="text" class="form-control" wire:model="data.barcode_prefix">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">طول بارکد</label>
                                        <input type="number" class="form-control" wire:model="data.barcode_length">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">نوع بارکد</label>
                                        <select class="form-select" wire:model="data.barcode_type">
                                            <option value="CODE128">Code128</option>
                                        </select>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">
                                    <i class="bi bi-layout-text-window"></i>
                                    تنظیمات ظاهر لیبل
                                </h5>

                                <div class="row g-3">

                                    <div class="col-md-3">
                                        <label class="form-label">عرض لیبل (میلی‌متر)</label>
                                        <input type="number" class="form-control" min="20"
                                            wire:model="data.label_width">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">ارتفاع لیبل (میلی‌متر)</label>
                                        <input type="number" class="form-control" min="15"
                                            wire:model="data.label_height">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">تعداد چاپ پیش‌فرض</label>
                                        <input type="number" class="form-control" min="1"
                                            wire:model="data.label_default_quantity">
                                    </div>

                                </div>

                                <div class="mt-4">

                                    <label class="form-label d-block">موارد قابل نمایش روی لیبل</label>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="data.label_show_name" id="label_show_name">
                                        <label class="form-check-label" for="label_show_name">نمایش نام کالا</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="data.label_show_price" id="label_show_price">
                                        <label class="form-check-label" for="label_show_price">نمایش قیمت</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="data.label_show_barcode" id="label_show_barcode">
                                        <label class="form-check-label" for="label_show_barcode">نمایش بارکد میله‌ای</label>
                                    </div>

                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="data.label_show_code" id="label_show_code">
                                        <label class="form-check-label" for="label_show_code">نمایش شماره بارکد</label>
                                    </div>

                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="data.label_show_unit" id="label_show_unit">
                                        <label class="form-check-label" for="label_show_unit">نمایش واحد کالا</label>
                                    </div>

                                </div>

                            </div>
                        </div>
                    @endif

                    {{-- ============================ سیستم ============================ --}}
                    @if ($activeTab === 'system')
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <strong>
                                    <i class="bi bi-cpu"></i>
                                    تنظیمات سیستم
                                </strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">

                                    <div class="col-md-4">
                                        <label class="form-label">زبان برنامه</label>
                                        <select class="form-select" wire:model="data.system_language">
                                            <option value="fa">فارسی</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">منطقه زمانی</label>
                                        <select class="form-select" wire:model="data.timezone">
                                            <option value="Asia/Tehran">Asia / Tehran</option>
                                            <option value="UTC">UTC</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">قالب تاریخ</label>
                                        <select class="form-select" wire:model="data.date_format">
                                            <option value="Y/m/d">1405/05/01</option>
                                            <option value="Y-m-d">1405-05-01</option>
                                            <option value="j F Y">1 مرداد 1405</option>
                                            <option value="l j F Y">شنبه 1 مرداد 1405</option>
                                        </select>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.system_log" id="system_log">
                                            <label class="form-check-label" for="system_log">ثبت گزارش فعالیت کاربران</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.remember_login" id="remember_login">
                                            <label class="form-check-label" for="remember_login">فعال بودن ورود خودکار</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.maintenance_mode" id="maintenance_mode">
                                            <label class="form-check-label" for="maintenance_mode">حالت تعمیر و نگهداری</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.developer_mode" id="developer_mode">
                                            <label class="form-check-label" for="developer_mode">حالت توسعه دهنده</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.enable_cache" id="enable_cache">
                                            <label class="form-check-label" for="enable_cache">فعال بودن کش سیستم</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.check_update" id="check_update">
                                            <label class="form-check-label" for="check_update">بررسی بروزرسانی هنگام اجرا</label>
                                        </div>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="row">

                                    <div class="col-md-6">
                                        <label class="form-label">مدت زمان انقضای نشست (دقیقه)</label>
                                        <input type="number" class="form-control" wire:model="data.session_timeout">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">تعداد رکورد در هر صفحه</label>
                                        <input type="number" class="form-control" wire:model="data.pagination_limit">
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ============================ پشتیبان‌گیری ============================ --}}
                    @if ($activeTab === 'backup')
                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <strong>
                                    <i class="bi bi-database"></i>
                                    پشتیبان گیری و بازیابی
                                </strong>
                            </div>

                            <div class="card-body">
                                <div class="row">

                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-body text-center">
                                                <i class="bi bi-cloud-arrow-down display-3 text-success"></i>
                                                <h5 class="mt-3">تهیه نسخه پشتیبان</h5>
                                                <p class="text-muted">
                                                    از اطلاعات نرم افزار نسخه پشتیبان تهیه کنید.
                                                </p>
                                                <button type="button" class="btn btn-success"
                                                    wire:click="createBackup" wire:loading.attr="disabled"
                                                    wire:target="createBackup">
                                                    <span wire:loading wire:target="createBackup"
                                                        class="spinner-border spinner-border-sm"></span>
                                                    <i class="bi bi-download" wire:loading.remove
                                                        wire:target="createBackup"></i>
                                                    ایجاد نسخه پشتیبان
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-body text-center">
                                                <i class="bi bi-cloud-arrow-up display-3 text-primary"></i>
                                                <h5 class="mt-3">بازیابی اطلاعات</h5>
                                                <p class="text-muted">فایل پشتیبان را انتخاب کنید.</p>
                                                <input type="file"
                                                    class="form-control mb-3 @error('restoreFile') is-invalid @enderror"
                                                    wire:model="restoreFile" accept=".sql,.zip">
                                                @error('restoreFile')
                                                    <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                                                @enderror
                                                <button type="button" class="btn btn-primary"
                                                    wire:click="restoreBackup"
                                                    wire:confirm="آیا مطمئن هستید؟ اطلاعات فعلی با نسخه‌ی پشتیبان جایگزین می‌شود."
                                                    wire:loading.attr="disabled" wire:target="restoreBackup,restoreFile">
                                                    <span wire:loading wire:target="restoreBackup"
                                                        class="spinner-border spinner-border-sm"></span>
                                                    <i class="bi bi-upload" wire:loading.remove
                                                        wire:target="restoreBackup"></i>
                                                    بازیابی اطلاعات
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="row">

                                    <div class="col-md-6">
                                        <label class="form-label">مسیر ذخیره نسخه های پشتیبان</label>
                                        <input type="text" class="form-control" wire:model="data.backup_path">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">تعداد نسخه قابل نگهداری</label>
                                        <input type="number" class="form-control" wire:model="data.backup_keep">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">فرمت فایل</label>
                                        <select class="form-select" wire:model="data.backup_format">
                                            <option value="zip">ZIP</option>
                                            <option value="sql">SQL</option>
                                        </select>
                                    </div>

                                </div>

                                <hr class="my-4">

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.auto_backup" id="auto_backup">
                                            <label class="form-check-label" for="auto_backup">تهیه نسخه پشتیبان خودکار</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="data.backup_before_restore" id="backup_before_restore">
                                            <label class="form-check-label" for="backup_before_restore">
                                                قبل از بازیابی، نسخه پشتیبان تهیه شود
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <hr class="my-4">

                                {{-- فهرست نسخه‌های پشتیبان موجود --}}
                                <h5 class="mb-3">
                                    <i class="bi bi-clock-history"></i>
                                    نسخه‌های پشتیبان موجود
                                </h5>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th width="55">ردیف</th>
                                                <th>نام فایل</th>
                                                <th width="120">حجم</th>
                                                <th width="180">تاریخ ساخت</th>
                                                <th width="180">عملیات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($backups as $index => $backup)
                                                <tr wire:key="backup-{{ $backup['name'] }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $backup['name'] }}</td>
                                                    <td>{{ number_format($backup['size'] / 1024, 1) }} KB</td>
                                                    <td>{{ $backup['date'] }}</td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                            wire:click="downloadBackup('{{ $backup['name'] }}')">
                                                            <i class="bi bi-download"></i> دانلود
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            wire:click="deleteBackup('{{ $backup['name'] }}')"
                                                            wire:confirm="این نسخه‌ی پشتیبان حذف شود؟">
                                                            <i class="bi bi-trash"></i> حذف
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">
                                                        هنوز نسخه پشتیبانی تهیه نشده است.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-info d-flex align-items-center mt-4">
                                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                    <div>
                                        <strong>آخرین نسخه پشتیبان</strong>
                                        <br>
                                        {{ $lastBackup ?? 'هنوز نسخه پشتیبانی تهیه نشده است.' }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                </div>

            </form>

        </div>

    </div>
</div>
