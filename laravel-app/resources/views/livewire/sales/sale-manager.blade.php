<div dir="rtl">

    {{-- Success/Error Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @error('checkout')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    {{-- Page Header --}}
    <div class="card shadow-sm mb-3 border-3" wire:loading.class="opacity-50">
        <div class="card-header align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-cart-check-fill text-primary"></i>
                    فروش (صندوق)
                </h3>
                <small class="text-muted">سبد خرید مشتری و صدور فاکتور خرید</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ستون جستجو و افزودن کالا --}}
        <div class="col-lg-7">
            <div class="card glass-card mb-3 border-3">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-upc-scan text-primary"></i></span>
                                <input type="text" class="form-control" placeholder="اسکن / وارد کردن بارکد"
                                    wire:model="barcode" wire:keydown.enter="addByBarcode" autofocus>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search text-info"></i></span>
                                <input type="text" class="form-control" wire:model.live.debounce.400ms="search"
                                    placeholder="جستجو بر اساس نام یا بارکد کالا">
                            </div>
                        </div>
                    </div>

                    @if ($search && $products->count())
                        <div class="list-group mt-2">
                            @foreach ($products as $product)
                                <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between"
                                    wire:click="addProduct({{ $product->id }})">
                                    <span>{{ $product->name }} <small
                                            class="text-muted">({{ $product->barcode }})</small></span>
                                    <span class="badge bg-success text-dark">{{ number_format($product->sell_price) }}</span>
                                </button>
                            @endforeach
                        </div>
                    @elseif ($search)
                        <div class="text-muted small mt-2">کالایی یافت نشد.</div>
                    @endif
                </div>
            </div>

            {{-- سبد فروش --}}
            <div class="card glass-card border-3">
                <div class="card-header">
                    <h5 class="fw-bold text-success">جدول آیتم های سبد خرید مشتری</h5>
                </div>
                <div class="table-responsive rounded-3">
                    <table class="table table-bordered table-hover align-middle">

                        <thead>
                            <tr>
                                <th>کالا</th>
                                <th>قیمت</th>
                                <th class="text-center">تعداد</th>
                                <th>جمع</th>
                                <th class="text-center">عملیات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($cart as $item)
                                <tr wire:key="cart-{{ $item['id'] }}">
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ number_format($item['price']) }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-secondary" type="button"
                                                wire:click="decrementQty({{ $item['id'] }})">-</button>
                                            <span class="btn btn-light disabled">{{ $item['quantity'] }}</span>
                                            <button class="btn btn-outline-secondary" type="button"
                                                wire:click="incrementQty({{ $item['id'] }})">+</button>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item['price'] * $item['quantity']) }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="removeFromCart({{ $item['id'] }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">سبد فروش خالی است.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ستون جمع‌بندی و پرداخت --}}
        <div class="col-lg-5">
            <div class="card glass-card border-3">

                <div class="card-header">
                    <h5 class="fw-bold text-primary">جمع‌بندی فاکتور</h5>
                </div>

                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">مشتری</label>
                        <select class="form-select" wire:model="customerId">
                            <option value="">مشتری متفرقه</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }}
                                    ({{ $customer->mobile }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">تخفیف</label>
                        <input type="number" step="0.01" class="form-control" wire:model.live="discount">
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mb-1">
                        <span>جمع کل:</span>
                        <strong>{{ number_format($this->subtotal) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>مبلغ نهایی:</span>
                        <strong class="text-primary fs-5">{{ number_format($this->finalPrice) }}</strong>
                    </div>

                    <button type="button" class="btn btn-success text-dark w-100" wire:click="openCheckoutModal">
                        <i class="bi bi-cash-coin"></i> پرداخت و ثبت فاکتور
                    </button>
                </div>
            </div>

            {{-- آخرین فاکتورهای ثبت شده --}}
            <div class="card glass-card border-3 mt-3">
                <div class="card-header">
                    <h5 class="fw-bold text-warning">آخرین فاکتورها</h5>
                </div>
                
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>شماره فاکتور</th>
                                    <th>مشتری</th>
                                    <th>مبلغ</th>
                                    <th>چاپ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentSales as $sale)
                                    <tr wire:key="sale-{{ $sale->id }}">
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->customer->full_name ?? 'متفرقه' }}</td>
                                        <td>{{ number_format($sale->final_price) }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="printInvoice({{ $sale->id }})">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">فاکتوری ثبت نشده است.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer">

                        {{-- {{ $recentSales->links() }} --}}

                    </div>
                
            </div>
        </div>
    </div>


    {{-- مودال پرداخت / تسویه --}}
    @if ($showCheckoutModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="checkout-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="checkout">
                        <div class="modal-header">
                            <h5 class="modal-title">تسویه و ثبت فاکتور فروش</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">نوع پرداخت <span class="text-danger">*</span></label>
                                    <select class="form-select @error('paymentType') is-invalid @enderror"
                                        wire:model="paymentType">
                                        <option value="cash">نقدی</option>
                                        <option value="card">کارتخوان</option>
                                        <option value="credit">نسیه (اعتباری)</option>
                                    </select>
                                    @error('paymentType')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">مبلغ پرداختی</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('paidAmount') is-invalid @enderror"
                                        wire:model="paidAmount" @if ($paymentType === 'credit') disabled @endif>
                                    @error('paidAmount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info d-flex justify-content-between mb-0">
                                        <span>مبلغ قابل پرداخت:</span>
                                        <strong>{{ number_format($this->finalPrice) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="checkout">
                                <span wire:loading wire:target="checkout"
                                    class="spinner-border spinner-border-sm"></span>
                                ثبت نهایی فاکتور
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال موفقیت + چاپ فاکتور --}}
    @if ($showInvoiceModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="invoice-modal">
            <div class="modal-dialog">
                <div class="modal-content text-center">
                    <div class="modal-header">
                        <h5 class="modal-title w-100">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            فاکتور با موفقیت ثبت شد
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">آیا مایل به چاپ فاکتور فروش هستید؟</p>
                        <a href="{{ route('invoice', $lastSaleId) }}" target="_blank"
                            class="btn btn-primary btn-lg">
                            <i class="bi bi-printer"></i> چاپ فاکتور فروش
                        </a>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- باز کردن خودکار فاکتور در تب جدید هنگام کلیک روی دکمه چاپ در لیست فاکتورهای اخیر --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-invoice', (event) => {
                const url = event.url ?? event[0]?.url;
                if (url) {
                    window.open(url, '_blank');
                }
            });
        });
    </script>

</div>
