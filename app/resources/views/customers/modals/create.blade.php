<div class="modal fade" id="createCustomerModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form action="{{ route('customers.store') }}" method="POST">

            @csrf

            <div class="modal-content glass-card">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-person-plus"></i>

                        افزودن مشتری

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">نام</label>

                            <input type="text"
                                   class="form-control"
                                   name="first_name"
                                   required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">نام خانوادگی</label>

                            <input type="text"
                                   class="form-control"
                                   name="last_name"
                                   required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">موبایل</label>

                            <input type="text"
                                   class="form-control"
                                   name="mobile"
                                   required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">تلفن</label>

                            <input type="text"
                                   class="form-control"
                                   name="phone">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">نقش مشتری</label>

                            <select name="customer_role_id" class="form-select">

                                @foreach($roles as $role)

                                    <option value="{{ $role->id }}"
                                        {{ $role->is_default ? 'selected' : '' }}>

                                        {{ $role->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">وضعیت</label>

                            <select name="is_active" class="form-select">

                                <option value="1">فعال</option>

                                <option value="0">غیرفعال</option>

                            </select>

                        </div>

                        <div class="col-12">

                            <label class="form-label">توضیحات</label>

                            <textarea class="form-control"
                                      rows="3"
                                      name="notes"></textarea>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        انصراف

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>

                        ذخیره

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>