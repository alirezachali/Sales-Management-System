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

</div>



ختخحت
