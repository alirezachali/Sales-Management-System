<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form method="POST" id="editProductForm" action="#">

            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <input type="hidden" name="_form" value="edit">
            <input type="hidden" name="edit_product_id" id="edit_product_id">

            <div class="modal-content glass-card">

                <div class="modal-header">

                    <h5 class="modal-title" id="editProductModalLabel">

                        <i class="bi bi-pencil-square"></i>

                        ویرایش کالا

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        
                        <div class="col-md-6">

                            <label class="form-label">
                                بارکد
                            </label>

                            <input type="text" name="barcode" id="edit_barcode"
                                class="form-control <?php $__errorArgs = ['barcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('barcode')); ?>">

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


                        
                        <div class="col-md-6">

                            <label class="form-label">
                                نام کالا
                            </label>

                            <input type="text" name="name" id="edit_name"
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


                        
                        <div class="col-md-4">

                            <label class="form-label">
                                دسته بندی
                            </label>

                            <select name="category_id" id="edit_category_id"
                                class="form-select <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                                <option value="">
                                    انتخاب کنید
                                </option>

                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php if(old('category_id') == $category->id): echo 'selected'; endif; ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>

                            <?php $__errorArgs = ['category_id'];
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


                        
                        <div class="col-md-4">

                            <label class="form-label">
                                قیمت خرید
                            </label>

                            <input type="number" name="buy_price" id="edit_buy_price"
                                class="form-control <?php $__errorArgs = ['buy_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('buy_price')); ?>">

                            <?php $__errorArgs = ['buy_price'];
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


                        
                        <div class="col-md-4">

                            <label class="form-label">
                                قیمت فروش
                            </label>

                            <input type="number" name="sell_price" id="edit_sell_price"
                                class="form-control <?php $__errorArgs = ['sell_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('sell_price')); ?>">

                            <?php $__errorArgs = ['sell_price'];
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


                        
                        <div class="col-md-4">

                            <label class="form-label">
                                واحد
                            </label>

                            <select name="unit" id="edit_unit"
                                class="form-select <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                                <option value="عدد" <?php if(old('unit') === 'عدد'): echo 'selected'; endif; ?>>
                                    عدد
                                </option>

                                <option value="کیلوگرم" <?php if(old('unit') === 'کیلوگرم'): echo 'selected'; endif; ?>>
                                    کیلوگرم
                                </option>

                                <option value="لیتر" <?php if(old('unit') === 'لیتر'): echo 'selected'; endif; ?>>
                                    لیتر
                                </option>

                            </select>

                            <?php $__errorArgs = ['unit'];
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


                        
                        <div class="col-md-4">

                            <label class="form-label">
                                وضعیت
                            </label>

                            <select name="is_active" id="edit_is_active"
                                class="form-select <?php $__errorArgs = ['is_active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                                <option value="1" <?php if(old('is_active') == '1'): echo 'selected'; endif; ?>>
                                    فعال
                                </option>

                                <option value="0" <?php if(old('is_active') == '0'): echo 'selected'; endif; ?>>
                                    غیرفعال
                                </option>

                            </select>

                            <?php $__errorArgs = ['is_active'];
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

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        انصراف

                    </button>

                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-save"></i>

                        ذخیره تغییرات

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/products/modals/edit.blade.php ENDPATH**/ ?>