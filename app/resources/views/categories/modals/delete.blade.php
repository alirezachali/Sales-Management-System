<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">

            <form id="deleteCategoryForm" method="POST">
                @csrf
                @method('DELETE')

                <input type="hidden" id="delete_id" name="id">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">
                        <i class="bi bi-trash-fill me-2"></i>
                        حذف دسته‌بندی
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger display-3"></i>
                    <h5 class="mt-3">
                        آیا از حذف این دسته‌بندی مطمئن هستید؟
                    </h5>
                    <p class="text-muted mb-0">
                        <strong id="delete_category_name"></strong>
                    </p>
                    <small class="text-danger">
                        در صورت وجود کالا در این دسته‌بندی، عملیات حذف انجام نخواهد شد.
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        انصراف
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        حذف دسته‌بندی
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
