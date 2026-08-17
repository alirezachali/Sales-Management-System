
<div class="modal fade" id="editSupplierModal<?php echo e($supplier->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo e(route('suppliers.update', $supplier)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">ویرایش تامین‌کننده: <?php echo e($supplier->name); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <?php echo $__env->make('suppliers._form', ['supplier' => $supplier], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>




                    <?php
                        $s = $supplier ?? null;
                    ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                value="<?php echo e(old('name', $s->name ?? '')); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">نام شرکت</label>
                            <input type="text" name="company_name" class="form-control"
                                value="<?php echo e(old('company_name', $s->company_name ?? '')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">نام شخص رابط</label>
                            <input type="text" name="contact_person" class="form-control"
                                value="<?php echo e(old('contact_person', $s->contact_person ?? '')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">نوع <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="individual" <?php if(old('type', $s->type ?? 'individual') === 'individual'): echo 'selected'; endif; ?>>حقیقی</option>
                                <option value="company" <?php if(old('type', $s->type ?? '') === 'company'): echo 'selected'; endif; ?>>حقوقی</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control"
                                value="<?php echo e(old('mobile', $s->mobile ?? '')); ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">تلفن ثابت</label>
                            <input type="text" name="phone" class="form-control"
                                value="<?php echo e(old('phone', $s->phone ?? '')); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" class="form-control"
                                value="<?php echo e(old('email', $s->email ?? '')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">استان</label>
                            <input type="text" name="province" class="form-control"
                                value="<?php echo e(old('province', $s->province ?? '')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">شهر</label>
                            <input type="text" name="city" class="form-control"
                                value="<?php echo e(old('city', $s->city ?? '')); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">آدرس</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $s->address ?? '')); ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">کد پستی</label>
                            <input type="text" name="postal_code" class="form-control"
                                value="<?php echo e(old('postal_code', $s->postal_code ?? '')); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">کد ملی</label>
                            <input type="text" name="national_id" class="form-control"
                                value="<?php echo e(old('national_id', $s->national_id ?? '')); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">کد اقتصادی</label>
                            <input type="text" name="economic_code" class="form-control"
                                value="<?php echo e(old('economic_code', $s->economic_code ?? '')); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">سقف اعتباری (ریال)</label>
                            <input type="number" step="0.01" name="credit_limit" class="form-control"
                                value="<?php echo e(old('credit_limit', $s->credit_limit ?? 0)); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">مونده اولیه حساب (ریال)</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control"
                                value="<?php echo e(old('opening_balance', $s->opening_balance ?? 0)); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">شماره شبا</label>
                            <input type="text" name="iban" class="form-control"
                                value="<?php echo e(old('iban', $s->iban ?? '')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">شرایط پرداخت</label>
                            <input type="text" name="payment_terms" class="form-control"
                                value="<?php echo e(old('payment_terms', $s->payment_terms ?? '')); ?>"
                                placeholder="مثلاً: اعتباری ۳۰ روزه">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">امتیاز (۱ تا ۵)</label>
                            <input type="number" min="1" max="5" name="rating" class="form-control"
                                value="<?php echo e(old('rating', $s->rating ?? '')); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">یادداشت</label>
                            <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $s->notes ?? '')); ?></textarea>
                        </div>

                        <div class="col-12 form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active_<?php echo e($s->id ?? 'new'); ?>"
                                name="is_active" value="1" <?php if(old('is_active', $s->is_active ?? true)): echo 'checked'; endif; ?>>
                            <label class="form-check-label" for="is_active_<?php echo e($s->id ?? 'new'); ?>">فعال</label>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>

                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

                </div>

            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/suppliers/modals/edit.blade.php ENDPATH**/ ?>