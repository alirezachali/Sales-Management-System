<!-- Start Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">

    <!-- Modal Dialog -->
    <div class="modal-dialog modal-lg">

        <form id="editUserForm"
              method="POST"
              class="modal-content">

            @csrf
            @method('PUT')

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

                        <input id="edit_name"
                               name="name"
                               class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            نام کاربری
                        </label>

                        <input id="edit_username"
                               name="username"
                               class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            نقش
                        </label>

                        <select name="role_id" class="form-select" required>

                            <option value="">
                                انتخاب نقش
                            </option>

                            @foreach($roles as $role)

                            <option value="{{ $role->id }}">
                                {{ $role->display_name }}
                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            ایمیل
                        </label>

                        <input id="edit_email"
                               name="email"
                               class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            موبایل
                        </label>

                        <input id="edit_phone"
                               name="phone"
                               class="form-control">

                    </div>

                    <div class="col-12">

                        <div class="form-check form-switch">

                            <input id="edit_active"
                                   type="checkbox"
                                   class="form-check-input"
                                   name="is_active"
                                   value="1">

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
</div>
<!-- End Edit User Modal -->
