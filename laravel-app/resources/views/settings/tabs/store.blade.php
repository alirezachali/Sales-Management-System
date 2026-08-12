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
                        <label class="form-label">
                            نام فروشگاه
                        </label>
                        <input type="text" class="form-control" name="store_name" value="{{ $settings['store_name'] ?? '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            نام مدیر
                        </label>
                        <input type="text" class="form-control" name="manager_name" value="{{ $settings['manager_name'] ?? '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            تلفن
                        </label>
                        <input type="text" class="form-control" name="phone" value="{{ $settings['phone'] ?? '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            موبایل
                        </label>
                        <input type="text" class="form-control" name="mobile" value="{{ $settings['mobile'] ?? '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            ایمیل
                        </label>
                        <input type="email" class="form-control" name="email" value="{{ $settings['email'] ?? '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            وب سایت
                        </label>
                        <input type="text" class="form-control" name="website" value="{{ $settings['website'] ?? '' }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">
                            آدرس
                        </label>
                        <textarea class="form-control" rows="3" name="address">{{ $settings['address'] ?? '' }}</textarea>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header">
                <strong>
                    لوگوی فروشگاه
                </strong>
            </div>
            <div class="card-body text-center">
                <img id="logoPreview" src="{{ storeLogo() }}" class="img-fluid rounded border mb-3" style="max-height:180px">
                <input class="form-control" type="file" name="store_logo" id="store_logo" accept=".png,.jpg,.jpeg,.webp">
                <small class="text-muted d-block mt-2">
                    PNG / JPG / WEBP
                </small>
            </div>
        </div>

        <div class="card shadow-sm border-0">

            <div class="card-header">
                <strong>
                    Favicon
                </strong>
            </div>

            <div class="card-body text-center">
                <img id="faviconPreview" src="{{ storeFavicon() }}" class="rounded border p-2 mb-3" width="64" height="64">
                <input class="form-control" type="file" name="store_favicon" id="store_favicon" accept=".png,.ico">
                <small class="text-muted d-block mt-2">
                    PNG / ICO
                </small>
            </div>

        </div>
    </div>
</div>
