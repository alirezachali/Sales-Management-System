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
                <label class="form-label">
                    زبان برنامه
                </label>
                <select class="form-select" name="system_language">
                    <option value="fa" @selected(($settings['system_language'] ?? 'fa') == 'fa')>
                        فارسی
                    </option>
                    <option value="en" @selected(($settings['system_language'] ?? '') == 'en')>
                        English
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    منطقه زمانی
                </label>
                <select class="form-select" name="timezone">
                    <option value="Asia/Tehran" @selected(($settings['timezone'] ?? 'Asia/Tehran') == 'Asia/Tehran')>
                        Asia / Tehran
                    </option>
                    <option value="UTC" @selected(($settings['timezone'] ?? '') == 'UTC')>
                        UTC
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    قالب تاریخ
                </label>
                <select class="form-select" name="date_format">
                    <option value="Y/m/d" @selected(($settings['date_format'] ?? 'Y/m/d') == 'Y/m/d')>
                        1405/05/01
                    </option>
                    <option value="Y-m-d" @selected(($settings['date_format'] ?? '') == 'Y-m-d')>
                        1405-05-01
                    </option>
                    <option value="j F Y" @selected(($settings['date_format'] ?? '') == 'j F Y')>
                        1 مرداد 1405
                    </option>
                    <option value="l j F Y" @selected(($settings['date_format'] ?? '') == 'l j F Y')>
                        شنبه 1 مرداد 1405
                    </option>
                </select>
            </div>

        </div>

        <hr class="my-4">

        <div class="row">

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="system_log" value="0">
                    <input class="form-check-input" type="checkbox" name="system_log" value="1"
                        @checked(($settings['system_log'] ?? 1) == 1)>
                    <label class="form-check-label">
                        ثبت گزارش فعالیت کاربران
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="remember_login" value="0">
                    <input class="form-check-input" type="checkbox" name="remember_login" value="1"
                        @checked(($settings['remember_login'] ?? 1) == 1)>
                    <label class="form-check-label">
                        فعال بودن ورود خودکار
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1"
                        @checked(($settings['maintenance_mode'] ?? 0) == 1)>
                    <label class="form-check-label">
                        حالت تعمیر و نگهداری
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="developer_mode" value="0">
                    <input class="form-check-input" type="checkbox" name="developer_mode" value="1"
                        @checked(($settings['developer_mode'] ?? 0) == 1)>
                    <label class="form-check-label">
                        حالت توسعه دهنده
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="enable_cache" value="0">
                    <input class="form-check-input" type="checkbox" name="enable_cache" value="1"
                        @checked(($settings['enable_cache'] ?? 1) == 1)>
                    <label class="form-check-label">
                        فعال بودن کش سیستم
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="check_update" value="0">
                    <input class="form-check-input" type="checkbox" name="check_update" value="1"
                        @checked(($settings['check_update'] ?? 1) == 1)>
                    <label class="form-check-label">
                        بررسی بروزرسانی هنگام اجرا
                    </label>
                </div>
            </div>

        </div>

        <hr class="my-4">

        <div class="row">

            <div class="col-md-6">
                <label class="form-label">
                    مدت زمان انقضای نشست (دقیقه)
                </label>
                <input type="number" class="form-control" name="session_timeout" value="{{ $settings['session_timeout'] ?? 120 }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">
                    تعداد رکورد در هر صفحه
                </label>
                <input type="number" class="form-control" name="pagination_limit" value="{{ $settings['pagination_limit'] ?? 15 }}">
            </div>

        </div>
    </div>
</div>
