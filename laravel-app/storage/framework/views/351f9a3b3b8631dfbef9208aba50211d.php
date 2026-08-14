<div class="modal fade" id="deleteRoleModal" tabindex="-1">
    <div class="modal-dialog">

        <form id="deleteRoleForm" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    حذف نقش
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>
                    آیا از حذف نقش
                    <strong id="deleteRoleName"></strong>
                    اطمینان دارید؟
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    انصراف
                </button>
                <button class="btn btn-danger">
                    حذف
                </button>
            </div>

        </form>

    </div>
</div><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\laravel-app\resources\views/roles/modals/delete.blade.php ENDPATH**/ ?>