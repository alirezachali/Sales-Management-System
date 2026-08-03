<!-- Start Change Password User Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">

    <!-- Modal Dialog -->
    <div class="modal-dialog">

        <form id="changePasswordForm"
              method="POST"
              class="modal-content">

            @csrf
            @method('PUT')

            <!-- Modal Header -->
            <div class="modal-header">

                <h5 class="modal-title">
                    تغییر رمز عبور
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>

            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <p class="text-muted">
                    تغییر رمز عبور کاربر
                    <strong id="passwordUserName"></strong>
                </p>

                <div class="mb-3">

                    <label class="form-label">
                        رمز عبور جدید
                    </label>

                    <input type="password"
                           name="password"
                           class="form-control"
                           required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        تکرار رمز عبور
                    </label>

                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           required>

                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="انصراف و بازگشت به صفحه قبلی">
                    انصراف
                </button>

                <button class="btn btn-primary" title="برای ذخیره تغییرات کلیک کنید">
                    ذخیره
                </button>

            </div>

        </form>

    </div>

</div>
<!-- End Change Password User Modal -->
