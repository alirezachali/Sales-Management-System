<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="createCategoryForm" action="{{ route('categories.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle-fill text-success me-2"></i>
                        افزودن دسته‌بندی جدید
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">
                            نام دسته‌بندی
                        </label>
                        <input type="text" name="name" class="form-control" maxlength="100" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            توضیحات
                        </label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">
                            فعال باشد
                        </label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        انصراف
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i>
                        ذخیره
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
