<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <form method="POST" id="editProductForm" action="#">

            @csrf
            @method('PUT')

            <div class="modal-content glass-card">

                <div class="modal-header">

                    <h5 class="modal-title" id="editProductModalLabel">

                        <i class="bi bi-pencil-square"></i>

                        ویرایش کالا

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        {{-- بارکد --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                بارکد
                            </label>

                            <input type="text" name="barcode" id="edit_barcode"
                                class="form-control @error('barcode') is-invalid @enderror"
                                value="{{ old('barcode') }}">

                            @error('barcode')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- نام کالا --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                نام کالا
                            </label>

                            <input type="text" name="name" id="edit_name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- دسته بندی --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                دسته بندی
                            </label>

                            <select name="category_id" id="edit_category_id"
                                class="form-select @error('category_id') is-invalid @enderror">

                                <option value="">
                                    انتخاب کنید
                                </option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach

                            </select>

                            @error('category_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- قیمت خرید --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                قیمت خرید
                            </label>

                            <input type="number" name="buy_price" id="edit_buy_price"
                                class="form-control @error('buy_price') is-invalid @enderror"
                                value="{{ old('buy_price') }}">

                            @error('buy_price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- قیمت فروش --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                قیمت فروش
                            </label>

                            <input type="number" name="sell_price" id="edit_sell_price"
                                class="form-control @error('sell_price') is-invalid @enderror"
                                value="{{ old('sell_price') }}">

                            @error('sell_price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- واحد --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                واحد
                            </label>

                            <select name="unit" id="edit_unit"
                                class="form-select @error('unit') is-invalid @enderror">

                                <option value="عدد">
                                    عدد
                                </option>

                                <option value="کیلوگرم">
                                    کیلوگرم
                                </option>

                                <option value="لیتر">
                                    لیتر
                                </option>

                            </select>

                            @error('unit')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- وضعیت --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                وضعیت
                            </label>

                            <select name="is_active" id="edit_is_active"
                                class="form-select @error('is_active') is-invalid @enderror">

                                <option value="1">
                                    فعال
                                </option>

                                <option value="0">
                                    غیرفعال
                                </option>

                            </select>

                            @error('is_active')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        انصراف

                    </button>

                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-save"></i>

                        ذخیره تغییرات

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
