<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">

        <form id="editUserForm" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    ویرایش کاربر
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نام
                        </label>
                        <input id="edit_name" name="name" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نام کاربری
                        </label>
                        <input id="edit_username" name="username" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نقش
                        </label>
                        <select name="role_id" id="edit_role" class="form-select" required>
                            <option value="">
                                انتخاب نقش
                            </option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($role->id); ?>">
                                    <?php echo e($role->display_name); ?>

                                </option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            ایمیل
                        </label>
                        <input id="edit_email" name="email" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            موبایل
                        </label>
                        <input id="edit_phone" name="phone" class="form-control">
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input id="edit_active" type="checkbox" class="form-check-input" name="is_active" value="1">
                            <label class="form-check-label">
                                کاربر فعال باشد
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="btn btn-primary" title="ذخیره تغییرات">
                    ذخیره تغییرات
                </button>
            </div>

        </form>
        
    </div>
</div><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/users/modals/edit.blade.php ENDPATH**/ ?>