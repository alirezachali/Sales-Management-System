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
                <label class="form-label">
                    پیشوند شماره فاکتور
                </label>
                <input type="text" class="form-control" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV' }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    شماره شروع فاکتور
                </label>
                <input type="number" class="form-control" name="invoice_start" value="{{ $settings['invoice_start'] ?? 1 }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    تعداد ارقام شماره فاکتور
                </label>
                <select class="form-select" name="invoice_digits">
                    @for ($i = 4; $i <= 10; $i++)
                        <option value="{{ $i }}" @selected(($settings['invoice_digits'] ?? 6) == $i)>
                            {{ $i }} رقم
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    واحد پول
                </label>
                <input type="text" class="form-control" name="currency" value="{{ $settings['currency'] ?? 'تومان' }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    تعداد اعشار قیمت
                </label>
                <select class="form-select" name="price_decimal">
                    @for ($i = 0; $i <= 4; $i++)
                        <option value="{{ $i }}" @selected(($settings['price_decimal'] ?? 0) == $i)>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    نرخ مالیات (%)
                </label>
                <input type="number" class="form-control" name="tax_percent" value="{{ $settings['tax_percent'] ?? 0 }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    تخفیف پیش فرض (%)
                </label>
                <input type="number" class="form-control" name="default_discount" value="{{ $settings['default_discount'] ?? 0 }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    هشدار اتمام موجودی
                </label>
                <input type="number" class="form-control" name="stock_alert" value="{{ $settings['stock_alert'] ?? 5 }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    حداکثر اقلام هر فاکتور
                </label>
                <input type="number" class="form-control" name="max_invoice_items" value="{{ $settings['max_invoice_items'] ?? 100 }}">
            </div>
        </div>

        <hr class="my-4">

        <div class="row">

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="allow_negative_stock" value="0">
                    <input class="form-check-input" type="checkbox" name="allow_negative_stock" value="1"
                        @checked(($settings['allow_negative_stock'] ?? 0) == 1)>
                    <label class="form-check-label">
                        اجازه فروش با موجودی منفی
                    </label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="auto_print_invoice" value="0">
                    <input class="form-check-input" type="checkbox" name="auto_print_invoice" value="1"
                        @checked(($settings['auto_print_invoice'] ?? 0) == 1)>
                    <label class="form-check-label">
                        چاپ خودکار فاکتور بعد از ثبت
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="barcode_sound" value="0">
                    <input class="form-check-input" type="checkbox" name="barcode_sound" value="1"
                        @checked(($settings['barcode_sound'] ?? 1) == 1)>
                    <label class="form-check-label">
                        پخش صدای اسکن بارکد
                    </label>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="confirm_delete_invoice" value="0">
                    <input class="form-check-input" type="checkbox" name="confirm_delete_invoice" value="1"
                        @checked(($settings['confirm_delete_invoice'] ?? 1) == 1)>
                    <label class="form-check-label">
                        تایید قبل از حذف فاکتور
                    </label>
                </div>
            </div>

        </div>
    </div>
</div>
