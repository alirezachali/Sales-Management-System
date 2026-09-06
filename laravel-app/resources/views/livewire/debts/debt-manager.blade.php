<div>
{{--============ پیام موفقیت ============--}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

{{--============ پیام خطا ============--}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

{{--============ کارت های آماری ============--}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">کل بدهی‌های جاری</div>
                    </div>
                    <div class="h1 mb-0">{{ number_format($stats['total']) }} <small
                            class="fs-6 text-muted">ریال</small></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مانده پرداخت‌نشده</div>
                    <div class="h1 mb-0 text-danger">{{ number_format($stats['remaining']) }} <small
                            class="fs-6 text-muted">ریال</small></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">پرداخت‌شده</div>
                    <div class="h1 mb-0 text-success">{{ number_format($stats['paid']) }} <small
                            class="fs-6 text-muted">ریال</small></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card {{ $stats['overdue'] > 0 ? 'border-danger' : '' }} border-3">
                <div class="card-body">
                    <div class="subheader">بدهی‌های سررسیدگذشته</div>
                    <div class="h1 mb-0 {{ $stats['overdue'] > 0 ? 'text-danger' : '' }}">{{ $stats['overdue'] }} <small
                            class="fs-6 text-muted">مورد</small></div>
                </div>
            </div>
        </div>
    </div>

{{--================== جستجو و فیلتر ==================--}}
    <div class="card mb-4 border-3">
        <div class="card-header">
            <div class="row g-2 align-items-center w-100">
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="جستجو در عنوان یا نام طلبکار...">
                    </div>
                </div>
                <div class="col-auto">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="all">همه وضعیت‌ها</option>
                        <option value="unpaid">پرداخت‌نشده</option>
                        <option value="partial">پرداخت ناقص</option>
                        <option value="paid">پرداخت‌شده</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


{{--======================== جدول لیست بدهی ها ========================--}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="page-title">
                    <i class="bi bi-journal-minus me-2 text-info"></i>
                    مدیریت بدهی‌ها
                </h3>
                <small class="text-muted">مدیریت بدهی های فروشگاه</small>
            </div>
            <div class="d-flex gap-3">
                <button wire:click="openAddModal" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    ثبت بدهی جدید
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>عنوان</th>
                        <th>طلبکار</th>
                        <th width="110">مبلغ کل</th>
                        <th width="110">پرداخت‌شده</th>
                        <th width="110">مانده</th>
                        <th width="120">سررسید</th>
                        <th width="100">وضعیت</th>
                        <th width="120">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($debts as $debt)
                        @php
                            $dueDateStatus = $debt->due_date_status;
                            $rowClass = match ($dueDateStatus) {
                                'overdue' => 'table-danger',
                                'warning' => 'table-warning',
                                default => '',
                            };
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td>{{ $loop->iteration + ($debts->currentPage() - 1) * $debts->perPage() }}</td>
                            <td>
                                <strong>{{ $debt->title }}</strong>
                                @if ($debt->notes)
                                    <br><small class="text-muted">{{ Str::limit($debt->notes, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $debt->creditor_name }}
                                @if ($debt->creditor_type === 'supplier')
                                    <span class="badge bg-blue-lt ms-1">تامین‌کننده</span>
                                @endif
                            </td>
                            <td>{{ number_format($debt->amount) }}</td>
                            <td>{{ number_format($debt->paid_amount) }}</td>
                            <td class="{{ $debt->remaining_amount > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                {{ number_format($debt->remaining_amount) }}
                            </td>
                            <td>
                                @if ($debt->due_date)
                                    <span class="d-flex align-items-center gap-1">
                                        @if ($dueDateStatus === 'overdue')
                                            <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                        @elseif ($dueDateStatus === 'warning')
                                            <i class="bi bi-clock-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @endif
                                        {{ $debt->due_date->format('Y/m/d') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($debt->status === 'paid')
                                    <span class="badge bg-success text-dark">پرداخت‌شده</span>
                                @elseif ($debt->status === 'partial')
                                    <span class="badge bg-warning text-dark">ناقص</span>
                                @else
                                    <span class="badge bg-danger text-dark">پرداخت‌نشده</span>
                                @endif
                            </td>
                            <td>
                                @if ($debt->status !== 'paid')
                                    <button wire:click="openPayModal({{ $debt->id }})"
                                        class="btn btn-success text-dark btn-sm" title="ثبت پرداخت">
                                        <i class="bi bi-cash-coin"></i>
                                    </button>
                                @endif
                                <button wire:click="openEditModal({{ $debt->id }})"
                                    class="btn btn-warning text-dark btn-sm" title="ویرایش">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $debt->id }})"
                                    class="btn btn-danger text-dark btn-sm" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                بدهی‌ای یافت نشد
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($debts->hasPages())
            <div class="card-footer d-flex align-items-center">
                {{ $debts->links() }}
            </div>
        @endif
    </div>


{{--================== Color legend ==================--}}
    <div class="mt-3 d-flex gap-3 flex-wrap">
        <span><span class="badge bg-danger-lt text-danger px-2">■</span> سررسید گذشته</span>
        <span><span class="badge bg-warning-lt text-warning px-2">■</span> کمتر از ۷ روز به سررسید</span>
        <span><span class="badge bg-success-lt text-success px-2">■</span> پرداخت‌شده</span>
    </div>

{{-- ======================================== مودال افزودن/ویرایش بدهی ======================================== --}}
    @if ($showModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-journal-text me-2"></i>
                            {{ $editingId ? 'ویرایش بدهی' : 'ثبت بدهی جدید' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">عنوان بدهی</label>
                                <input type="text" wire:model="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="مثلاً: تعمیر یخچال">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">نوع طلبکار</label>
                                <select wire:model.live="creditor_type" class="form-select">
                                    <option value="other">سایر (تعمیرکار، اجاره، ...)</option>
                                    <option value="supplier">تامین‌کننده</option>
                                </select>
                            </div>

                            @if ($creditor_type === 'supplier')
                                <div class="col-md-6">
                                    <label class="form-label required">تامین‌کننده</label>
                                    <select wire:model.live="supplier_id"
                                        class="form-select @error('supplier_id') is-invalid @enderror">
                                        <option value="">انتخاب کنید...</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">نام طلبکار</label>
                                    <input type="text" wire:model="creditor_name"
                                        class="form-control @error('creditor_name') is-invalid @enderror"
                                        placeholder="نام تامین‌کننده">
                                    @error('creditor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label class="form-label required">نام طلبکار</label>
                                    <input type="text" wire:model="creditor_name"
                                        class="form-control @error('creditor_name') is-invalid @enderror"
                                        placeholder="مثلاً: آقای احمدی تعمیرکار">
                                    @error('creditor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label required">مبلغ بدهی (ریال)</label>
                                <input type="number" wire:model="amount"
                                    class="form-control @error('amount') is-invalid @enderror" placeholder="0"
                                    min="1">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاریخ سررسید</label>
                                <input type="date" wire:model="due_date"
                                    class="form-control @error('due_date') is-invalid @enderror">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">توضیحات</label>
                                <textarea wire:model="notes" class="form-control" rows="2" placeholder="توضیحات اضافی..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-auto"
                            wire:click="closeModal">انصراف</button>
                        <button type="button" class="btn btn-primary" wire:click="save"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="save"
                                class="spinner-border spinner-border-sm me-1"></span>
                            {{ $editingId ? 'ذخیره تغییرات' : 'ثبت بدهی' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

{{-- ======================================== مودال پرداخت بدهی ======================================== --}}
    @if ($showPayModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>ثبت پرداخت</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($payingDebtId)
                            @php $payingDebt = \App\Models\Debt::find($payingDebtId); @endphp
                            @if ($payingDebt)
                                <div class="mb-3 p-2 bg-light rounded">
                                    <div class="text-muted small">مانده بدهی</div>
                                    <div class="fw-bold text-danger">
                                        {{ number_format($payingDebt->remaining_amount) }} ریال</div>
                                </div>
                            @endif
                        @endif
                        <label class="form-label required">مبلغ پرداختی (ریال)</label>
                        <input type="number" wire:model="pay_amount"
                            class="form-control @error('pay_amount') is-invalid @enderror" placeholder="0"
                            min="1" autofocus>
                        @error('pay_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-auto"
                            wire:click="closeModal">انصراف</button>
                        <button type="button" class="btn btn-success" wire:click="recordPayment"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="recordPayment"
                                class="spinner-border spinner-border-sm me-1"></span>
                            ثبت پرداخت
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

{{-- ======================================== مودال تایید حذف بدهی ======================================== --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-dark">
                        <h5 class="modal-title">حذف بدهی</h5>
                        <button type="button" class="btn-close" wire:click="closeModal" title="بستن"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger fs-1 mb-3 d-block"></i>
                        <h4>حذف بدهی</h4>
                        <p class="text-muted">آیا مطمئن هستید؟ این عملیات قابل بازگشت نیست.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-auto"
                            wire:click="closeModal">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            <i class="bi bi-trash me-1"></i>حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
