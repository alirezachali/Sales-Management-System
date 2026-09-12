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

{{--=================== کارت‌های آماری ===================--}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد مشتریان بدهکار</div>
                    <div class="h1 mb-0 text-danger">{{ number_format($debtorCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">جمع کل مبلغ بدهی مشتریان</div>
                    <div class="h1 mb-0 text-warning">{{ number_format($totalDebt) }}
                        <small>{{ setting('currency', '') }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">نسیه‌ی فروش امروز</div>
                    <div class="h1 mb-0 text-info">{{ number_format($todayCredit) }}
                        <small>{{ setting('currency', '') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{--=================== فیلترها ===================--}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-10">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو نام یا موبایل...">
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

{{--============================ جدول مشتریان بدهکار ============================--}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-person-exclamation text-danger"></i>
                    مشتریان بدهکار
                </h3>
                <small class="text-muted">لیست مشتریانی که حساب نسیه‌ی آن‌ها تسویه نشده است</small>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('customers.index') }}">
                    <button class="btn btn-info text-dark" title="بازگشت به باشگاه مشتریان">
                        <i class="bi bi-arrow-right"></i>
                        بازگشت
                    </button>
                </a>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">ردیف</th>
                        <th>نام مشتری</th>
                        <th width="140">شماره موبایل</th>
                        <th width="150">رده باشگاه</th>
                        <th width="160">مبلغ کل بدهی</th>
                        <th width="190">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($debtors as $debtor)
                        <tr wire:key="debtor-{{ $debtor->id }}">
                            <td>{{ $loop->iteration + ($debtors->currentPage() - 1) * $debtors->perPage() }}</td>
                            <td>{{ $debtor->full_name }}</td>
                            <td>{{ $debtor->mobile }}</td>
                            <td>
                                @if ($debtor->role)
                                    <span class="badge bg-{{ $debtor->role->color }} text-dark">
                                        <i class="bi {{ $debtor->role->icon }}"></i>
                                        {{ $debtor->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-dark">بدون رده</span>
                                @endif
                            </td>
                            <td class="text-danger fw-bold">
                                {{ number_format($debtor->debt_balance) }} {{ setting('currency', '') }}
                            </td>
                            <td>
                                @can('customers.balance')
                                <button type="button" class="btn btn-sm btn-success text-dark"
                                    wire:click="openPayModal({{ $debtor->id }})" title="ثبت پرداخت بدهی">
                                    <i class="bi bi-cash-coin"></i>
                                    پرداخت
                                </button>
                                @endcan
                                <button type="button" class="btn btn-sm btn-info text-dark"
                                    wire:click="openHistory({{ $debtor->id }})"
                                    title="مشاهده سوابق خرید و بدهی و پرداخت">
                                    <i class="bi bi-clock-history"></i>
                                    سوابق
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-emoji-smile fs-3 d-block mb-2"></i>
                                هیچ مشتری بدهکاری وجود ندارد. همه‌ی حساب‌ها تسویه است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $debtors->links() }}</div>
        </div>
    </div>

{{-- =============================== مودال ثبت پرداخت بدهی =============================== --}}
    @if ($showPayModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="debtor-pay-modal">
            <div class="modal-dialog modal-dialog-centered">
                <form wire:submit="pay">
                    <div class="modal-content">

                        <div class="modal-header bg-success text-dark">
                            <h5 class="modal-title">
                                <i class="bi bi-cash-coin"></i>
                                دریافت پرداخت از:
                                {{ $payingCustomer?->full_name }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="alert alert-warning">
                                مبلغ کل بدهی فعلی:
                                <strong>{{ number_format($payingBalance ?? 0) }}
                                    {{ setting('currency', '') }}</strong>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">روش پرداخت</label>
                                <select wire:model.live="pay_method" class="form-select">
                                    <option value="cash">نقدی</option>
                                    <option value="card">کارتخوان</option>
                                    <option value="mixed">ترکیبی (نقدی + کارتخوان)</option>
                                </select>
                            </div>

                            @if ($pay_method === 'mixed')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">مبلغ نقدی</label>
                                        <input type="number" min="1" wire:model="pay_cash"
                                            class="form-control @error('pay_cash') is-invalid @enderror"
                                            placeholder="مبلغ نقدی">
                                        @error('pay_cash')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">مبلغ کارتخوان</label>
                                        <input type="number" min="1" wire:model="pay_card"
                                            class="form-control @error('pay_card') is-invalid @enderror"
                                            placeholder="مبلغ کارتخوان">
                                        @error('pay_card')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label class="form-label">مبلغ پرداختی</label>
                                    <input type="number" min="1" wire:model="pay_amount"
                                        class="form-control @error('pay_amount') is-invalid @enderror"
                                        placeholder="مبلغ پرداختی">
                                    @error('pay_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals"
                                title="انصراف">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-success text-dark" wire:loading.attr="disabled"
                                wire:target="pay">
                                <span wire:loading wire:target="pay" class="spinner-border spinner-border-sm"></span>
                                ثبت پرداخت
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

{{-- =============================== مودال سوابق مشتری =============================== --}}
    @if ($showHistoryModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="debtor-history-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-info text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-clock-history"></i>
                            سوابق خرید و بدهی و پرداخت:
                            {{ $historyCustomer?->full_name }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">

                        @if ($historyCustomer)
                            <div class="alert {{ $historyBalance > 0 ? 'alert-warning' : 'alert-success' }}">
                                مانده حساب فعلی:
                                <strong>{{ number_format($historyBalance) }} {{ setting('currency', '') }}</strong>
                                @if ($historyBalance > 0)
                                    (بدهکار)
                                @else
                                    (تسویه)
                                @endif
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>تاریخ</th>
                                            <th>نوع</th>
                                            <th>مبلغ</th>
                                            <th>توضیحات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($historyTransactions as $transaction)
                                            <tr wire:key="history-{{ $transaction->id }}">
                                                <td>{{ jalaliDateTime($transaction->created_at) }}</td>
                                                <td>
                                                    @switch($transaction->type)
                                                        @case('sale')
                                                            <span class="badge bg-danger">خرید نسیه (بدهکار)</span>
                                                        @break

                                                        @case('payment')
                                                            <span class="badge bg-success">پرداخت</span>
                                                        @break

                                                        @case('refund')
                                                            <span class="badge bg-info">استرداد</span>
                                                        @break

                                                        @case('adjustment')
                                                            <span class="badge bg-warning">اصلاح حساب</span>
                                                        @break

                                                        @default
                                                            {{ $transaction->type }}
                                                    @endswitch
                                                </td>
                                                <td>{{ number_format($transaction->amount) }}</td>
                                                <td>{{ $transaction->description }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-3 text-muted">
                                                    هیچ سابقه‌ای برای این مشتری ثبت نشده است.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
