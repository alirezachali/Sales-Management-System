<div dir="rtl">

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

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد کل عملیات خرید</div>
                    <div class="h1 mb-0">{{ number_format($totals->count ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">فاکتورهای خرید</div>
                    <div class="h1 mb-0 text-primary">{{ number_format($totals->invoice_count ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">ورود/خروج دستی انبار</div>
                    <div class="h1 mb-0 text-warning">{{ number_format($totals->movement_count ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">جمع مبالغ فاکتورها</div>
                    <div class="h1 mb-0 text-success">
                        {{ number_format($totals->invoice_total ?? 0) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">از تاریخ (شمسی)</label>
                    <input type="text" wire:model.live.debounce.500ms="dateFromJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/01"
                        class="form-control @if (isset($dateErrors['from'])) is-invalid @endif">
                    @if (isset($dateErrors['from']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['from'] }}</div>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">تا تاریخ (شمسی)</label>
                    <input type="text" wire:model.live.debounce.500ms="dateToJalali" data-jdp
                        autocomplete="off" inputmode="numeric" placeholder="1405/06/31"
                        class="form-control @if (isset($dateErrors['to'])) is-invalid @endif">
                    @if (isset($dateErrors['to']))
                        <div class="invalid-feedback d-block">{{ $dateErrors['to'] }}</div>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع عملیات</label>
                    <select wire:model.live="filterType" class="form-select">
                        <option value="">همه</option>
                        <option value="initial">موجودی اولیه</option>
                        <option value="purchase">ورود کالا</option>
                        <option value="sale">خروج کالا</option>
                    </select>
                </div>
                {{-- <div class="col-md-2">
                    <label class="form-label">روش پرداخت</label>
                    <select wire:model.live="filterPaymentMethod" class="form-select">
                        <option value="">همه روش‌ها</option>
                        @foreach ($paymentMethodLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div> --}}
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

    {{-- جدول گزارش خرید --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-clipboard-data text-primary"></i>
                    گزارش خرید و ورود/خروج انبار
                </h3>
                <small class="text-muted">مشاهده فاکتورهای خرید و عملیات ورود/خروج کالا در بازه زمانی انتخابی</small>
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
                            <th width="80">نوع</th>
                            <th>شرح</th>
                            <th width="80">نام محصول</th>
                            <th width="120">تاریخ</th>
                            <th width="50">تعداد</th>
                            {{-- <th width="70">مبلغ کل</th> --}}
                            {{-- <th width="70">پرداخت</th> --}}
                            <th width="120">توسط</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr wire:key="{{ $record['type'] }}-{{ $record['id'] }}">
                                <td>{{ $loop->iteration + ($records->currentPage() - 1) * $records->perPage() }}</td>
                                <td>
                                    @if ($record['type'] === 'purchase')
                                        <span class="badge bg-success text-dark">
                                            <i class="bi bi-box-arrow-in-down me-1"></i>ورود کالا
                                        </span>
                                    @elseif ($record['type'] === 'sale')
                                        <span class="badge bg-danger text-dark">
                                            <i class="bi bi-box-arrow-up me-1"></i>خروج کالا
                                        </span>
                                    @elseif ($record['type'] === 'initial')
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-box-arrow-up me-1"></i>موجودی اولیه
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $record['description'] }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $record['related_name'] }}
                                    </span>
                                </td>
                                <td>{{ jalaliDate($record['date'] ) }}</td>
                                {{-- <td>{{ $record['date'] }}</td> --}}
                                <td class="text-center">
                                    @if ($record['type'] === 'sale')
                                        <span class="text-danger">{{ number_format($record['quantity']) }}-</span>
                                    @elseif ($record['type'] === 'purchase')
                                        <span class="text-success">{{ number_format($record['quantity']) }}+</span>
                                    @elseif ($record['type'] === 'initial')
                                        <span class="text-info">{{ number_format($record['quantity']) }}+</span>
                                    @endif
                                </td>
                                {{-- <td>
                                    @if ($record['total_amount'] !== null)
                                        {{ number_format($record['total_amount']) }} {{ setting('currency', '') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td> --}}
                                {{-- <td>
                                    @if ($record['payment_method'] !== null)
                                        @switch($record['payment_method'])
                                            @case('نقدی')
                                                <span class="badge bg-success text-dark">نقدی</span>
                                            @break
                                            @case('کارت')
                                                <span class="badge bg-primary text-dark">کارت</span>
                                            @break
                                            @case('کارت به کارت / حواله')
                                                <span class="badge bg-info text-dark">کارت به کارت</span>
                                            @break
                                            @case('نسیه')
                                                <span class="badge bg-warning text-dark">نسیه</span>
                                            @break
                                            @default
                                                <span class="badge bg-secondary text-dark">{{ $record['payment_method'] }}</span>
                                            @break
                                        @endswitch
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td> --}}
                                <td>
                                    @if ($record['user_name'])
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-person me-1"></i>{{ $record['user_name'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
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
</div>
