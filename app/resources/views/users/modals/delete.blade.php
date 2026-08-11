<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">

        <form id="deleteUserForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')

            <!-- Modal Header -->
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    حذف کاربر
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" title="بستن"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <p>
                    آیا از حذف کاربر
                    <strong id="deleteUserName"></strong>
                    اطمینان دارید؟
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    title="انصراف و بازگشت به صفحه قبلی">
                    انصراف
                </button>
                <button class="btn btn-danger" title="حذف کاربر از سیستم">
                    حذف
                </button>
            </div>

        </form>
    </div>
</div>
