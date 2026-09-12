<div dir="rtl">

    {{-- ======================================================
         استایل‌های اختصاصی صندوق فروش
    ======================================================= --}}
    <style>
        :root {
            --pos-radius: 12px;
        }

        .pos-card {
            border: 3px solid var(--tblr-border-color, rgba(120, 130, 155, .18));
            border-radius: var(--pos-radius);
            box-shadow: 0 6px 24px rgba(20, 30, 60, .06);
            overflow: hidden;
        }

        .pos-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .9rem 1.15rem;
            /* border-bottom: 1px solid var(--tblr-border-color, rgba(120, 130, 155, .14)); */
            background: linear-gradient(135deg, rgba(32, 107, 196, .07), rgba(32, 107, 196, .01));
        }

        .pos-card-header .pos-title {
            font-weight: 800;
            font-size: 1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        /* فیلدهای بارکد و جستجو */
        .pos-scan-input {
            border-radius: 10px !important;
            font-size: 1rem;
        }

        .pos-input-group .input-group-text {
            border-radius: 0 12px 12px 0;
            background: rgba(8, 8, 8, 0.212);
            border-color: var(--tblr-border-color, rgba(120, 130, 155, .2));
        }

        /* نتایج جستجوی کالا */
        .pos-search-results {
            max-height: 260px;
            overflow-y: auto;
            border: 1px solid var(--tblr-border-color, rgba(120, 130, 155, .2));
            border-radius: 14px;
            margin-top: .6rem;
            background: var(--tblr-bg-surface, #fff);
        }

        .pos-product-item {
            border: 0;
            border-bottom: 2px dashed var(--tblr-border-color, rgba(120, 130, 155, .15));
            padding: .55rem .85rem;
            transition: background .15s ease;
        }

        .pos-product-item:last-child {
            border-bottom: 0;
        }

        .pos-product-item:hover {
            background: rgba(32, 107, 196, .07);
        }

        /* جدول سبد */
        .pos-cart-table thead th {
            background: rgba(32, 107, 196, .06);
            font-size: .8rem;
            font-weight: 700;
            color: var(--tblr-muted, #6c7a91);
            border-bottom: 0;
            white-space: nowrap;
        }

        .pos-cart-table td {
            vertical-align: middle;
        }

        .pos-qty-btn {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }

        .pos-qty-value {
            min-width: 42px;
            text-align: center;
            font-weight: 700;
        }

        .pos-price-input {
            width: 120px;
            border-radius: 10px;
            text-align: center;
        }

        /* جمع‌بندی */
        .pos-summary-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            font-size: .95rem;
        }

        .pos-grand-total {
            background: linear-gradient(135deg, rgba(32, 107, 196, .12), rgba(24, 145, 48, .1));
            border-radius: 20px;
            padding: .85rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pos-pay-btn {
            border-radius: 20px;
            font-weight: 900;
            padding: .8rem;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(24, 145, 48, .25);
        }

        .pos-cart-empty {
            padding: 2.5rem 1rem;
            color: var(--tblr-muted, #8a94a6);
        }

        .pos-cart-empty i {
            font-size: 20px;
            opacity: .35;
            display: block;
            margin-bottom: .5rem;
        }

        /* ============ مودال پرداخت ============ */
        .pos-modal .modal-content {
            border-radius: 20px;
            border: 0;
        }

        .pay-card {
            border: 2px solid var(--tblr-border-color, rgba(120, 130, 155, .22));
            border-radius: 16px;
            padding: .8rem .4rem;
            text-align: center;
            cursor: pointer;
            background: var(--tblr-bg-surface, #fff);
            transition: all .18s ease;
            user-select: none;
            width: 100%;
        }

        .pay-card:hover {
            transform: translateY(-2px);
            border-color: rgba(32, 107, 196, .45);
        }

        .pay-card .pay-icon {
            width: 55px;
            height: 55px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: .4rem;
        }

        .pay-card .pay-label {
            font-weight: 700;
            font-size: .85rem;
        }

        .pay-card.active {
            border-color: var(--pos-pay-color, #206bc4);
            background: color-mix(in srgb, var(--pos-pay-color, #206bc4) 10%, transparent);
            box-shadow: 0 6px 16px color-mix(in srgb, var(--pos-pay-color, #206bc4) 25%, transparent);
        }

        .pay-card.active .pay-label {
            color: var(--pos-pay-color, #206bc4);
        }

        .pay-card.disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .pay-cash   { --pos-pay-color: #189130; }
        .pay-card-2 { --pos-pay-color: #206bc4; }
        .pay-credit { --pos-pay-color: #d63d62; }
        .pay-mixed  { --pos-pay-color: #8b5cf6; }

        /* جستجوی مشتری */
        .cust-results {
            max-height: 190px;
            overflow-y: auto;
            /* border: 1px solid rgba(122, 248, 5, 0.966); */
            border-radius: 8px;
            margin-top: .35rem;
            /* background: #f8f6f6; */
        }

        .cust-item {
            padding: .5rem .8rem;
            cursor: pointer;
            /* border-bottom: 1px dashed rgba(8, 8, 8, 0.966); */
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            /* border-radius: 8px; */
        }

        .cust-item:last-child { 
            border-bottom: 0;
        }

        .cust-item:hover {
            /* background: rgb(236, 7, 167); */
        }

        .cust-chip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            /* border: 1px solid rgb(3, 116, 255); */
            /* background: rgb(32, 106, 196); */
            border-radius: 8px;
            padding: .5rem .8rem;
        }

        /* سوییچ روش پرداخت پیش‌پرداخت نسیه */
        .mini-pay-toggle {
            display: inline-flex;
            border: 1px solid rgba(120, 130, 155, .3);
            border-radius: 10px;
            overflow: hidden;
        }

        .mini-pay-toggle button {
            border: 0;
            background: transparent;
            padding: .35rem .7rem;
            font-size: .8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            color: #6c7a91;
        }

        .mini-pay-toggle button.active-cash {
            background: rgba(24, 145, 48, .15);
            color: #189130;
        }

        .mini-pay-toggle button.active-card {
            background: rgba(32, 107, 196, .15);
            color: #206bc4;
        }
    </style>

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

    {{-- Page Header --}}
    <div class="pos-card mb-3">
        <div class="pos-card-header">
            <h3 class="pos-title">
                <i class="bi bi-cart-check-fill text-primary"></i>
                فروش (صندوق)
            </h3>
            <small class="text-muted d-none d-sm-inline">سبد خرید مشتری و صدور فاکتور خرید</small>
        </div>
    </div>

    @error('checkout')
        <div class="alert alert-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i>{{ $message }}</div>
    @enderror

    <div class="row g-3">
        {{--=============== ستون جستجو و سبد خرید ===============--}}
        <div class="col-lg-7">

            {{-- جستجو و افزودن کالا --}}
            <div class="pos-card mb-3">
                <div class="pos-card-header">
                    <h5 class="pos-title text-primary">
                        <i class="bi bi-plus-circle-fill"></i>
                        افزودن کالا
                    </h5>
                    @if (count($cart))
                        <span class="badge bg-primary-lt rounded-pill">
                            {{ count($cart) }} قلم کالا در سبد
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="input-group pos-input-group">
                                <input type="text" class="form-control pos-scan-input"
                                    placeholder="اسکن یا وارد کردن بارکد…" wire:model="barcode"
                                    wire:keydown.enter="addByBarcode" autofocus>
                                <span class="input-group-text"><i class="bi bi-upc-scan text-primary"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group pos-input-group">
                                <input type="text" class="form-control pos-scan-input"
                                    wire:model.live.debounce.400ms="search" placeholder="جستجو نام یا بارکد کالا…">
                                <span class="input-group-text"><i class="bi bi-search text-info"></i></span>
                            </div>
                        </div>
                    </div>

                    @if ($search && $products->count())
                        <div class="pos-search-results list-group list-group-flush">
                            @foreach ($products as $product)
                                <button type="button"
                                    class="list-group-item list-group-item-action pos-product-item d-flex justify-content-between align-items-center"
                                    wire:click="addProduct({{ $product->id }})">
                                    <span>
                                        <i class="bi bi-box-seam text-muted ms-1"></i>
                                        {{ $product->name }}
                                        <small class="text-muted d-block" style="font-size:.72rem;">
                                            بارکد: {{ $product->barcode }} — موجودی: {{ $product->stock }}
                                        </small>
                                    </span>
                                    <span class="badge bg-success-lt rounded-pill text-success-emphasis">
                                        {{ number_format($product->sell_price) }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @elseif ($search)
                        <div class="text-muted small mt-2 text-center py-2">
                            <i class="bi bi-emoji-frown me-1"></i>کالایی یافت نشد.
                        </div>
                    @endif
                </div>
            </div>

            {{-- سبد فروش --}}
            <div class="pos-card">
                <div class="pos-card-header">
                    <h5 class="pos-title text-success mb-0">
                        <i class="bi bi-bag-fill"></i>
                        سبد خرید مشتری
                    </h5>
                    @if (count($cart))
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                            wire:click="clearCart" wire:confirm="آیا از پاک کردن کل سبد خرید مطمئن هستید؟">
                            <i class="bi bi-x-circle me-1"></i>خالی کردن سبد
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle pos-cart-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>کالا</th>
                                <th>قیمت (تومان)</th>
                                <th class="text-center">تعداد</th>
                                <th>جمع</th>
                                <th class="text-center">حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cart as $item)
                                <tr wire:key="cart-{{ $item['id'] }}">
                                    <td>
                                        <span class="fw-semibold">{{ $item['name'] }}</span>
                                        <small class="text-muted d-block" style="font-size:.72rem;">
                                            {{ $item['barcode'] }}
                                        </small>
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="any" class="form-control form-control-sm pos-price-input"
                                            value="{{ $item['price'] }}"
                                            wire:change="updatePrice({{ $item['id'] }}, $event.target.value)">
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button class="btn btn-sm btn-danger text-dark border pos-qty-btn" type="button"
                                                wire:click="decrementQty({{ $item['id'] }})">
                                                <i class="bi bi-dash-lg"></i>
                                            </button>
                                            <span class="pos-qty-value">{{ $item['quantity'] }}</span>
                                            <button class="btn btn-sm btn-success text-dark border pos-qty-btn" type="button"
                                                wire:click="incrementQty({{ $item['id'] }})">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                        @if ($item['quantity'] > $item['stock'])
                                            <div class="text-danger small mt-1">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                موجودی کافی نیست
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format($item['price'] * $item['quantity']) }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger text-dark border"
                                            wire:click="removeFromCart({{ $item['id'] }})" title="حذف کالا">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="pos-cart-empty text-center">
                                            <i class="bi bi-basket2"></i>
                                            سبد فروش خالی است.<br>
                                            <small>با بارکدخوان یا جستجو کالا اضافه کنید.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--=============== ستون جمع‌بندی و پرداخت ===============--}}
        <div class="col-lg-5">
            <div class="pos-card">
                <div class="pos-card-header">
                    <h5 class="pos-title text-primary mb-0">
                        <i class="bi bi-receipt-cutoff"></i>
                        جمع‌بندی فاکتور
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">تخفیف (تومان)</label>
                        <input type="number" min="0" step="any" class="form-control pos-scan-input"
                            wire:model.live.debounce.500ms="discount" placeholder="0">
                    </div>

                    <div class="pos-summary-line">
                        <span class="text-muted">جمع کل ({{ number_format(count($cart)) }} قلم)</span>
                        <strong>{{ number_format($this->subtotal) }}</strong>
                    </div>

                    @if ($discount > 0)
                        <div class="pos-summary-line text-success">
                            <span>تخفیف</span>
                            <strong>{{ number_format(min($discount, $this->subtotal)) }}</strong>
                        </div>
                    @endif

                    <hr class="my-2">

                    <div class="pos-grand-total mb-3">
                        <span class="fw-bold">مبلغ قابل پرداخت</span>
                        <strong class="fs-4 text-success">{{ number_format($this->finalPrice) }}
                            <small class="fw-normal">تومان</small>
                        </strong>
                    </div>

                    <button type="button" class="btn btn-success text-dark pos-pay-btn w-100"
                        wire:click="openCheckoutModal" @if (empty($cart)) disabled @endif>
                        <i class="bi bi-cash-coin me-4"></i>
                        پرداخت و ثبت فاکتور
                    </button>

                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-person-badge me-1"></i>
                            صندوقدار: {{ auth()->user()->name ?? '—' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{--================================== مودال پرداخت / تسویه =================================--}}
    @if ($showCheckoutModal)
        <div class="modal modal-blur fade show d-block pos-modal" tabindex="-1"
            style="background: rgba(15,23,42,.55);" wire:key="checkout-modal">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form wire:submit="checkout">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-1"></i>
                                تسویه و ثبت فاکتور فروش
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>

                        <div class="modal-body">

                            {{-- ============ انتخاب مشتری با جستجوی لایو ============ --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-person-fill me-1 text-primary"></i>مشتری
                                </label>

                                @if ($customerId)
                                    <div class="cust-chip">
                                        <span>
                                            <i class="bi bi-patch-check-fill text-success me-1"></i>
                                            <strong>{{ $customerName }}</strong>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-dark text-danger border"
                                            wire:click="clearCustomer" title="حذف مشتری">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="نام یا موبایل مشتری را تایپ کنید…"
                                            wire:model.live.debounce.300ms="customerQuery" autocomplete="off">
                                        <button type="button" class="btn btn-outline-secondary"
                                            wire:click="clearCustomer">
                                            مشتری متفرقه
                                        </button>
                                    </div>

                                    @if (trim($customerQuery) !== '')
                                        @if (count($this->customerResults))
                                            <div class="cust-results">
                                                @foreach ($this->customerResults as $customer)
                                                    <button type="button" class="cust-item list-group-item-action"
                                                        wire:click="selectCustomer({{ $customer['id'] }})">
                                                        <span class="fw-semibold">
                                                            <i class="bi bi-person-circle text-primary ms-1"></i>
                                                            {{ $customer['name'] }}
                                                        </span>
                                                        <small class="text-warning">{{ $customer['mobile'] }}</small>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-info small mt-1">
                                                <i class="bi bi-info-circle me-1"></i>مشتری‌ای با این مشخصات یافت نشد —
                                                با دکمه «مشتری متفرقه» بدون انتخاب مشتری ادامه دهید.
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>

                            {{-- ============ روش پرداخت ============ --}}
                            <label class="form-label fw-bold">
                                <i class="bi bi-wallet2 me-1 text-primary"></i>روش پرداخت
                                <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2 mb-1">
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card pay-cash {{ $paymentType === 'cash' ? 'active' : '' }}"
                                        wire:click="setPaymentType('cash')">
                                        <span class="pay-icon" style="background: rgba(24,145,48,.12); color:#189130;">
                                            <i class="bi bi-cash-stack"></i>
                                        </span>
                                        <div class="pay-label">نقدی</div>
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card pay-card-2 {{ $paymentType === 'card' ? 'active' : '' }}"
                                        wire:click="setPaymentType('card')">
                                        <span class="pay-icon" style="background: rgba(32,107,196,.12); color:#206bc4;">
                                            <i class="bi bi-credit-card-fill"></i>
                                        </span>
                                        <div class="pay-label">کارتخوان</div>
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card pay-credit {{ $paymentType === 'credit' ? 'active' : '' }} {{ !$customerId ? 'disabled' : '' }}"
                                        @if ($customerId) wire:click="setPaymentType('credit')" @endif
                                        title="{{ $customerId ? '' : 'نسیه فقط برای مشتری ثبت‌شده امکان‌پذیر است' }}">
                                        <span class="pay-icon" style="background: rgba(214,61,98,.12); color:#d63d62;">
                                            <i class="bi bi-clock-history"></i>
                                        </span>
                                        <div class="pay-label">نسیه</div>
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card pay-mixed {{ $paymentType === 'mixed' ? 'active' : '' }}"
                                        wire:click="setPaymentType('mixed')">
                                        <span class="pay-icon" style="background: rgba(139,92,246,.12); color:#8b5cf6;">
                                            <i class="bi bi-shuffle"></i>
                                        </span>
                                        <div class="pay-label">ترکیبی</div>
                                    </button>
                                </div>
                            </div>
                            <div class="text-muted small mb-3">
                                <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                                @switch($paymentType)
                                    @case('cash')
                                        دریافت کل یا بخشی بیش از مبلغ فاکتور به‌صورت نقدی (باقی محاسبه می‌شود).
                                    @break
                                    @case('card')
                                        پرداخت دقیقاً از طریق کارتخوان.
                                    @break
                                    @case('mixed')
                                        بخشی نقدی و بخشی با کارتخوان؛ مجموع باید برابر مبلغ فاکتور باشد.
                                    @break
                                    @case('credit')
                                        ثبت بدهی روی حساب مشتری؛ امکان پیش‌پرداخت نقدی یا کارتخوان وجود دارد.
                                    @break
                                @endswitch
                            </div>

                            @error('paymentType')
                                <div class="alert alert-warning py-2 small"><i
                                        class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}</div>
                            @enderror

                            {{-- ============ فیلدهای مبلغ بر اساس روش پرداخت ============ --}}
                            <div class="border rounded-4 p-3 mb-3"
                                style="border-color: var(--tblr-border-color, rgba(120,130,155,.2)) !important;">

                                @if ($paymentType === 'cash')
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-7">
                                            <label class="form-label fw-semibold">مبلغ نقدی دریافتی</label>
                                            <input type="number" min="0" step="any"
                                                class="form-control @error('paidAmount') is-invalid @enderror"
                                                wire:model.live.debounce.400ms="paidAmount">
                                            @error('paidAmount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-5">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill w-100"
                                                wire:click="$set('paidAmount', {{ $this->finalPrice }})">
                                                <i class="bi bi-magic me-1"></i>پرداخت دقیق ({{ number_format($this->finalPrice) }})
                                            </button>
                                        </div>
                                    </div>

                                    @if ($this->change > 0)
                                        <div class="alert alert-success mt-3 mb-0 d-flex justify-content-between py-2">
                                            <span><i class="bi bi-arrow-repeat me-1"></i>باقی وجه مشتری:</span>
                                            <strong>{{ number_format($this->change) }} تومان</strong>
                                        </div>
                                    @endif

                                @elseif ($paymentType === 'card')
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">
                                            <i class="bi bi-credit-card-fill text-primary me-1"></i>
                                            مبلغ قابل کشیدن از کارتخوان
                                        </span>
                                        <strong class="fs-5 text-primary">{{ number_format($this->finalPrice) }} تومان</strong>
                                    </div>

                                @elseif ($paymentType === 'mixed')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-cash-stack text-success me-1"></i>مبلغ نقدی
                                            </label>
                                            <input type="number" min="0" step="any"
                                                class="form-control @error('cashAmount') is-invalid @enderror"
                                                wire:model.live.debounce.400ms="cashAmount" placeholder="0">
                                            @error('cashAmount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-credit-card-fill text-primary me-1"></i>مبلغ کارتخوان
                                            </label>
                                            <input type="number" min="0" step="any"
                                                class="form-control @error('cardAmount') is-invalid @enderror"
                                                wire:model.live.debounce.400ms="cardAmount" placeholder="0">
                                            @error('cardAmount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    @if (!$errors->has('cardAmount'))
                                        <div class="mt-3 d-flex justify-content-between small">
                                            <span class="text-muted">مجموع وارد شده:
                                                <strong>{{ number_format($this->cashAmount + $this->cardAmount) }}</strong>
                                            </span>
                                            <span
                                                class="{{ abs($this->mixedDiff) < 0.001 ? 'text-success' : ($this->mixedDiff > 0 ? 'text-danger' : 'text-warning') }}">
                                                @if (abs($this->mixedDiff) < 0.001)
                                                    <i class="bi bi-check-circle-fill me-1"></i>تسویه کامل شد
                                                @elseif ($this->mixedDiff > 0)
                                                    <i class="bi bi-arrow-down-circle me-1"></i>باقی‌مانده:
                                                    {{ number_format($this->mixedDiff) }}
                                                @else
                                                    <i class="bi bi-arrow-up-circle me-1"></i>اضافه:
                                                    {{ number_format(abs($this->mixedDiff)) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    @if ($this->mixedDiff > 0.001)
                                        <button type="button" class="btn btn-sm btn-primary-lt rounded-pill mt-2"
                                            wire:click="$set('cardAmount', {{ $this->mixedDiff + $this->cardAmount }})">
                                            <i class="bi bi-magic me-1"></i>تکمیل خودکار با کارتخوان
                                        </button>
                                    @endif

                                @elseif ($paymentType === 'credit')
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">
                                                مبلغ پیش‌پرداخت <span class="text-muted fw-normal">(اختیاری)</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" min="0" step="any"
                                                    class="form-control @error('paidAmount') is-invalid @enderror"
                                                    wire:model.live.debounce.400ms="paidAmount" placeholder="0">
                                                <span
                                                    class="input-group-text p-0 border-0"
                                                    style="background: transparent;">
                                                    <span class="mini-pay-toggle h-100">
                                                        <button type="button"
                                                            class="{{ $creditPayMethod === 'cash' ? 'active-cash' : '' }}"
                                                            wire:click="$set('creditPayMethod', 'cash')"
                                                            title="پرداخت پیش‌پرداخت نقدی">
                                                            <i class="bi bi-cash-stack"></i> نقدی
                                                        </button>
                                                        <button type="button"
                                                            class="{{ $creditPayMethod === 'card' ? 'active-card' : '' }}"
                                                            wire:click="$set('creditPayMethod', 'card')"
                                                            title="پرداخت پیش‌پرداخت با کارتخوان">
                                                            <i class="bi bi-credit-card"></i> کارتخوان
                                                        </button>
                                                    </span>
                                                </span>
                                                @error('paidAmount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <span class="text-muted small d-block">ثبت نسیه روی حساب مشتری</span>
                                            <strong class="fs-5 text-danger">{{ number_format($this->creditRemain) }}</strong>
                                            <span class="small text-danger">تومان</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- خلاصه فاکتور --}}
                            <div class="alert alert-primary d-flex justify-content-between align-items-center mb-0">
                                <span class="fw-semibold">
                                    <i class="bi bi-receipt me-1"></i>مبلغ قابل پرداخت:
                                </span>
                                <strong class="fs-5">{{ number_format($this->finalPrice) }} تومان</strong>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">
                                <i class="bi bi-x-lg me-1"></i>انصراف
                            </button>
                            <button type="submit" class="btn btn-success text-white fw-bold px-4"
                                wire:loading.attr="disabled" wire:target="checkout">
                                <span wire:loading wire:target="checkout"
                                    class="spinner-border spinner-border-sm ms-1"></span>
                                <i class="bi bi-check2-circle me-1"></i>ثبت نهایی فاکتور
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{--=============================== مودال موفقیت + چاپ فاکتور ==============================--}}
    @if ($showInvoiceModal)
        <div class="modal modal-blur fade show d-block pos-modal" tabindex="-1"
            style="background: rgba(15,23,42,.55);" wire:key="invoice-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-header justify-content-center position-relative">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-check-circle-fill text-success fs-4 me-1"></i>
                            فاکتور با موفقیت ثبت شد
                        </h5>
                        <button type="button" class="btn-close position-absolute" style="left:1rem;top:1rem;"
                            wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        @if ($lastSale)
                            <div class="pos-summary-line">
                                <span class="text-muted">شماره فاکتور</span>
                                <strong>{{ $lastSale->invoice_number }}</strong>
                            </div>
                            <div class="pos-summary-line">
                                <span class="text-muted">مشتری</span>
                                <strong>{{ $lastSale->customer->full_name ?? 'متفرقه' }}</strong>
                            </div>
                            <div class="pos-summary-line">
                                <span class="text-muted">روش پرداخت</span>
                                <strong>
                                    @switch($lastSale->payment_type)
                                        @case('cash')نقدی
                                        @break
                                        @case('card')کارتخوان
                                        @break
                                        @case('mixed')ترکیبی
                                        @break
                                        @case('credit')نسیه
                                        @break
                                        @default{{ $lastSale->payment_type }}
                                    @endswitch
                                </strong>
                            </div>
                            <div class="pos-summary-line">
                                <span class="text-muted">مبلغ کل</span>
                                <strong class="text-primary">{{ number_format($lastSale->final_price) }} تومان</strong>
                            </div>
                            @if ($lastSale->payment_type === 'credit' && (float) $lastSale->paid_amount < (float) $lastSale->final_price)
                                <div class="pos-summary-line">
                                    <span class="text-muted">باقی‌مانده نسیه</span>
                                    <strong class="text-danger">
                                        {{ number_format((float) $lastSale->final_price - (float) $lastSale->paid_amount) }} تومان
                                    </strong>
                                </div>
                            @endif
                        @endif

                        <p class="text-muted mt-2 mb-3">در صورت نیاز فاکتور را برای مشتری چاپ کنید.</p>

                        <a href="{{ route('invoice', $lastSaleId) }}" target="_blank"
                            class="btn btn-primary btn-lg rounded-4 px-5">
                            <i class="bi bi-printer me-1"></i> چاپ فاکتور فروش
                        </a>
                    </div>
                    <div class="modal-footer justify-content-center border-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                            wire:click="closeModals">بستن و فروش بعدی</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{--============ باز کردن خودکار فاکتور در تب جدید هنگام کلیک روی دکمه چاپ ============--}}
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
