<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" class="modal-content">
            @csrf

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    افزودن کاربر جدید
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نام و نام خانوادگی
                        </label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نام کاربری
                        </label>
                        <input type="text" class="form-control" name="username" required value="{{ old('username') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            نقش
                        </label>
                        <select name="role_id" class="form-select" required>
                            <option value="">
                                انتخاب نقش
                            </option>
                            @foreach ($roles as $role)
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
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            موبایل
                        </label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            رمز عبور
                        </label>
                        <input type="password" class="form-control" name="password" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            تکرار رمز عبور
                        </label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked
                                value="{{ old('is_active', true) ? 'checked' : '' }}">
                            <label class="form-check-label">
                                کاربر فعال باشد
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    title="انصراف و برگشت به صفحه قبلی">
                    انصراف
                </button>
                <button type="submit" class="btn btn-primary" tile="ذخیره تغییرات">
                    ذخیره
                </button>
            </div>

        </form>
    </div>
</div>
