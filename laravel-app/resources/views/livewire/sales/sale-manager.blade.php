<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">


    @include('partials.flash-messages')

    {{-- Page Header --}}
    <div class="card mb-3">
        <div class="card-header">
            <div>
                <h2>
                    <i class="bi bi-cart-check-fill text-primary"></i>
                    صــنــدوق فــروش
                </h2>
            <small class="d-none d-sm-inline">سـبـد خـریـد مـشـتـری و صـدور فـاکـتـور خـریـد</small>
            </div>
        </div>
    </div>

    @error('checkout')
        <div class="alert alert-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i>{{ $message }}</div>
    @enderror

    <div class="row g-3">
        {{--=============== ستون جستجو و سبد خرید ===============--}}
        <div class="col-lg-7">

            {{-- جستجو و افزودن کالا --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3>
                        <i class="bi bi-plus-circle-fill"></i>
                        افـزودن کـالـا
                    </h3>
                    @if (count($cart))
                        <span class="badge bg-primary-lt rounded-pill">
                            {{ count($cart) }} قلم کالا در سبد
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                    placeholder="اسـکـن یـا وارد کـردن بـارکـد…" wire:model="barcode"
                                    wire:keydown.enter="addByBarcode" autofocus>
                                <span class="input-group-text"><i class="bi bi-upc-scan text-primary"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                    wire:model.live.debounce.400ms="search" placeholder="جـسـتـجـو نـام یـا بـارکـد کـالـا …">
                                <span class="input-group-text"><i class="bi bi-search text-info"></i></span>
                            </div>
                        </div>
                    </div>

                    @if ($search && $products->count())
                        <div class="list-group list-group-flush">
                            @foreach ($products as $product)
                                <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
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
                            <i class="bi bi-emoji-frown me-1"></i>کـالـایـی یـافـت نـشـد.
                        </div>
                    @endif
                </div>
            </div>

            {{-- سبد فروش --}}
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="bi bi-bag-fill"></i>
                        سبـد خـریـد مـشـتـری
                    </h3>
                    @if (count($cart))
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                            wire:click="clearCart" wire:confirm="آیا از پاک کردن کل سبد خرید مطمئن هستید؟">
                            <i class="bi bi-x-circle me-1"></i>خالی کردن سبد
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
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
                                        <input type="number" min="0" step="any" class="form-control form-control-sm"
                                            value="{{ $item['price'] }}"
                                            wire:change="updatePrice({{ $item['id'] }}, $event.target.value)">
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button class="btn btn-sm btn-outline-danger" type="button"
                                                wire:click="decrementQty({{ $item['id'] }})">
                                                <i class="bi bi-dash-lg"></i>
                                            </button>
                                            <span class="pos-qty-value">{{ $item['quantity'] }}</span>
                                            <button class="btn btn-sm btn-outline-success" type="button"
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
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="removeFromCart({{ $item['id'] }})" title="حذف کالا">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            <i class="bi bi-basket2"></i>
                                            سـبـد فـروش خـالـی اسـت.<br>
                                            <small>بـا بـارکـدخـوان یـا جـسـتـجـو کـالـا اضـافـه کـنـیـد.</small>
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
            <div class="card">
                <div class="card-header">
                    <h3>
                        <i class="bi bi-receipt-cutoff"></i>
                        جـمـع‌بـنـدی فـاکـتـور
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">تـخـفـیـف (تـومـان)</label>
                        <input type="number" min="0" step="any" class="form-control pos-scan-input"
                            wire:model.live.debounce.500ms="discount" placeholder="0">
                    </div>

                    <div class="">
                        <span class="text-muted">جـمـع کـل ({{ number_format(count($cart)) }} قـلـم)</span>
                        <strong>{{ number_format($this->subtotal) }}</strong>
                    </div>

                    @if ($discount > 0)
                        <div class="text-success">
                            <span>تـخـفـیـف</span>
                            <strong>{{ number_format(min($discount, $this->subtotal)) }}</strong>
                        </div>
                    @endif

                    <hr class="my-2">

                    <div class=" mb-3">
                        <span class="fw-bold">مـبـلـغ قـابـل پـرداخـت</span>
                        <strong class="fs-4 text-success">{{ number_format($this->finalPrice) }}
                            <small class="fw-normal">تـومـان</small>
                        </strong>
                    </div>

                    <button type="button" class="btn btn-success text-dark w-100"
                        wire:click="openCheckoutModal" @if (empty($cart)) disabled @endif>
                        <i class="bi bi-cash-coin me-4"></i>
                        پـرداخـت و ثـبـت فـاکـتـور
                    </button>

                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-person-badge me-1"></i>
                            صـنـدوقـدار: {{ auth()->user()->name ?? '—' }}
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
                                تـسـویـه و ثـبـت فـاکـتـور فـروش
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>

                        <div class="modal-body">

                            {{-- ============ انتخاب مشتری با جستجوی لایو ============ --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-person-fill me-1 text-primary"></i>مـشـتـری
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

                            {{-- ============ استفاده از امتیاز وفاداری ============ --}}
                            @if ($customerId && $customerAvailablePoints > 0 && setting('loyalty_enabled', '1') == '1')
                                <div class="mb-4 loyalty-box">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <span class="fw-bold"><i class="bi bi-gem text-fuchsia me-1"></i>امتیاز قابل استفاده:
                                                {{ number_format($customerAvailablePoints) }}</span>
                                            <div class="text-muted small">هـر امـتـیـاز = {{ number_format($pointValue) }} تومان تخفیف</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="number" min="0" max="{{ $customerAvailablePoints }}"
                                                class="form-control form-control-sm text-center" style="width:120px"
                                                wire:model.live.debounce.400ms="pointsToRedeem">
                                            <button type="button" class="btn btn-sm btn-fuchsia text-white rounded-pill"
                                                wire:click="applyAllPoints"
                                                title="حداکثر امتیاز مجاز برای این فاکتور">
                                                <i class="bi bi-stars me-1"></i>حـداکـثـر
                                            </button>
                                        </div>
                                    </div>
                                    @if ($pointsToRedeem > 0)
                                        <div class="alert alert-fuchsia mt-2 mb-0 py-2 small d-flex justify-content-between">
                                            <span><i class="bi bi-ticket-perforated me-1"></i>تـخـفـیـف امـتـیـازی:</span>
                                            <strong>{{ number_format($this->pointsDiscount) }} تـومـان</strong>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- ============ روش پرداخت ============ --}}
                            <label class="form-label fw-bold">
                                <i class="bi bi-wallet2 me-1 text-primary"></i>روش پـرداخـت
                                <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2 mb-1">
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card {{ $paymentType === 'cash' ? 'active' : '' }}"
                                        wire:click="setPaymentType('cash')">
                                        <span class="icon" style="background: rgba(24,145,48,.12); color:#189130;">
                                            <i class="bi bi-cash-stack"></i>
                                        </span>
                                        <div class="label">نـقـدی</div>
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card {{ $paymentType === 'card' ? 'active' : '' }}"
                                        wire:click="setPaymentType('card')">
                                        <span class="icon" style="background: rgba(32,107,196,.12); color:#206bc4;">
                                            <i class="bi bi-credit-card-fill"></i>
                                        </span>
                                        <div class="label">کـارتـخـوان</div>
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card {{ $paymentType === 'credit' ? 'active' : '' }} {{ !$customerId ? 'disabled' : '' }}"
                                        @if ($customerId) wire:click="setPaymentType('credit')" @endif
                                        title="{{ $customerId ? '' : 'نسیه فقط برای مشتری ثبت‌شده امکان‌پذیر است' }}">
                                        <span class="icon" style="background: rgba(214,61,98,.12); color:#d63d62;">
                                            <i class="bi bi-clock-history"></i>
                                        </span>
                                        <div class="label">نـسـیـه</div>
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button"
                                        class="pay-card {{ $paymentType === 'mixed' ? 'active' : '' }}"
                                        wire:click="setPaymentType('mixed')">
                                        <span class="icon" style="background: rgba(139,92,246,.12); color:#8b5cf6;">
                                            <i class="bi bi-shuffle"></i>
                                        </span>
                                        <div class="label">تـرکـیـبـی</div>
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
                                            <label class="form-label fw-semibold">مـبـلـغ نـقـدی دریـافـتـی</label>
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
                                                <i class="bi bi-magic me-1"></i>پـرداخـت دقـیـق ({{ number_format($this->finalPrice) }})
                                            </button>
                                        </div>
                                    </div>

                                    @if ($this->change > 0)
                                        <div class="alert alert-success mt-3 mb-0 d-flex justify-content-between py-2">
                                            <span><i class="bi bi-arrow-repeat me-1"></i>بـاقـی وجـه مـشـتـری:</span>
                                            <strong>{{ number_format($this->change) }} تـومـان</strong>
                                        </div>
                                    @endif

                                @elseif ($paymentType === 'card')
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">
                                            <i class="bi bi-credit-card-fill text-primary me-1"></i>
                                            مـبـلـغ قـابـل کـشـیـدن از کـارتـخـوان
                                        </span>
                                        <strong class="fs-5 text-primary">{{ number_format($this->finalPrice) }} تومان</strong>
                                    </div>

                                @elseif ($paymentType === 'mixed')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-cash-stack text-success me-1"></i>مـبـلـغ نـقـدی
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
                                                <i class="bi bi-credit-card-fill text-primary me-1"></i>مـبـلـغ کـارتـخـوان
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
                                            <span class="text-muted">مـجـمـوع وارد شـده:
                                                <strong>{{ number_format($this->cashAmount + $this->cardAmount) }}</strong>
                                            </span>
                                            <span
                                                class="{{ abs($this->mixedDiff) < 0.001 ? 'text-success' : ($this->mixedDiff > 0 ? 'text-danger' : 'text-warning') }}">
                                                @if (abs($this->mixedDiff) < 0.001)
                                                    <i class="bi bi-check-circle-fill me-1"></i>تـسـویـه کـامـل شـد
                                                @elseif ($this->mixedDiff > 0)
                                                    <i class="bi bi-arrow-down-circle me-1"></i>بـاقـی‌مـانـده:
                                                    {{ number_format($this->mixedDiff) }}
                                                @else
                                                    <i class="bi bi-arrow-up-circle me-1"></i>اضـافـه:
                                                    {{ number_format(abs($this->mixedDiff)) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    @if ($this->mixedDiff > 0.001)
                                        <button type="button" class="btn btn-sm btn-primary-lt rounded-pill mt-2"
                                            wire:click="$set('cardAmount', {{ $this->mixedDiff + $this->cardAmount }})">
                                            <i class="bi bi-magic me-1"></i>تـکـمـیـل خـودکـار بـا کـارتـخـوان
                                        </button>
                                    @endif

                                @elseif ($paymentType === 'credit')
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">
                                                مـبـلـغ پـیـش‌پـرداخـت <span class="text-muted fw-normal">(اخـتـیـاری)</span>
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
