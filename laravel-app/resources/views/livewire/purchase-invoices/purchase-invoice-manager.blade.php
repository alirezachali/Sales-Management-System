<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    {{-- پیام موفقیت --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{-- پیام خطا --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    <div class="card shadow-sm border-3 mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="bi bi-file-earmark-plus"></i>
                ثبت فاکتور خرید
            </h4>
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-2">
                    <label class="form-label" title="تاریخ خرید به صورت خودکار امروز را مشخص میکند">تاریخ خرید</label>
                    <input type="text" wire:model="purchase_date_jalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/11"
                        class="form-control @error('purchase_date_jalali') is-invalid @enderror">
                    @error('purchase_date_jalali')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label" title="تامین کننده ای که قصد خرید از آن را دارید را انتخاب کنید">تامین کننده</label>
                    <select wire:model="supplier_id" title="تامین کننده ای که قصد خرید از آن را دارید را انتخاب کنید"
                        class="form-select @error('supplier_id') is-invalid @enderror">
                        <option value="">انتخاب کنید...</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name }}
                                @if ($supplier->company_name)
                                    ({{ $supplier->company_name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label" title="انباری که قصد دارید محصولات را در آن تخلیه کنید را انتخاب کنید">انتخاب انبار</label>
                    <select wire:model="warehouse_id" title="انباری که قصد دارید محصولات را در آن تخلیه کنید را انتخاب کنید"
                        class="form-select @error('warehouse_id') is-invalid @enderror">
                        <option value="">انتخاب کنید...</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">
                                {{ $warehouse->name }}
                                @if ($warehouse->is_default)
                                    (پیش‌فرض)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-1">
                    <label class="form-label" title="شماره فاکتور به صورت خودکار انتخاب میشود">ش فاکتور</label>
                    <input type="text" class="form-control" value="{{ $this->nextInvoiceNumber }}" readonly 
                        title="شماره فاکتور به صورت خودکار انتخاب میشود">
                </div>

                <div class="col-md-3">
                    <label class="form-label">روش پرداخت</label>
                    <select wire:model="payment_method" class="form-select" title="روش پرداخت مبلغ فاکتور به تامین کننده را مشخص کنید">
                        <option value="cash">نقدی</option>
                        <option value="card">کارت</option>
                        <option value="transfer">کارت به کارت / حواله</option>
                        <option value="credit">نسیه</option>
                        {{-- <option value="other">سایر</option> --}}
                    </select>
                </div>

            </div>
        </div>
    </div>

    <div class="card shadow-sm border-3 mb-4" style="z-index: 2;">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-search"></i>
                جستجو و افزودن کالا
            </h5>

        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">بارکد کالا</label>
                    <input type="text" wire:model="product_barcode" wire:keydown.enter="processBarcode" title="بارکد کالا را اسکن کنید یا به صورت دستی وارد کنید"
                        class="form-control" placeholder="بارکد را اسکن یا وارد کنید">
                </div>
                <div class="col-md-6 position-relative">
                    <label class="form-label">یا جستجوی نام کالا</label>
                    <input type="text" wire:model.live.debounce.300ms="product_search" title="نام کالا را وارد کنید و نتایج رو به صورت لایو مشاهده کنید"
                        class="form-control" placeholder="نام کالا را جستجو کنید" autocomplete="off">
                    @if (!empty($searchResults))
                        <ul class="list-group position-absolute" id="purchase-inv-search-product">
                            @foreach ($searchResults as $result)
                                <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                    wire:click="selectSearchResult({{ $result['id'] }})">
                                    <span>{{ $result['name'] }}</span>
                                    <small class="text-muted">{{ $result['barcode'] }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="openNewProductModal" class="btn btn-success w-100"
                        title="برای اضافه کردن محصولی که در سیستم وجود ندارد کلیک کنید">
                        <i class="bi bi-plus-circle"></i>
                        محصول جدید
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-cart3"></i>
                لیست محصولات
            </h5>
            <span class="badge bg-primary rounded-pill">
                {{ count($items) }} قلم کالا
            </span>
        </div>
        <div class="card-body">
            @if (count($items) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>نام کالا</th>
                                <th width="130">بارکد</th>
                                <th width="80">تعداد</th>
                                <th width="150">قیمت خرید</th>
                                <th width="150">قیمت فروش</th>
                                <th width="130">مجموع</th>
                                <th width="40">حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr wire:key="item-{{ $item['id'] }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item['product_name'] }}
                                    </td>
                                    <td>{{ $item['barcode'] }}</td>
                                    <td>
                                        <input type="number" step="1" min="1"
                                            wire:change="updateItemPrice({{ $item['id'] }}, 'quantity', $event.target.value)"
                                            wire:init="updateItemPrice({{ $item['id'] }}, 'quantity', {{ $item['quantity'] }})"
                                            value="{{ $item['quantity'] }}"
                                            class="form-control form-control-sm">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="1000"
                                                wire:change="updateItemPrice({{ $item['id'] }}, 'buy_price', $event.target.value)"
                                                value="{{ $item['buy_price'] ?: $item['current_buy_price'] }}"
                                                class="form-control">
                                            <span class="input-group-text">{{ setting('currency', 'تومان') }}</span>
                                        </div>

                                        @if ($item['buy_price'] != $item['current_buy_price'] && $item['buy_price'] != '')

                                            <small class="text-success d-block">
                                                قبلی: {{ number_format($item['current_buy_price']) }}
                                            </small>

                                        @endif
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="1000"
                                                wire:change="updateItemPrice({{ $item['id'] }}, 'sell_price', $event.target.value)"
                                                value="{{ $item['sell_price'] ?: $item['current_sell_price'] }}"
                                                class="form-control">
                                            <span class="input-group-text">{{ setting('currency', 'تومان') }}</span>
                                        </div>
                                        @if ($item['sell_price'] != $item['current_sell_price'] && $item['sell_price'] != '')
                                            <small class="text-success d-block">
                                                قبلی: {{ number_format($item['current_sell_price']) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format(($item['quantity'] ?: 0) * ($item['buy_price'] ?: $item['current_buy_price'])) }}
                                        {{ setting('currency', 'تومان') }}
                                    </td>
                                    <td>
                                        <button type="button" wire:click="removeItem({{ $item['id'] }})"
                                            class="btn btn-sm btn-outline-danger" title="حذف از لیست">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x fs-1"></i>
                    <p class="mt-2 mb-0">هیچ محصولی به لیست اضافه نشده است.</p>
                    <small>از قسمت بالا بارکد کالا را اسکن کنید یا نام کالا را جستجو نمایید.</small>
                </div>
            @endif
        </div>
    </div>

    @if (count($items) > 0)
        <div class="card shadow-sm border-3 mt-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="alert alert-info mb-0 text-center text-info">
                            <small>تعداد اقلام</small>
                            <div class="h4 text-info mb-0">{{ $this->totalItemsCount }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-info mb-0 text-center text-info">
                            <small>مجموع تعداد</small>
                            <div class="h4 text-info mb-0">{{ number_format($this->totalQuantity, 0) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success mb-0 text-center text-success">
                            <small>مبلغ کل فاکتور</small>
                            <div class="h4 text-success mb-0">{{ number_format($this->totalAmount) }} {{ setting('currency', '') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid gap-2">
                            <button type="button" wire:click="save" id="submitInvoice" class="btn btn-success btn-lg"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                <i wire:loading.remove wire:target="save" class="bi bi-save"></i>
                                ثبت فاکتور خرید
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">توضیحات</label>
                        <textarea wire:model="notes" class="form-control" rows="2"
                            placeholder="توضیحات اضافی (اختیاری)"></textarea>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{--==================== مودال افزودن محصول جدید ====================--}}
    @if ($showNewProductModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="product-new-modal">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <form wire:submit="saveNewProduct">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-plus-circle-fill text-success"></i>
                                افزودن محصول جدید
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeProductModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">بارکد</label>
                                    <input type="text" wire:model="new_barcode"
                                        class="form-control @error('new_barcode') is-invalid @enderror">
                                    <button type="button" class="btn btn-outline-primary mt-2"
                                        wire:click="generateBarcode">
                                        <i class="bi bi-upc-scan"></i>
                                        تولید بارکد
                                    </button>
                                    @error('new_barcode')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نام کالا</label>
                                    <input type="text" wire:model="new_name"
                                        class="form-control @error('new_name') is-invalid @enderror">
                                    @error('new_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">دسته بندی</label>
                                    <select wire:model="new_category_id" class="form-select">
                                        <option value="">انتخاب کنید</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">قیمت خرید</label>
                                    <input type="number" wire:model="new_buy_price"
                                        class="form-control @error('new_buy_price') is-invalid @enderror">
                                    @error('new_buy_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">قیمت فروش</label>
                                    <input type="number" wire:model="new_sell_price"
                                        class="form-control @error('new_sell_price') is-invalid @enderror">
                                    @error('new_sell_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">واحد</label>
                                    <select wire:model="new_unit" class="form-select">
                                        <option value="عدد">عدد</option>
                                        <option value="کیلوگرم">کیلوگرم</option>
                                        <option value="لیتر">لیتر</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">وضعیت</label>
                                    <select wire:model="new_is_active" class="form-select">
                                        <option value="1">فعال</option>
                                        <option value="0">غیرفعال</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeProductModal">انصراف</button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="saveNewProduct">
                                <span wire:loading wire:target="saveNewProduct"
                                    class="spinner-border spinner-border-sm"></span>
                                ذخیره و افزودن به فاکتور
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>