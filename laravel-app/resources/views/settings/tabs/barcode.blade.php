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
                <label class="form-label">
                    پیشوند بارکد داخلی
                </label>
                <input type="text" class="form-control" name="barcode_prefix"
                    value="{{ $settings['barcode_prefix'] ?? '20' }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    طول بارکد
                </label>
                <input type="number" class="form-control" name="barcode_length"
                    value="{{ $settings['barcode_length'] ?? 12 }}">
            </div>

            <div class="col-md-4">
                <label class="form-label">
                    نوع بارکد
                </label>
                <select class="form-select" name="barcode_type">
                    <option value="CODE128">
                        Code128
                    </option>
                </select>
            </div>

        </div>
    </div>

    <hr class="my-4">

    <h5 class="mb-3">
        <i class="bi bi-layout-text-window"></i>
        تنظیمات ظاهر لیبل
    </h5>

    <div class="row g-3">

        <div class="col-md-3">

            <label class="form-label">
                عرض لیبل (میلی‌متر)
            </label>

            <input type="number" name="label_width" class="form-control" min="20"
                value="{{ $settings['label_width'] ?? 50 }}">

        </div>


        <div class="col-md-3">

            <label class="form-label">
                ارتفاع لیبل (میلی‌متر)
            </label>

            <input type="number" name="label_height" class="form-control" min="15"
                value="{{ $settings['label_height'] ?? 30 }}">

        </div>


        <div class="col-md-3">

            <label class="form-label">
                تعداد چاپ پیش‌فرض
            </label>

            <input type="number" name="label_default_quantity" class="form-control" min="1"
                value="{{ $settings['label_default_quantity'] ?? 1 }}">

        </div>

    </div>


    <div class="mt-4">

        <label class="form-label d-block">
            موارد قابل نمایش روی لیبل
        </label>


        <input type="hidden" name="label_show_name" value="0">

        <div class="form-check form-switch mb-2">

            <input type="checkbox" class="form-check-input" name="label_show_name" value="1"
                @checked(($settings['label_show_name'] ?? 1) == 1)>

            <label class="form-check-label">
                نمایش نام کالا
            </label>

        </div>


        <input type="hidden" name="label_show_price" value="0">

        <div class="form-check form-switch mb-2">

            <input type="checkbox" class="form-check-input" name="label_show_price" value="1"
                @checked(($settings['label_show_price'] ?? 1) == 1)>

            <label class="form-check-label">
                نمایش قیمت
            </label>

        </div>


        <input type="hidden" name="label_show_barcode" value="0">

        <div class="form-check form-switch mb-2">

            <input type="checkbox" class="form-check-input" name="label_show_barcode" value="1"
                @checked(($settings['label_show_barcode'] ?? 1) == 1)>

            <label class="form-check-label">
                نمایش بارکد میله‌ای
            </label>

        </div>


        <input type="hidden" name="label_show_code" value="0">

        <div class="form-check form-switch mb-2">

            <input type="checkbox" class="form-check-input" name="label_show_code" value="1"
                @checked(($settings['label_show_code'] ?? 1) == 1)>

            <label class="form-check-label">
                نمایش شماره بارکد
            </label>

        </div>


        <input type="hidden" name="label_show_unit" value="0">

        <div class="form-check form-switch">

            <input type="checkbox" class="form-check-input" name="label_show_unit" value="1"
                @checked(($settings['label_show_unit'] ?? 0) == 1)>

            <label class="form-check-label">
                نمایش واحد کالا
            </label>

        </div>

    </div>

</div>
