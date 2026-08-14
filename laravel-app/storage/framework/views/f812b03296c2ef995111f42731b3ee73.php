<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog">

        <form action="<?php echo e(route('roles.store')); ?>" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>

            <div class="modal-header">
                <h5 class="modal-title">
                    نقش جدید
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">
                        نام نقش
                    </label>
                    <input type="text" name="display_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        شناسه (عنوان نقش به انگلیسی و حروف کوچک)
                    </label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        توضیحات
                    </label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    انصراف
                </button>
                <button class="btn btn-primary">
                    ذخیره
                </button>
            </div>

        </form>

    </div>
</div>

<?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/roles/modals/create.blade.php ENDPATH**/ ?>