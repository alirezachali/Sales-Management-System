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
                <div class="card border">
                    <div class="card-body text-center">
                        <i class="bi bi-cloud-arrow-down display-3 text-success"></i>
                        <h5 class="mt-3">
                            تهیه نسخه پشتیبان
                        </h5>
                        <p class="text-muted">
                            از اطلاعات نرم افزار نسخه پشتیبان تهیه کنید.
                        </p>
                        <button type="button" class="btn btn-success">
                            <i class="bi bi-download"></i>
                            ایجاد نسخه پشتیبان
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border">

                    <div class="card-body text-center">
                        <i class="bi bi-cloud-arrow-up display-3 text-primary"></i>
                        <h5 class="mt-3">
                            بازیابی اطلاعات
                        </h5>
                        <p class="text-muted">
                            فایل پشتیبان را انتخاب کنید.
                        </p>
                        <input type="file" class="form-control mb-3" name="backup_file">
                        <button type="button" class="btn btn-primary">
                            <i class="bi bi-upload"></i>
                            بازیابی اطلاعات
                        </button>
                    </div>

                </div>
            </div>

        </div>

        <hr class="my-5">

        <div class="row">

            <div class="col-md-6">
                <label class="form-label">
                    مسیر ذخیره نسخه های پشتیبان
                </label>
                <input type="text" class="form-control" name="backup_path"
                    value="<?php echo e($settings['backup_path'] ?? storage_path('app/backups')); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">
                    تعداد نسخه قابل نگهداری
                </label>
                <input type="number" class="form-control" name="backup_keep"
                    value="<?php echo e($settings['backup_keep'] ?? 20); ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">
                    فرمت فایل
                </label>
                <select class="form-select" name="backup_format">
                    <option value="zip" <?php if(($settings['backup_format'] ?? 'zip') == 'zip'): echo 'selected'; endif; ?>>
                        ZIP
                    </option>
                    <option value="sql" <?php if(($settings['backup_format'] ?? '') == 'sql'): echo 'selected'; endif; ?>>
                        SQL
                    </option>
                </select>
            </div>

        </div>

        <hr class="my-4">

        <div class="row">

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="auto_backup" value="0">
                    <input class="form-check-input" type="checkbox" name="auto_backup" value="1"
                        <?php if(($settings['auto_backup'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        تهیه نسخه پشتیبان خودکار
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="backup_before_restore" value="0">
                    <input class="form-check-input" type="checkbox" name="backup_before_restore" value="1"
                        <?php if(($settings['backup_before_restore'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        قبل از بازیابی، نسخه پشتیبان تهیه شود
                    </label>
                </div>
            </div>

        </div>

        <hr class="my-5">

        <div class="alert alert-info d-flex align-items-center">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <strong>
                    آخرین نسخه پشتیبان
                </strong>
                <br>
                <?php echo e($settings['last_backup'] ?? 'هنوز نسخه پشتیبانی تهیه نشده است.'); ?>

            </div>
        </div>

    </div>
</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/settings/tabs/backup.blade.php ENDPATH**/ ?>