<div dir="rtl">

    {{-- پیام موفقیت --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{-- پیام خطا --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">

        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد کالاها</div>
                    <div class="h1 mb-0">{{ $totalProducts }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">کالاهای فعال</div>
                    <div class="h1 mb-0 text-success">{{ $activeProducts }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">کالاهای غیرفعال</div>
                    <div class="h1 mb-0 text-danger">{{ $inactiveProducts }}</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">موجودی کم</div>
                    <div class="h1 mb-0 text-warning">{{ $lowStockProducts }}</div>
                </div>
            </div>
        </div>

    </div>

    {{-- فیلترها --}}
    <div class="card glass-card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2">

                <div class="col-md-7">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو نام یا بارکد...">
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

    {{-- جدول کالاها --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-box-seam-fill text-primary"></i>
                    مدیریت محصولات
                </h3>
                <small class="text-muted">مدیریت اطلاعات محصولات موجود در فروشگاه</small>
            </div>
            <button type="button" class="btn btn-primary" wire:click="openCreateModal"
                title="افزودن محصول جدید به سیستم">
                <i class="bi bi-plus-circle"></i>
                افزودن محصول
            </button>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th width="130">بارکد</th>
                        <th>نام کالا</th>
                        <th>دسته بندی</th>
                        <th width="130">قیمت فروش</th>
                        <th width="90">موجودی</th>
                        <th width="230">عملیات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($products as $product)
                        <tr wire:key="product-{{ $product->id }}">
                            <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                            <td>{{ $product->barcode }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name }}</td>
                            <td>
                                {{ number_format($product->sell_price) }}
                                <span>{{ setting('currency', '') }}</span>
                            </td>
                            <td>
                                {{ $product->formatted_stock }}
                                <span class="badge bg-secondary text-dark">{{ $product->unit }}</span>
                            </td>
                            <td>
                                {{-- دکمه ویرایش کالا --}}
                                <button type="button" class="btn btn-sm btn-warning text-dark"
                                    wire:click="openEditModal({{ $product->id }})" title="ویرایش کالا">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>

                                {{-- دکمه چاپ لیبل --}}
                                <button type="button" class="btn btn-sm btn-info text-dark print-label-btn"
                                    data-id="{{ $product->id }}" title="چاپ لیبل">
                                    <i class="bi bi-printer-fill"></i>
                                </button>

                                {{-- دکمه مشاهده موجودی و ورود و خروج این کالا به انبار --}}
                                <a href="{{ route('products.stock', $product) }}" class="btn btn-sm btn-light"
                                    title="مشاهده سوابق ورود و خروج این کالا به انبار">
                                    <i class="bi bi-boxes"></i>
                                </a>

                                {{-- دکمه ورود کالا به انبار (مستقیم مودال ورود را در صفحه‌ی گردش کالا باز می‌کند) --}}
                                <a href="{{ route('products.stock', ['product' => $product, 'action' => 'purchase']) }}"
                                    class="btn btn-sm btn-outline-success" title="ورود این کالا به انبار">
                                    <i class="bi bi-plus-lg"></i>
                                </a>

                                {{-- دکمه خروج کالا از انبار (مستقیم مودال خروج را در صفحه‌ی گردش کالا باز می‌کند) --}}
                                <a href="{{ route('products.stock', ['product' => $product, 'action' => 'sale']) }}"
                                    class="btn btn-sm btn-outline-danger" title="خروج این کالا از انبار">
                                    <i class="bi bi-dash-lg"></i>
                                </a>

                                {{-- دکمه حذف کالا --}}
                                <button type="button" class="btn btn-danger text-dark btn-sm"
                                    wire:click="confirmDelete({{ $product->id }})" title="حذف این کالا">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
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

            <div class="mt-3">{{ $products->links() }}</div>

        </div>
    </div>

    {{-- ============================ مودال افزودن/ویرایش کالا (بدون بوت‌استرپ JS، دقیقاً مثل ماژول تامین‌کنندگان) ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="product-form-modal">
            <div class="modal-dialog modal-xl modal-dialog-centered">

                <form wire:submit="save">

                    <div class="modal-content glass-card">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi {{ $editingProductId ? 'bi-pencil-fill' : 'bi-plus-circle-fill' }}"></i>
                                {{ $editingProductId ? 'ویرایش کالا' : 'افزودن کالا جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"
                                title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
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

                                <div class="col-md-6 mb-3">
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
                                title="انصراف">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save" title="ذخیره کالا">
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

    {{-- ============================ مودال تایید حذف ============================ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="product-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف کالا</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این کالا مطمئن هستید؟ این عملیات قابل بازگشت نیست.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete"
                            wire:loading.attr="disabled" wire:target="delete">
                            <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm"></span>
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================ مودال چاپ لیبل ============================ --}}
    <div class="modal fade" id="labelModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">چاپ لیبل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" title="بستن"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">تعداد لیبل</label>
                        <input type="number" id="label_quantity" class="form-control" value="1"
                            min="1">
                    </div>
                    <div id="label-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="print-label-btn" class="btn btn-primary">
                        <i class="bi bi-printer"></i>
                        چاپ
                    </button>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            // چاپ لیبل کالا از طریق endpoint موجود، مستقل از رندر لیوایر (فقط یک‌بار ثبت می‌شود)
            let currentLabelTemplate = '';

            document.addEventListener('click', function(e) {
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

                        currentLabelTemplate = `
                            <div class="label-print-area" style="width: ${data.label_width}mm; height: ${data.label_height}mm;">
                                ${labelName}
                                ${labelPrice}
                                ${labelBarcode}
                                ${labelCode}
                            </div>
                        `;

                        document.getElementById('label-container').innerHTML = currentLabelTemplate;

                        new bootstrap.Modal(document.getElementById('labelModal')).show();
                    })
                    .catch(error => console.error('Label Error:', error));
            });

            document.getElementById('print-label-btn')?.addEventListener('click', function() {
                let quantity = parseInt(document.getElementById('label_quantity').value) || 1;
                let container = document.getElementById('label-container');
                let output = '';

                for (let i = 0; i < quantity; i++) {
                    output += currentLabelTemplate;
                }

                container.innerHTML = output;
                window.print();
            });

            document.getElementById('labelModal')?.addEventListener('hidden.bs.modal', function() {
                document.getElementById('label-container').innerHTML = '';
                currentLabelTemplate = '';
            });
        </script>
    @endscript

</div>
