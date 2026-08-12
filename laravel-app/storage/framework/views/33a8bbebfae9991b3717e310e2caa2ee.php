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
                    <option value="fa" <?php if(($settings['system_language'] ?? 'fa') == 'fa'): echo 'selected'; endif; ?>>
                        فارسی
                    </option>
                    <option value="en" <?php if(($settings['system_language'] ?? '') == 'en'): echo 'selected'; endif; ?>>
                        English
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    منطقه زمانی
                </label>
                <select class="form-select" name="timezone">
                    <option value="Asia/Tehran" <?php if(($settings['timezone'] ?? 'Asia/Tehran') == 'Asia/Tehran'): echo 'selected'; endif; ?>>
                        Asia / Tehran
                    </option>
                    <option value="UTC" <?php if(($settings['timezone'] ?? '') == 'UTC'): echo 'selected'; endif; ?>>
                        UTC
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    قالب تاریخ
                </label>
                <select class="form-select" name="date_format">
                    <option value="Y/m/d" <?php if(($settings['date_format'] ?? 'Y/m/d') == 'Y/m/d'): echo 'selected'; endif; ?>>
                        1405/05/01
                    </option>
                    <option value="d/m/Y" <?php if(($settings['date_format'] ?? '') == 'd/m/Y'): echo 'selected'; endif; ?>>
                        01/05/1405
                    </option>
                    <option value="Y-m-d" <?php if(($settings['date_format'] ?? '') == 'Y-m-d'): echo 'selected'; endif; ?>>
                        1405-05-01
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
                        <?php if(($settings['system_log'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        ثبت گزارش فعالیت کاربران
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="remember_login" value="0">
                    <input class="form-check-input" type="checkbox" name="remember_login" value="1"
                        <?php if(($settings['remember_login'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        فعال بودن ورود خودکار
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input class="form-check-input" type="checkbox" name="maintenance_mode" value="1"
                        <?php if(($settings['maintenance_mode'] ?? 0) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        حالت تعمیر و نگهداری
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="developer_mode" value="0">
                    <input class="form-check-input" type="checkbox" name="developer_mode" value="1"
                        <?php if(($settings['developer_mode'] ?? 0) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        حالت توسعه دهنده
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="enable_cache" value="0">
                    <input class="form-check-input" type="checkbox" name="enable_cache" value="1"
                        <?php if(($settings['enable_cache'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        فعال بودن کش سیستم
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="check_update" value="0">
                    <input class="form-check-input" type="checkbox" name="check_update" value="1"
                        <?php if(($settings['check_update'] ?? 1) == 1): echo 'checked'; endif; ?>>
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
                <input type="number" class="form-control" name="session_timeout" value="<?php echo e($settings['session_timeout'] ?? 120); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">
                    تعداد رکورد در هر صفحه
                </label>
                <input type="number" class="form-control" name="pagination_limit" value="<?php echo e($settings['pagination_limit'] ?? 15); ?>">
            </div>

        </div>
    </div>
</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/settings/tabs/system.blade.php ENDPATH**/ ?>