<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">

        <form id="editRoleForm" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="modal-header">
                <h5 class="modal-title">
                    ویرایش نقش
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">
                        نام نقش
                    </label>
                    <input id="edit_name" name="display_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        شناسه
                    </label>
                    <input id="edit_slug" name="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        توضیحات
                    </label>
                    <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    انصراف
                </button>
                <button class="btn btn-primary">
                    ذخیره تغییرات
                </button>
            </div>

        </form>

    </div>
</div><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/roles/modals/edit.blade.php ENDPATH**/ ?>