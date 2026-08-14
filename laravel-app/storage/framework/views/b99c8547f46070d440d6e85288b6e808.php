<?php $__env->startSection('title', 'مدیریت محصولات'); ?>
<?php $__env->startSection('content'); ?>

    <div class="row row-cards mb-4 bg-dark">

        <!-- Success Alert Section -->
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        <?php endif; ?>

        <!-- Error Alert Section -->
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
            </div>
        <?php endif; ?>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">
                            تعداد کالاها
                        </div>
                    </div>
                    <div class="h1 mb-0">
                        <?php echo e($totalProducts); ?>

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
                        <?php echo e($activeProducts); ?>

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
                        <?php echo e($inactiveProducts); ?>

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
                        <?php echo e($lowStockProducts); ?>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" action="<?php echo e(route('products.index')); ?>">
                <div class="row g-2">

                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="جستجو نام یا بارکد..."
                            value="<?php echo e(request('search')); ?>">
                    </div>

                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">
                                همه دسته بندی ها
                            </option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php if(request('category_id') == $category->id): echo 'selected'; endif; ?>>
                                    <?php echo e($category->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" title="جستجو کنید">
                            <i class="bi bi-search"></i>
                            جستجو
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-outline-secondary w-100"
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
                        <th>بارکد</th>
                        <th>نام کالا</th>
                        <th>دسته بندی</th>
                        <th>قیمت فروش</th>
                        <th>موجودی</th>
                        <th width="250">عملیات</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($product->barcode); ?></td>
                            <td><?php echo e($product->name); ?></td>
                            <td><?php echo e($product->category?->name); ?></td>
                            <td><?php echo e(number_format($product->sell_price)); ?></td>
                            <td><?php echo e($product->stock); ?></td>
                            <td>
                                <!-- دکمه ویرایش کالا -->
                                <button type="button" class="btn btn-sm btn-warning edit-product-btn"
                                    data-bs-toggle="modal" data-bs-target="#editProductModal" data-id="<?php echo e($product->id); ?>"
                                    data-barcode="<?php echo e($product->barcode); ?>" data-name="<?php echo e($product->name); ?>"
                                    data-category="<?php echo e($product->category_id); ?>" data-buy-price="<?php echo e($product->buy_price); ?>"
                                    data-sell-price="<?php echo e($product->sell_price); ?>" data-unit="<?php echo e($product->unit); ?>"
                                    data-active="<?php echo e($product->is_active); ?>" title="ویرایش کالا">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- دکمه چاپ لیبل محصول -->
                                <button type="button" class="btn btn-sm btn-info print-label-btn"
                                    data-id="<?php echo e($product->id); ?>" title="چاپ لیبل">
                                    <i class="bi bi-printer"></i>
                                </button>

                                <!-- دکمه مشاهده لیست ورود و خروج این کالا به انبار -->
                                <a href="<?php echo e(route('products.stock', $product)); ?>" class="btn btn-sm btn-light"
                                    title="مشاهده سوابق ورود و خروج این کالا به انبار">
                                    <i class="bi bi-boxes"></i>
                                </a>

                                <!-- -->
                                <form action="<?php echo e(route('products.destroy', $product)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>

                                    <!-- دکمه ورود کالا به انبار-->
                                    <a href="<?php echo e(route('products.stock.create', $product)); ?>"
                                        class="btn btn-sm btn-outline-success" title="ورود این کالا به انبار">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>

                                    <!-- دکمه خروج کالا از انبار-->
                                    <a href="<?php echo e(route('products.sale.create', $product)); ?>"
                                        class="btn btn-sm btn-outline-danger" title="خروج این کالا از انبار">
                                        <i class="bi bi-dash-lg"></i>
                                    </a>
                                </form>
                                <!-- دکمه حذف کالا-->
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('حذف شود؟')"
                                        title="حذف این کالا">
                                        حذف
                                    </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                هیچ کالایی ثبت نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>

            <div class="mt-3"><?php echo e($products->links()); ?></div>

        </div>
    </div>

    <?php echo $__env->make('products.modals.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('products.modals.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('products.modals.label', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($errors->any() && old('_form') === 'create'): ?>
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
    <?php endif; ?>

    <?php if($errors->any() && old('_form') === 'edit' && old('edit_product_id')): ?>
        <script>
            // اسکریپت مربوط به نمایش خطا در مودال ویرایش محصول
            document.addEventListener('DOMContentLoaded', function() {
                const productId = "<?php echo e(old('edit_product_id')); ?>";
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
    <?php endif; ?>

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
                    fetch("<?php echo e(route('products.generate.barcode')); ?>")
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('barcode').value = data.barcode;
                            }
                        });
                });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/products/index.blade.php ENDPATH**/ ?>