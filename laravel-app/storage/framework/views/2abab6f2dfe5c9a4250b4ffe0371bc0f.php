<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form id="editCustomerForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="modal-content glass-card">

                <div class="modal-header">
                    <h5 class="modal-title">
                        ویرایش مشتری
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>
                                نام
                            </label>
                            <input id="edit_first_name" name="first_name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                نام خانوادگی
                            </label>
                            <input id="edit_last_name" name="last_name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                موبایل
                            </label>
                            <input id="edit_mobile" name="mobile" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                تلفن
                            </label>
                            <input id="edit_phone" name="phone" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                نقش
                            </label>
                            <select id="edit_customer_role_id" name="customer_role_id" class="form-select">
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>">
                                        <?php echo e($role->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>
                                وضعیت
                            </label>
                            <select id="edit_is_active" name="is_active" class="form-select">
                                <option value="1">
                                    فعال
                                </option>
                                <option value="0">
                                    غیرفعال
                                </option>
                            </select>

                        </div>
                        <div class="col-12">
                            <label>
                                توضیحات
                            </label>
                            <textarea id="edit_notes" name="notes" rows="3" class="form-control"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        انصراف
                    </button>

                    <button class="btn btn-warning">
                        ذخیره تغییرات
                    </button>

                </div>
            </div>

        </form>

    </div>
</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/customers/modals/edit.blade.php ENDPATH**/ ?>