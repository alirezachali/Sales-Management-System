<div dir="rtl">

    @include('partials.flash-messages')

{{--=================== فیلترها ===================--}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو نام یا موبایل...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="sortBy" class="form-select">
                        <option value="amount">مرتب‌سازی: مبلغ کل خرید</option>
                        <option value="count">مرتب‌سازی: تعداد خرید</option>
                        <option value="date">مرتب‌سازی: تاریخ آخرین خرید</option>
                        <option value="name">مرتب‌سازی: نام مشتری</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100"
                        title="پاک کردن فیلترهای جستجو">
                        پاک کردن فیلترها
                    </button>
                </div>
            </div>
        </div>
    </div>

{{--============================ جدول خرید مشتریان ============================--}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-bag-check text-primary"></i>
                    خریدهای مشتریان
                </h3>
                <small class="text-muted">لیست مشتریانی که حداقل یک بار خرید کرده‌اند</small>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">ردیف</th>
                        <th>نام مشتری</th>
                        <th width="140">شماره موبایل</th>
                        <th width="120">تعداد کل خرید</th>
                        <th width="180">مبلغ کل خرید</th>
                        <th width="150">تاریخ آخرین خرید</th>
                        <th width="160">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($buyers as $buyer)
                        <tr wire:key="buyer-{{ $buyer->id }}">
                            <td>{{ $loop->iteration + ($buyers->currentPage() - 1) * $buyers->perPage() }}</td>
                            <td>{{ $buyer->full_name }}</td>
                            <td>{{ $buyer->mobile }}</td>
                            <td>
                                <span class="badge bg-blue text-dark">{{ number_format($buyer->total_purchases ?? 0) }}</span>
                            </td>
                            <td class="fw-bold text-primary">
                                {{ number_format($buyer->total_purchases_amount ?? 0) }} {{ setting('currency', '') }}
                            </td>
                            <td>{{ $buyer->last_purchase_date ? jalaliDate($buyer->last_purchase_date) : '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info text-dark"
                                    wire:click="openPurchases({{ $buyer->id }})"
                                    title="مشاهده لیست خریدهای این مشتری">
                                    <i class="bi bi-receipt"></i>
                                    خریدها
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                                هیچ مشتری‌ای با سابقه‌ی خرید یافت نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $buyers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

{{-- =============================== مودال لیست خریدهای مشتری =============================== --}}
    @if ($showPurchasesModal && $purchasesCustomer)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="customer-purchases-modal">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-bag-check"></i>
                            لیست خریدهای:
                            {{ $purchasesCustomer->full_name }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">

                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="50">ردیف</th>
                                        <th>شماره فاکتور</th>
                                        <th>تاریخ خرید</th>
                                        <th>تعداد کالاها</th>
                                        <th>مبلغ فاکتور</th>
                                        <th>وضعیت</th>
                                        <th width="210">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($customerInvoices as $invoice)
                                        <tr wire:key="customer-invoice-{{ $invoice->id }}">
                                            <td>{{ $loop->iteration + ($customerInvoices->currentPage() - 1) * $customerInvoices->perPage() }}</td>
                                            <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                                            <td>{{ jalaliDateTime($invoice->created_at) }}</td>
                                            <td>{{ number_format((float) ($invoice->items_sum_quantity ?? 0), 0) }}</td>
                                            <td class="fw-bold text-primary">
                                                {{ number_format($invoice->final_price) }} {{ setting('currency', '') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $invoice->statusColor() }}">
                                                    {{ $invoice->statusText() }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    wire:click="openInvoice({{ $invoice->id }})"
                                                    title="مشاهده جزئیات فاکتور">
                                                    <i class="bi bi-eye"></i>
                                                    مشاهده
                                                </button>
                                                @can('sales.view')
                                                    <a href="{{ route('invoice', $invoice->id) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        title="چاپ مجدد فاکتور">
                                                        <i class="bi bi-printer"></i>
                                                        چاپ مجدد
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">
                                                هیچ فاکتور خریدی برای این مشتری ثبت نشده است.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2">{{ $customerInvoices->links() }}</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

{{-- =============================== مودال مشاهده جزئیات فاکتور =============================== --}}
    @if ($showInvoiceModal && $invoiceSale)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.6);"
            wire:key="invoice-details-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt-cutoff"></i>
                            جزئیات فاکتور شماره {{ $invoiceSale->invoice_number }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeInvoiceModal"
                            title="بستن"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row g-2 mb-3">
                            <div class="col-sm-6 col-md-3">
                                <div class="border rounded p-2 text-center">
                                    <div class="subheader">تاریخ خرید</div>
                                    <strong>{{ jalaliDateTime($invoiceSale->created_at) }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="border rounded p-2 text-center">
                                    <div class="subheader">مشتری</div>
                                    <strong>{{ $invoiceSale->customer?->full_name ?? 'متفرقه' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="border rounded p-2 text-center">
                                    <div class="subheader">روش پرداخت</div>
                                    <strong>
                                        @switch($invoiceSale->payment_type)
                                            @case('cash')نقدی
                                            @break
                                            @case('card')کارتخوان
                                            @break
                                            @case('mixed')ترکیبی
                                            @break
                                            @case('credit')نسیه
                                            @break
                                            @default{{ $invoiceSale->payment_type }}
                                        @endswitch
                                    </strong>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="border rounded p-2 text-center">
                                    <div class="subheader">وضعیت</div>
                                    <span class="badge bg-{{ $invoiceSale->statusColor() }} mt-1">
                                        {{ $invoiceSale->statusText() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th width="50">ردیف</th>
                                        <th>نام کالا</th>
                                        <th>بارکد</th>
                                        <th>تعداد</th>
                                        <th>فی</th>
                                        <th>مبلغ خط</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoiceSale->items as $item)
                                        <tr wire:key="invoice-item-{{ $item->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->product?->name ?? '-' }}</td>
                                            <td>{{ $item->product?->barcode ?? '-' }}</td>
                                            <td>{{ number_format((float) $item->quantity, 0) }}</td>
                                            <td>{{ number_format($item->unit_price) }}</td>
                                            <td class="fw-bold">{{ number_format($item->line_total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-start">مبلغ کل</th>
                                        <th>{{ number_format($invoiceSale->total_price) }}</th>
                                    </tr>
                                    @if ((float) $invoiceSale->discount > 0)
                                        <tr>
                                            <th colspan="5" class="text-start text-danger">تخفیف</th>
                                            <th class="text-danger">{{ number_format($invoiceSale->discount) }}</th>
                                        </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th colspan="5" class="text-start">مبلغ قابل پرداخت</th>
                                        <th>{{ number_format($invoiceSale->final_price) }} {{ setting('currency', '') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="text-muted small">
                            <i class="bi bi-person-badge"></i>
                            صادرکننده: {{ $invoiceSale->user?->name ?? '-' }}
                        </div>

                    </div>
                    <div class="modal-footer">
                        @can('sales.view')
                            <a href="{{ route('invoice', $invoiceSale->id) }}" target="_blank"
                                class="btn btn-primary">
                                <i class="bi bi-printer me-1"></i> چاپ فاکتور
                            </a>
                        @endcan
                        <button type="button" class="btn btn-secondary" wire:click="closeInvoiceModal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
