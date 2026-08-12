


<div class="modal fade" id="createCustomerModal" tabindex="-1">

    <!-- Modal Dialog -->
    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form action="<?php echo e(route('customers.store')); ?>" method="POST">

            <?php echo csrf_field(); ?>

            <div class="modal-content glass-card">

                <!-- Modal Header -->
                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-person-plus-fill"></i>
                        افزودن مشتری
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>

                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                نام
                            </label>

                            <input type="text"
                                   name="first_name"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                نام خانوادگی
                            </label>

                            <input type="text"
                                   name="last_name"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                موبایل
                            </label>

                            <input type="text"
                                   name="mobile"
                                   class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                تلفن
                            </label>

                            <input type="text"
                                   name="phone"
                                   class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                نقش مشتری
                            </label>

                            <select class="form-select" name="customer_role_id">
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($role->id); ?>"
                                        <?php echo e($role->is_default ? 'selected' : ''); ?>>
                                        <?php echo e($role->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                وضعیت
                            </label>

                            <select class="form-select" name="is_active">
                                <option value="1">
                                    فعال
                                </option>
                                <option value="0">
                                    غیرفعال
                                </option>
                            </select>

                        </div>

                        <div class="col-12">

                            <label class="form-label">
                                توضیحات
                            </label>

                            <textarea class="form-control"
                                      rows="3"
                                      name="notes"></textarea>

                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            title="انصراف و بازگشت به صفحه قبلی">

                        انصراف
                    </button>

                    <button class="btn btn-primary" title="ذخیره مشتری در سیستم">
                        ذخیره
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/customers/modals/create.blade.php ENDPATH**/ ?>