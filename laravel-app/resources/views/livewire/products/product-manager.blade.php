<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    {{-- فایل کدهای مربوط به نمایش پیغام های اطلاع رسانی --}}
    @include('partials.flash-messages')

    {{-- ======== استایل چاپ لیبل (مستقل از صفحه) ======== --}}
    <style>
        /* پیش‌نمایش لیبل داخل مودال */
        #label-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            padding: 8px 0;
        }

        .label-print-area {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: #ffffff;
            color: #000000;
            border: 1px dashed #adb5bd;
            padding: 4mm;
            box-sizing: border-box;
            overflow: hidden;
            font-family: 'Vazirmatn', sans-serif;
            border-radius: 20px;
        }

        .label-print-area .label-name {
            font-size: 9pt;
            font-weight: 700;
            line-height: 1.2;
            word-break: break-word;
            width: 100%;
        }

        .label-print-area .label-price {
            font-size: 8pt;
            font-weight: 700;
            color: #d63384;
            margin-top: 1mm;
        }

        .label-print-area .label-barcode {
            margin-top: 1mm;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .label-print-area .label-barcode svg {
            max-width: 100%;
            height: auto;
        }

        .label-print-area .label-code {
            font-size: 7pt;
            letter-spacing: 1px;
            color: #333;
            margin-top: 0.5mm;
        }

        /* فقط هنگام چاپ: بقیه صفحه مخفی و فقط لیبل‌ها چاپ می‌شوند */
        @media print {
            body * {
                visibility: hidden !important;
            }

            #label-container,
            #label-container * {
                visibility: visible !important;
            }

            #label-container {
                position: absolute;
                left: 0;
                top: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 0;
                justify-content: flex-start;
                align-items: flex-start;
                padding: 0;
            }

            .label-print-area {
                border: none;
                padding: 2mm;
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>


    {{--======== کارت‌های آماری ========--}}
    <div class="row row-cards mb-4">

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">تعداد کالاها</div>
                    <div class="h1 mb-0">{{ $totalProducts }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">کالاهای فعال</div>
                    <div class="h1 mb-0 text-success">{{ $activeProducts }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">کالاهای غیرفعال</div>
                    <div class="h1 mb-0 text-danger">{{ $inactiveProducts }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">موجودی کم</div>
                    <div class="h1 mb-0 text-warning">{{ $lowStockProducts }}</div>
                </div>
            </div>
        </div>

    </div>

    {{--========= فیلترها ==========--}}
    <div class="card glass-card mb-4">
        <div class="card-body">
            <div class="row g-2">

                <div class="col-md-7">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        data-hotkey="products_search" placeholder="جستجو نام یا بارکد..."
                        title="جستجوی کالا{{ hotkeyHint('products_search') }}">
                </div>

                <div class="col-md-3">
                    <select wire:model.live="filterCategoryId" class="form-select">
                        <option value="">همه دسته بندی ها</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100"
                        title="پاک کردن فیلترهای جستجو">
                        پاک کردن فیلترها
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{--=================== جدول لیست کالاها ===================--}}
    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-box-seam-fill text-fuchsia"></i>
                    مدیریت محصولات
                </h3>
                <small class="text-muted">مدیریت اطلاعات محصولات موجود در فروشگاه</small>
            </div>
            @can('products.create')
            <button type="button" class="btn btn-primary glow-btn" wire:click="openCreateModal"
                data-hotkey="products_add" title="افزودن محصول جدید به سیستم{{ hotkeyHint('products_add') }}">
                <i class="bi bi-plus-circle"></i>
                افزودن محصول
            </button>
            @endcan
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="130">بارکد</th>
                            <th>نام کالا</th>
                            <th>دسته بندی</th>
                            <th width="130">قیمت ({{ setting('currency', 'تومان') }})</th>
                            <th width="100">موجودی</th>
                            <th width="130">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr wire:key="product-{{ $product->id }}">
                                <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                <td>
                                    <strong>
                                        {{ $product->barcode }}
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        {{ $product->name }}
                                    </strong>
                                </td>
                                <td>{{ $product->category?->name }}</td>
                                <td>
                                    <strong class="text-success">
                                        {{ number_format($product->sell_price) }}
                                        {{-- <span>{{ setting('currency', '') }}</span> --}}
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle">
                                        {{ $product->formatted_stock }} {{ $product->unit }}
                                    </span>
                                </td>
                                <td>
                                    {{--===== دکمه ویرایش کالا =====--}}
                                    @can('products.edit')
                                    <button type="button" class="btn btn-sm btn-outline-warning"
                                        wire:click="openEditModal({{ $product->id }})" title="ویرایش کالا">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    @endcan

                                    {{--===== دکمه چاپ لیبل =====--}}
                                    <button type="button" class="btn btn-sm btn-outline-primary print-label-btn"
                                        data-id="{{ $product->id }}" title="چاپ لیبل">
                                        <i class="bi bi-upc-scan"></i>
                                    </button>

                                    {{--== دکمه مشاهده موجودی و ورود و خروج این کالا به انبار ==--}}
                                    <a href="{{ route('products.stock', $product) }}" class="btn btn-sm btn-outline-success"
                                        title="مشاهده سوابق ورود و خروج این کالا به انبار">
                                        <i class="bi bi-boxes"></i>
                                    </a>

                                    {{--===== دکمه حذف کالا =====--}}
                                    {{-- @can('products.delete')
                                    <button type="button" class="btn btn-danger text-dark btn-sm"
                                        wire:click="confirmDelete({{ $product->id }})" title="حذف این کالا">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    @endcan --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    هیچ کالایی ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="card-footer">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    {{-- =============== مودال افزودن/ویرایش کالا =============== --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="product-form-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">

                <form wire:submit="save">

                    <div class="modal-content glass-card">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi {{ $editingProductId ? 'bi-pencil-fill' : 'bi-plus-circle-fill' }}"></i>
                                {{ $editingProductId ? 'ویرایش کالا' : 'افزودن کالا جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"
                                title="بستن{{ hotkeyHint('form_cancel') }}"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-5">
                                    <label class="form-label">بارکد</label>
                                    <input type="text" wire:model="barcode"
                                        class="form-control @error('barcode') is-invalid @enderror">
                                    @unless ($editingProductId)
                                        <button type="button" class="btn btn-outline-primary mt-2"
                                            wire:click="generateBarcode">
                                            <i class="bi bi-upc-scan"></i>
                                            تولید بارکد
                                        </button>
                                    @endunless
                                    @error('barcode')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-7 mb-3">
                                    <label class="form-label">نام کالا</label>
                                    <input type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">دسته بندی</label>
                                        <select wire:model="category_id" class="form-select">
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">برند</label>
                                        <select wire:model="brand_id" class="form-select">
                                            <option value="">انتخاب کنید</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">قیمت خرید</label>
                                    <input type="number" wire:model="buy_price"
                                        class="form-control @error('buy_price') is-invalid @enderror">
                                    @error('buy_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">قیمت فروش</label>
                                    <input type="number" wire:model="sell_price"
                                        class="form-control @error('sell_price') is-invalid @enderror">
                                    @error('sell_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label
                                        class="form-label">{{ $editingProductId ? 'موجودی' : 'موجودی اولیه' }}</label>
                                    <input type="number" step="0.001" wire:model="stock"
                                        class="form-control @error('stock') is-invalid @enderror">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">واحد</label>
                                    <select wire:model="unit" class="form-select">
                                        <option value="عدد">عدد</option>
                                        <option value="کیلوگرم">کیلوگرم</option>
                                        <option value="لیتر">لیتر</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">وضعیت</label>
                                    <select wire:model="is_active" class="form-select">
                                        <option value="1">فعال</option>
                                        <option value="0">غیرفعال</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals"
                                title="انصراف{{ hotkeyHint('form_cancel') }}">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save" title="ذخیره کالا{{ hotkeyHint('form_save') }}">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                <i class="bi bi-save" wire:loading.remove wire:target="save"></i>
                                {{ $editingProductId ? 'ذخیره تغییرات' : 'ذخیره کالا' }}
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    @endif

    {{-- =================== مودال تایید حذف ============================ --}}
    {{-- @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="product-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-dark">
                        <h5 class="modal-title">حذف کالا</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این کالا مطمئن هستید؟ این عملیات قابل بازگشت نیست.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals"
                            title="انصراف{{ hotkeyHint('form_cancel') }}">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled" wire:target="delete">
                            <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm"></span>
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

    {{-- =================== مودال چاپ لیبل ==================== --}}
    <div class="modal modal-blur fade" id="labelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">چـــاپ لـــیـــبـــل</h5>
                    <button type="button" class="btn-close" id="label-modal-close" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">تــعــداد لــیــبــل</label>
                        <input type="number" id="label_quantity" class="form-control" value="1"
                            min="1">
                    </div>
                    <div id="label-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="print-label-btn" class="btn btn-primary">
                        <i class="bi bi-printer"></i>
                        چــــاپ
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ استایل و رفتار مودال چاپ لیبل ============= --}}
    @push('scripts')
        <script>
            (function () {
                const modalEl = document.getElementById('labelModal');
                if (!modalEl) return;

                let currentLabelTemplate = '';

                const getContainer = () => document.getElementById('label-container');

                function openLabelModal(template) {
                    getContainer().innerHTML = template;
                    currentLabelTemplate = template;

                    modalEl.classList.add('show', 'd-block');
                    modalEl.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('modal-open');
                    // افزودن پس‌زمینه تیره پشت مودال
                    if (!document.querySelector('.modal-backdrop')) {
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                }

                function closeLabelModal() {
                    modalEl.classList.remove('show', 'd-block');
                    modalEl.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                    getContainer().innerHTML = '';
                    currentLabelTemplate = '';
                }

                // باز کردن مودال با کلیک روی دکمه چاپ لیبل هر محصول
                document.addEventListener('click', function (e) {
                    const button = e.target.closest('.print-label-btn');
                    if (!button) return;

                    const productId = button.dataset.id;

                    fetch(`/products/${productId}/label`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('خطا در دریافت اطلاعات لیبل');
                            }
                            return response.json();
                        })
                        .then(data => {
                            let labelName = data.label_show_name ?
                                `<div class="label-name">${data.name}</div>` : '';
                            let labelPrice = data.label_show_price ?
                                `<div class="label-price">${Number(data.price).toLocaleString()} تومان</div>` : '';
                            let labelBarcode = data.label_show_barcode ?
                                `<div class="label-barcode">${data.barcode_svg}</div>` : '';
                            let labelCode = data.label_show_code ?
                                `<div class="label-code">${data.barcode}</div>` : '';

                            const template = `
                                <div class="label-print-area" style="width: ${data.label_width}mm; height: ${data.label_height}mm;">
                                    ${labelName}
                                    ${labelPrice}
                                    ${labelBarcode}
                                    ${labelCode}
                                </div>
                            `;

                            openLabelModal(template);
                        })
                        .catch(error => console.error('Label Error:', error));
                });

                // دکمه چاپ با تعداد دلخواه
                document.getElementById('print-label-btn')?.addEventListener('click', function () {
                    let quantity = parseInt(document.getElementById('label_quantity').value) || 1;
                    let output = '';
                    for (let i = 0; i < quantity; i++) {
                        output += currentLabelTemplate;
                    }
                    getContainer().innerHTML = output;
                    window.print();
                });

                // بستن مودال (دکمه بستن، کلیک روی پس‌زمینه، و کلید Escape)
                document.getElementById('label-modal-close')?.addEventListener('click', closeLabelModal);

                modalEl.addEventListener('click', function (e) {
                    if (e.target === modalEl) closeLabelModal();
                });

                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && modalEl.classList.contains('show')) {
                        closeLabelModal();
                    }
                });
            })();
        </script>
    @endpush

</div>
