<div class="modal fade" id="createProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form method="POST" action="<?php echo e(route('products.store')); ?>">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="_form" value="create">

            <div class="modal-content glass-card">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus-fill"></i>
                        افزودن کالا جدید
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                بارکد
                            </label>
                            <input type="text" name="barcode" id="barcode"
                                class="form-control <?php $__errorArgs = ['barcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('barcode')); ?>">
                            <!-- دکمه تولید بارکد داخلی -->
                            <button type="button" class="btn btn-outline-primary" id="generateBarcodeBtn">
                                <i class="bi bi-upc-scan"></i>
                                تولید بارکد
                            </button>
                            <?php $__errorArgs = ['barcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                نام کالا
                            </label>
                            <input type="text" name="name"
                                class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name')); ?>">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback">
                                    <?php echo e($message); ?>

                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                دسته بندی
                            </label>
                            <select name="category_id" class="form-select">
                                <option value="">
                                    انتخاب کنید
                                </option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php if(old('category_id') == $category->id): echo 'selected'; endif; ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                قیمت خرید
                            </label>
                            <input type="number" name="buy_price" class="form-control"
                                value="<?php echo e(old('buy_price', 0)); ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                قیمت فروش
                            </label>
                            <input type="number" name="sell_price" class="form-control"
                                value="<?php echo e(old('sell_price', 0)); ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                موجودی اولیه
                            </label>
                            <input type="number" step="0.001" name="stock" class="form-control"
                                value="<?php echo e(old('stock', 0)); ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                واحد
                            </label>
                            <select name="unit" class="form-select">
                                <option value="عدد">
                                    عدد
                                </option>
                                <option value="کیلوگرم">
                                    کیلوگرم
                                </option>
                                <option value="لیتر">
                                    لیتر
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">
                                وضعیت
                            </label>
                            <select name="is_active" class="form-select">
                                <option value="1">
                                    فعال
                                </option>
                                <option value="0">
                                    غیرفعال
                                </option>
                            </select>
                        </div>

                    </div>
                </div>
                <!-- End Modal Body -->

                <!-- Start Modal Footer -->
                <div class="modal-footer">
                    <button class="btn btn-primary" title="ذخیره کالا در سیستم">
                        <i class="bi bi-save"></i>
                        ذخیره کالا
                    </button>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary"
                        title="انصراف و بازگشت به صفحه قبلی">
                        بازگشت
                    </a>
                </div>

            </div>

        </form>

    </div>
</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/products/modals/create.blade.php ENDPATH**/ ?>