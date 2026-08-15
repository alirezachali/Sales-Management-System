@extends('layouts.app')
@section('title', 'مدیریت محصولات')
@section('content')

    <!-- Success Alert Section -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    <!-- Error Alert Section -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    <div class="row row-cards mb-4">

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">
                            تعداد کالاها
                        </div>
                    </div>
                    <div class="h1 mb-0">
                        {{ $totalProducts }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">
                        کالاهای فعال
                    </div>
                    <div class="h1 mb-0 text-success">
                        {{ $activeProducts }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">
                        کالاهای غیرفعال
                    </div>
                    <div class="h1 mb-0 text-danger">
                        {{ $inactiveProducts }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">
                        موجودی کم
                    </div>
                    <div class="h1 mb-0 text-warning">
                        {{ $lowStockProducts }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('products.index') }}">
                <div class="row g-2">

                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="جستجو نام یا بارکد..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">
                                همه دسته بندی ها
                            </option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" title="جستجو کنید">
                            <i class="bi bi-search"></i>
                            جستجو
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100"
                            title="پاک کردن فیلترهای جستجو">
                            پاک کردن
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                مدیریت کالاها
            </h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal"
                title="افزودن محصول جدید به سیستم">>
                افزودن کالا جدید
            </button>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th width="130">بارکد</th>
                        <th>نام کالا</th>
                        <th>دسته بندی</th>
                        <th>قیمت فروش</th>
                        <th width="120">موجودی</th>
                        <th width="233">عملیات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <!-- ردیف -->
                            <td>{{ $loop->iteration }}</td>
                            <!-- بارکد -->
                            <td>{{ $product->barcode }}</td>
                            <!-- نام کالا -->
                            <td>{{ $product->name }}</td>
                            <!-- دسته بندی -->
                            <td>{{ $product->category?->name }}</td>
                            <!-- قیمت فروش-->
                            <td>
                                {{ number_format($product->sell_price) }}
                                <span>
                                    {{ setting('currency', '') }}
                                </span>
                            </td>
                            <!-- موجودی -->
                            <td>
                                {{ $product->formatted_stock }}
                                <span class="badge bg-secondary">
                                    {{ $product->unit }}
                                </span>
                            </td>
                            <!-- عملیات -->
                            <td>
                                <!-- دکمه ویرایش کالا -->
                                <button type="button" class="btn btn-sm btn-warning edit-product-btn"
                                    data-bs-toggle="modal" data-bs-target="#editProductModal" data-id="{{ $product->id }}"
                                    data-barcode="{{ $product->barcode }}" data-name="{{ $product->name }}"
                                    data-category="{{ $product->category_id }}" data-buy-price="{{ $product->buy_price }}"
                                    data-sell-price="{{ $product->sell_price }}" data-unit="{{ $product->unit }}"
                                    data-active="{{ $product->is_active }}" title="ویرایش کالا">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- دکمه چاپ لیبل محصول -->
                                <button type="button" class="btn btn-sm btn-info print-label-btn"
                                    data-id="{{ $product->id }}" title="چاپ لیبل">
                                    <i class="bi bi-printer"></i>
                                </button>

                                <!-- دکمه مشاهده لیست ورود و خروج این کالا به انبار -->
                                <a href="{{ route('products.stock', $product) }}" class="btn btn-sm btn-light"
                                    title="مشاهده سوابق ورود و خروج این کالا به انبار">
                                    <i class="bi bi-boxes"></i>
                                </a>

                                <!-- -->
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')

                                    <!-- دکمه ورود کالا به انبار-->
                                    <a href="{{ route('products.stock.create', $product) }}"
                                        class="btn btn-sm btn-outline-success" title="ورود این کالا به انبار">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>

                                    <!-- دکمه خروج کالا از انبار-->
                                    <a href="{{ route('products.sale.create', $product) }}"
                                        class="btn btn-sm btn-outline-danger" title="خروج این کالا از انبار">
                                        <i class="bi bi-dash-lg"></i>
                                    </a>
                                </form>
                                <!-- دکمه حذف کالا-->
                                <button class="btn btn-danger btn-sm" onclick="return confirm('حذف شود؟')"
                                    title="حذف این کالا">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                هیچ کالایی ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="mt-3">{{ $products->links() }}</div>

        </div>
    </div>

    @include('products.modals.create')
    @include('products.modals.edit')
    @include('products.modals.label')

    @if ($errors->any() && old('_form') === 'create')
        <script>
            // اسکریپت مربوط به نمایش خطا در مودال افزودن محصول
            document.addEventListener('DOMContentLoaded', function() {
                const modalElement = document.getElementById('createProductModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            });
        </script>
    @endif

    @if ($errors->any() && old('_form') === 'edit' && old('edit_product_id'))
        <script>
            // اسکریپت مربوط به نمایش خطا در مودال ویرایش محصول
            document.addEventListener('DOMContentLoaded', function() {
                const productId = "{{ old('edit_product_id') }}";
                const modalElement = document.getElementById('editProductModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                    const form = document.getElementById('editProductForm');
                    if (form) {
                        form.action = `/products/${productId}`;
                        document.getElementById('edit_product_id').value = productId;
                    }
                }
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentLabelTemplate = '';

            // باز کردن مودال چاپ لیبل
            document.querySelectorAll('.print-label-btn').forEach(button => {

                button.addEventListener('click', function() {

                    let productId = this.dataset.id;

                    fetch(`/products/${productId}/label`)
                        .then(response => {

                            if (!response.ok) {
                                throw new Error('خطا در دریافت اطلاعات لیبل');
                            }

                            return response.json();

                        })
                        .then(data => {

                            // نام کالا
                            let labelName = '';

                            if (data.label_show_name) {
                                labelName = `
                        <div class="label-name">
                            ${data.name}
                        </div>
                    `;
                            }


                            // قیمت
                            let labelPrice = '';

                            if (data.label_show_price) {
                                labelPrice = `
                        <div class="label-price">
                            ${Number(data.price).toLocaleString()} تومان
                        </div>
                    `;
                            }


                            // بارکد میله‌ای
                            let labelBarcode = '';

                            if (data.label_show_barcode) {
                                labelBarcode = `
                        <div class="label-barcode">
                            ${data.barcode_svg}
                        </div>
                    `;
                            }


                            // شماره بارکد
                            let labelCode = '';

                            if (data.label_show_code) {
                                labelCode = `
                        <div class="label-code">
                            ${data.barcode}
                        </div>
                    `;
                            }


                            // ساخت Template اصلی
                            currentLabelTemplate = `

                    <div class="label-print-area"
                         style="
                             width: ${data.label_width}mm;
                             height: ${data.label_height}mm;
                         ">

                        ${labelName}

                        ${labelPrice}

                        ${labelBarcode}

                        ${labelCode}

                    </div>

                `;


                            document.getElementById('label-container').innerHTML =
                                currentLabelTemplate;


                            let modal = new bootstrap.Modal(
                                document.getElementById('labelModal')
                            );

                            modal.show();

                        })
                        .catch(error => {

                            console.error('Label Error:', error);

                        });

                });

            });

            // چاپ لیبل
            document.getElementById('print-label-btn').addEventListener('click', function() {
                let quantity = parseInt(document.getElementById('label_quantity').value);

                if (!quantity || quantity < 1) {
                    quantity = 1;
                }

                let container = document.getElementById('label-container');
                let output = '';

                for (let i = 0; i < quantity; i++) {
                    output += currentLabelTemplate;
                }

                container.innerHTML = output;
                window.print();
            });

            // ریست کردن Preview هنگام بسته شدن Modal
            document.getElementById('labelModal')
                .addEventListener('hidden.bs.modal', function() {
                    document.getElementById('label-container').innerHTML = '';
                    currentLabelTemplate = '';
                });

            // اسکریپت فرستادن اطلاعات محصول همراه با دکمه ویرایش به مودال ویرایش محصول
            const editButtons = document.querySelectorAll('.edit-product-btn');
            editButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    document.getElementById('edit_product_id').value = id;
                    document.getElementById('edit_barcode').value = this.dataset.barcode || '';
                    document.getElementById('edit_name').value = this.dataset.name || '';
                    document.getElementById('edit_category_id').value = this.dataset.category || '';
                    document.getElementById('edit_buy_price').value = this.dataset.buyPrice || '';
                    document.getElementById('edit_sell_price').value = this.dataset.sellPrice || '';
                    document.getElementById('edit_unit').value = this.dataset.unit || 'عدد';
                    document.getElementById('edit_is_active').value = this.dataset.active || '1';
                    const form = document.getElementById('editProductForm');
                    form.action = `/products/${id}`;
                });
            });

            // اسکریبت مربوط به دکمه تولید بارکد داخلی در مودال و فرم افزودن محصول جدید
            document.getElementById('generateBarcodeBtn')
                ?.addEventListener('click', function() {
                    fetch("{{ route('products.generate.barcode') }}")
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('barcode').value = data.barcode;
                            }
                        });
                });

        });
    </script>
@endsection
