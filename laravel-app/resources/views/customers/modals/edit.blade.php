<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form id="editCustomerForm" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content glass-card">

                <div class="modal-header">
                    <h5 class="modal-title">
                        ویرایش مشتری
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>
                                نام
                            </label>
                            <input id="edit_first_name" name="first_name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                نام خانوادگی
                            </label>
                            <input id="edit_last_name" name="last_name" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                موبایل
                            </label>
                            <input id="edit_mobile" name="mobile" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                تلفن
                            </label>
                            <input id="edit_phone" name="phone" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>
                                نقش
                            </label>
                            <select id="edit_customer_role_id" name="customer_role_id" class="form-select">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>
                                وضعیت
                            </label>
                            <select id="edit_is_active" name="is_active" class="form-select">
                                <option value="1">
                                    فعال
                                </option>
                                <option value="0">
                                    غیرفعال
                                </option>
                            </select>

                        </div>
                        <div class="col-12">
                            <label>
                                توضیحات
                            </label>
                            <textarea id="edit_notes" name="notes" rows="3" class="form-control"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        انصراف
                    </button>

                    <button class="btn btn-warning">
                        ذخیره تغییرات
                    </button>

                </div>
            </div>

        </form>

    </div>
</div>
