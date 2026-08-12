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
                <label class="form-label">
                    اندازه کاغذ
                </label>
                <select class="form-select" name="paper_size">
                    <option value="58" <?php if(($settings['paper_size'] ?? '80') == '58'): echo 'selected'; endif; ?>>
                        58 میلی‌متر
                    </option>
                    <option value="80" <?php if(($settings['paper_size'] ?? '80') == '80'): echo 'selected'; endif; ?>>
                        80 میلی‌متر
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    تعداد نسخه چاپ
                </label>
                <input type="number" class="form-control" name="print_copies" value="<?php echo e($settings['print_copies'] ?? 1); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    چاپ خودکار
                </label>
                <select class="form-select" name="auto_print">
                    <option value="1" <?php if(($settings['auto_print'] ?? 0) == 1): echo 'selected'; endif; ?>>
                        فعال
                    </option>
                    <option value="0" <?php if(($settings['auto_print'] ?? 0) == 0): echo 'selected'; endif; ?>>
                        غیرفعال
                    </option>
                </select>
            </div>

        </div>

        <hr class="my-4">

        <div class="row">

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="print_logo" value="0">
                    <input class="form-check-input" type="checkbox" name="print_logo" value="1"
                        <?php if(($settings['print_logo'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        چاپ لوگوی فروشگاه
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="print_address" value="0">
                    <input class="form-check-input" type="checkbox" name="print_address" value="1"
                        <?php if(($settings['print_address'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        چاپ آدرس فروشگاه
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="print_phone" value="0">
                    <input class="form-check-input" type="checkbox" name="print_phone" value="1"
                        <?php if(($settings['print_phone'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        چاپ شماره تلفن
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="print_barcode" value="0">
                    <input class="form-check-input" type="checkbox" name="print_barcode" value="1"
                        <?php if(($settings['print_barcode'] ?? 0) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        چاپ بارکد روی فاکتور
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="print_qrcode" value="0">
                    <input class="form-check-input" type="checkbox" name="print_qrcode" value="1"
                        <?php if(($settings['print_qrcode'] ?? 0) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        چاپ QR Code
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="print_datetime" value="0">
                    <input class="form-check-input" type="checkbox" name="print_datetime" value="1"
                        <?php if(($settings['print_datetime'] ?? 1) == 1): echo 'checked'; endif; ?>>
                    <label class="form-check-label">
                        چاپ تاریخ و ساعت
                    </label>
                </div>
            </div>

        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-12">
                <label class="form-label">
                    متن پایین فاکتور
                </label>
                <textarea class="form-control" rows="4" name="receipt_footer"><?php echo e($settings['receipt_footer'] ?? 'از خرید شما سپاسگزاریم'); ?></textarea>
            </div>
        </div>

    </div>

</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/settings/tabs/print.blade.php ENDPATH**/ ?>