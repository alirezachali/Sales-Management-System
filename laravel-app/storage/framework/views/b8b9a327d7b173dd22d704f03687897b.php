

<div class="modal fade" id="deleteCustomerModal">

    <div class="modal-dialog modal-dialog-centered">

        <form
            id="deleteCustomerForm"
            method="POST">

            <?php echo csrf_field(); ?>

            <?php echo method_field('DELETE'); ?>

            <div class="modal-content">

                <div class="modal-header">

                    <h5>

                        حذف مشتری

                    </h5>

                </div>

                <div class="modal-body">

                    آیا از حذف این مشتری مطمئن هستید؟

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        انصراف

                    </button>

                    <button
                        class="btn btn-danger">

                        حذف

                    </button>

                </div>

            </div>

        </form>

    </div>

</div><?php /**PATH C:\Users\Ali\Documents\Sales-Management-System\app\resources\views/customers/modals/delete.blade.php ENDPATH**/ ?>