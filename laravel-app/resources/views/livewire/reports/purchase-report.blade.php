<div dir="rtl">

{{--=================== نمایش پیغام‌های موفقیت ===================--}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

{{--=================== نمایش پیغام‌های خطا ===================--}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

{{--============ کارت‌های آماری ============--}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد فاکتورهای خرید</div>
                    <div class="h1 mb-0">
                        {{ $totals->invoice_count }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد عملیات های ورود کالا</div>
                    <div class="h1 mb-0 text-success">
                        {{-- {{ $totals->entry_count }} --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد عملیات های خروج کالا</div>
                    <div class="h1 mb-0 text-danger">
                        {{-- {{ $totals->exit_count }} --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد عملیات های موجودی اولیه</div>
                    <div class="h1 mb-0 text-info">
                        {{-- {{ $totals->initial_count }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

{{--================== فیلترها ==================--}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">از تاریخ</label>
                    <input type="text" wire:model.live.debounce.500ms="dateFromJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/01"
                        class="form-control @if (isset($dateErrors['from'])) is-invalid @endif">
                    @if (isset($dateErrors['from']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['from'] }}</div>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">تا تاریخ</label>
                    <input type="text" wire:model.live.debounce.500ms="dateToJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/31"
                        class="form-control @if (isset($dateErrors['to'])) is-invalid @endif">
                    @if (isset($dateErrors['to']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['to'] }}</div>
                    @endif
                </div>
                {{-- <div class="col-md-3">
                    <label class="form-label">نوع عملیات</label>
                    <select wire:model.live="filterType" class="form-select">
                        <option value="">همه</option>
                        <option value="initial">موجودی اولیه</option>
                        <option value="purchase">ورود کالا</option>
                        <option value="sale">خروج کالا</option>
                    </select>
                </div> --}}
                <div class="col-md-3">
                    <label class="form-label">روش پرداخت</label>
                    <select wire:model.live="filterPaymentMethod" class="form-select">
                        <option value="">همه روش‌ها</option>
                        @foreach ($paymentMethodLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100"
                        title="پاک کردن فیلترها">
                        پاک کردن فیلترها
                    </button>
                </div>
            </div>
            @if (isset($dateErrors['range']))
                <div class="alert alert-warning mt-2 mb-0 py-2">{{ $dateErrors['range'] }}</div>
            @endif
        </div>
    </div>

{{--======================== جدول گزارش ========================--}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-clipboard-data text-primary"></i>
                    گزارش فاکتورهای خرید
                </h3>
                <small class="">مشاهده گزارش لیست فاکتورهای خرید ثبت شده در بازه زمانی انتخابی</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-success text-dark" wire:click="exportExcel" wire:loading.attr="disabled"
                    wire:target="exportExcel" title="خروجی اکسل">
                    <span wire:loading wire:target="exportExcel" class="spinner-border spinner-border-sm me-1"></span>
                    <i class="bi bi-file-earmark-excel"></i>
                    خروجی اکسل
                </button>
                <button type="button" class="btn btn-info text-dark" wire:click="exportCsv"
                    wire:loading.attr="disabled" wire:target="exportCsv" title="خروجی CSV">
                    <span wire:loading wire:target="exportCsv" class="spinner-border spinner-border-sm me-1"></span>
                    <i class="bi bi-filetype-csv"></i>
                    خروجی CSV
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">ردیف</th>
                            <th width="80">شماره فاکتور</th>
                            <th width="80">روش پرداخت</th>
                            <th>تامین کننده</th>
                            <th width="120">تاریخ ثبت</th>
                            <th width="150">مبلغ</th>
                            <th width="120">ثبت کننده</th>
                            <th width="70">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr wire:key="{{ $record['id'] }}">
                                <td>{{ $loop->iteration + ($records->currentPage() - 1) * $records->perPage() }}</td>
                                {{--شماره فاکتور--}}
                                <td>{{ $record['invoice_number'] }}</td>
                                {{--روش پرداخت--}}
                                <td>
                                    @if ($record['payment_method'] === 'credit')
                                        <span class="badge bg-danger text-dark">
                                            <i class="bi bi-box-arrow-in-down me-1"></i>
                                            نسیه
                                        </span>
                                    @elseif ($record['payment_method'] === 'cash')
                                        <span class="badge bg-success text-dark">
                                            <i class="bi bi-box-arrow-up me-1"></i>
                                            نقد
                                        </span>
                                    @elseif ($record['payment_method'] === 'transfer')
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-box-arrow-up me-1"></i>
                                            حواله
                                        </span>
                                    @elseif ($record['payment_method'] === 'card')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-box-arrow-up me-1"></i>
                                            کارت
                                        </span>
                                    @endif
                                </td>
                                {{--تامین کننده--}}
                                <td class="fw-bold">{{ $record['supplier'] }}</td>
                                {{--تاریخ ثبت--}}
                                <td>
                                    {{ jalaliDate($record['date'] ) }}
                                </td>
                                {{--مبلغ--}}
                                <td>{{ $record['total_amount'] }}</td>
                                {{--ثبت کننده--}}
                                <td>
                                    @if ($record['user_name'])
                                        <span class="badge bg-secondary text-dark">
                                            <i class="bi bi-person me-1"></i>{{ $record['user_name'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">نامشخص</span>
                                    @endif
                                </td>
                                {{--عملیات--}}
                                <td>
                                    <button type="button" class="btn btn-sm btn-info text-dark"
                                        wire:click="openDetails({{ $record['id'] }})" title="مشاهده جزئیات">
                                        جزئیات
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    در بازه انتخاب‌شده عملیات خریدی یافت نشد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex flex-wrap justify-content-between align-items-center">
                <div>{{ $records->links() }}</div>
            </div>
        </div>
    </div>

{{--======================== مودال جزئیات فاکتور خرید ========================--}}
    @if ($showDetailsModal && $detailsInvoice)
        @php
            $itemCount = $detailsInvoice->items->count();
            $totalQty = $detailsInvoice->items->sum(fn ($item) => (float) $item->quantity);
            $invoiceTotal = (float) $detailsInvoice->total_amount;
            $paidAmount = (float) ($detailsInvoice->paid_amount ?? 0);
            $remainAmount = $invoiceTotal - $paidAmount;
            $statusLabels = [
                'completed' => 'تکمیل شده',
                'pending' => 'در انتظار',
                'cancelled' => 'لغو شده',
            ];
        @endphp
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="purchase-invoice-details-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt"></i>
                            جزئیات فاکتور خرید
                            @if ($detailsInvoice->invoice_number)
                                <span class="fw-normal">#{{ $detailsInvoice->invoice_number }}</span>
                            @endif
                        </h5> 
                        <button type="button" class="btn-close" wire:click="closeDetails" title="بستن"></button>
                    </div>
                    <div class="modal-body" id="purchase-invoice-print-area">
                        <h5 class="fw-bold mb-3">اطلاعات فاکتور</h5>
                        <table class="table table-bordered table-sm mb-4">
                            <tbody>
                                <tr>
                                    <th class="w-25 text-primary">شماره فاکتور</th>
                                    <td>{{ $detailsInvoice->invoice_number ?: '—' }}</td>
                                    <th class="w-25 text-primary">تاریخ خرید</th>
                                    <td>{{ jalaliDate($detailsInvoice->purchase_date) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-primary">تامین‌کننده</th>
                                    <td>{{ $detailsInvoice->supplier?->name ?? '—' }}</td>
                                    <th class="text-primary">روش پرداخت</th>
                                    <td>{{ $paymentMethodLabels[$detailsInvoice->payment_method] ?? $detailsInvoice->payment_method }}</td>
                                </tr>
                                <tr>
                                    <th class="text-primary">وضعیت</th>
                                    <td>{{ $statusLabels[$detailsInvoice->status] ?? ($detailsInvoice->status ?: '—') }}</td>
                                    <th class="text-primary">ثبت‌کننده</th>
                                    <td>{{ $detailsInvoice->user?->name ?? 'نامشخص' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-primary">مبلغ کل</th>
                                    <td class="fw-bold">{{ number_format($invoiceTotal) }}</td>
                                    <th class="text-primary">پرداخت‌شده</th>
                                    <td>{{ number_format($paidAmount) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-primary">مانده</th>
                                    <td>{{ number_format($remainAmount) }}</td>
                                    <th class="text-primary">توضیحات</th>
                                    <td>{{ $detailsInvoice->notes ?: '—' }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <h6 class="fw-bold mb-3">اقلام خرید</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">ردیف</th>
                                        <th>کالا</th>
                                        <th width="90">تعداد</th>
                                        <th width="120">قیمت خرید</th>
                                        <th width="120">قیمت فروش</th>
                                        <th width="130">جمع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($detailsInvoice->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->product?->name ?? '—' }}</td>
                                            <td>{{ rtrim(rtrim(number_format((float) $item->quantity, 3, '.', ''), '0'), '.') }}</td>
                                            <td>{{ number_format((float) $item->buy_price) }}</td>
                                            <td>{{ number_format((float) $item->sell_price) }}</td>
                                            <td class="fw-bold">{{ number_format((float) $item->total) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">
                                                آیتمی برای این فاکتور ثبت نشده است.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-2 mt-2">
                            <div class="col-md-4">
                                <div class="border rounded p-2">
                                    <div class="text-muted small">تعداد اقلام کالا</div>
                                    <div class="fw-bold fs-5">{{ $itemCount }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-2">
                                    <div class="text-muted small">تعداد کل کالا</div>
                                    <div class="fw-bold fs-5">{{ rtrim(rtrim(number_format($totalQty, 3, '.', ''), '0'), '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-2">
                                    <div class="text-muted small">مبلغ خرید کل فاکتور</div>
                                    <div class="fw-bold fs-5">{{ number_format($invoiceTotal) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer no-print">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetails">بستن</button>
                        <button type="button" class="btn btn-sm btn-light text-dark"
                                onclick="printPurchaseInvoiceDetails()" title="پرینت">
                            <i class="bi bi-printer"></i>
                            پرینت
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function printPurchaseInvoiceDetails() {
                const area = document.getElementById('purchase-invoice-print-area');
                if (!area) {
                    return;
                }

                const win = window.open('', '_blank', 'width=900,height=700');
                if (!win) {
                    return;
                }

                win.document.write(`<!DOCTYPE html>
                    <html lang="fa" dir="rtl">
                        <head>
                            <meta charset="UTF-8">
                            <title>جزئیات فاکتور خرید</title>
                            <style>
                                body {
                                    font-family: Tahoma, Vazirmatn, sans-serif;
                                    padding: 16px;
                                    color: #111;
                                    background: #fff;
                                }
                                h6 { margin: 0 0 12px; }
                                table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
                                th, td { border: 1px solid #333; padding: 6px 8px; text-align: right; font-size: 13px; }
                                th { background: #f3f3f3; }
                                .row { display: flex; gap: 8px; }
                                .col-md-4 { flex: 1; }
                                .border { border: 1px solid #333; border-radius: 6px; padding: 8px; }
                                .text-muted { color: #555; font-size: 12px; }
                                .fw-bold { font-weight: bold; }
                                .fs-5 { font-size: 18px; }
                                .table-responsive { overflow: visible; }
                                @page { margin: 12mm; }
                            </style>
                        </head>
                        <body>
                            <h3 style="margin-top:0;">جزئیات فاکتور خرید</h3>
                            ${area.innerHTML}
                        </body>
                    </html>`);
                win.document.close();
                win.focus();
                setTimeout(function () {
                    win.print();
                    win.close();
                }, 250);
            }
        </script>
    @endif
</div>


