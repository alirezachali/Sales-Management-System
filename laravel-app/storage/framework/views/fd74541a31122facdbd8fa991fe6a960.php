<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form action="<?php echo e(route('users.store')); ?>" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    افزودن کاربر جدید
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نام و نام خانوادگی
                        </label>
                        <input type="text" class="form-control" name="name" required value="<?php echo e(old('name')); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نام کاربری
                        </label>
                        <input type="text" class="form-control" name="username" required value="<?php echo e(old('username')); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نقش
                        </label>
                        <select name="role_id" class="form-select" required>
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
                        <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            موبایل
                        </label>
                        <input type="text" class="form-control" name="phone" value="<?php echo e(old('phone')); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            رمز عبور
                        </label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            تکرار رمز عبور
                        </label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked
                                value="<?php echo e(old('is_active', true) ? 'checked' : ''); ?>">
                            <label class="form-check-label">
                                کاربر فعال باشد
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    title="انصراف و برگشت به صفحه قبلی">
                    انصراف
                </button>
                <button type="submit" class="btn btn-primary" tile="ذخیره تغییرات">
                    ذخیره
                </button>
            </div>

        </form>
    </div>
</div>
<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/users/modals/create.blade.php ENDPATH**/ ?>