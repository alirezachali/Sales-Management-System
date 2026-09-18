<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    {{--=================== نمایش پیغام‌های موفقیت ===================--}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{--=================== نمایش پیغام‌های خطا ===================--}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" title="بستن"></button>
        </div>
    @endif

    {{-- جمع کل نقدینگی --}}
    <div class="card border-0 shadow-sm mb-4 gradient-header">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="subheader text-white-50">جمع موجودی همه صندوق‌ها</div>
                <div class="h1 mb-0 fw-bold text-white">{{ number_format($totalBalance) }} <small class="fs-6">تومان</small></div>
            </div>
            <div class="d-flex gap-2">
                @can('cashboxes.create')
                    <button class="btn btn-light" wire:click="openCreateModal">
                        <i class="bi bi-plus-circle"></i> صندوق جدید
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="row row-cards">
        {{-- کارت‌های صندوق‌ها --}}
        <div class="{{ $selected ? 'col-lg-4' : 'col-12' }}">
            <div class="row row-cards g-3">
                @forelse ($cashboxes as $cashbox)
                    <div class="col-sm-6 col-lg-6">
                        <div class="card stat-card border-0 h-100 {{ $selected?->id === $cashbox->id ? 'ring-selected' : '' }} cursor-pointer"
                            wire:click="viewCashbox({{ $cashbox->id }})" style="cursor:pointer">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="stat-icon" style="--icon-bg: rgba(99,102,241,.15); --icon-color:#6366f1">
                                        <i class="bi {{ $cashbox->type === 'bank' ? 'bi-bank' : ($cashbox->type === 'wallet' ? 'bi-wallet2' : 'bi-cash-stack') }}"></i>
                                    </div>
                                    <div class="d-flex gap-1">
                                        @can('cashboxes.edit')
                                            <button class="btn btn-sm btn-ghost-primary" wire:click.stop="openEditModal({{ $cashbox->id }})" title="ویرایش">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan
                                        @can('cashboxes.delete')
                                            <button class="btn btn-sm btn-ghost-danger" wire:click.stop="confirmDelete({{ $cashbox->id }})" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                                <div class="fw-bold">{{ $cashbox->name }}
                                    @if ($cashbox->is_default)<span class="badge bg-warning text-dark">پیش‌فرض</span>@endif
                                </div>
                                <div class="small text-muted mb-2">{{ $cashbox->typeText() }} — {{ $transactions_count ?? $cashbox->transactions_count }} گردش</div>
                                <div class="h3 mb-0 {{ (float) $cashbox->balance < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format((float) $cashbox->balance) }}
                                    <small class="fs-6 text-muted">تومان</small>
                                </div>
                                @if (! $cashbox->is_active)
                                    <span class="badge bg-secondary mt-2">غیرفعال</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">صندوقی ثبت نشده است.</div>
                @endforelse
            </div>
        </div>

        {{-- گردش صندوق انتخاب‌شده --}}
        @if ($selected)
            <div class="col-lg-8" wire:key="cashbox-{{ $selected->id }}">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header flex-wrap gap-2">
                        <div>
                            <h3 class="fw-bold mb-1"><i class="bi bi-arrow-repeat text-primary"></i> گردش {{ $selected->name }}</h3>
                            <small class="text-muted">موجودی فعلی: {{ number_format((float) $selected->balance) }} تومان</small>
                        </div>
                        <div class="ms-auto d-flex gap-2">
                            @can('cashboxes.deposit')
                                <button class="btn btn-sm btn-success" wire:click="openTxModal('deposit')"><i class="bi bi-arrow-down-circle"></i> واریز</button>
                            @endcan
                            @can('cashboxes.withdraw')
                                <button class="btn btn-sm btn-danger" wire:click="openTxModal('withdraw')"><i class="bi bi-arrow-up-circle"></i> برداشت</button>
                            @endcan
                            @can('cashboxes.transfer')
                                <button class="btn btn-sm btn-info" wire:click="openTxModal('transfer')"><i class="bi bi-shuffle"></i> انتقال</button>
                            @endcan
                            @can('cashboxes.adjust')
                                <button class="btn btn-sm btn-warning text-dark" wire:click="openTxModal('adjustment')"><i class="bi bi-sliders"></i> اصلاح</button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-3 border-bottom">
                            <div class="input-group input-group-sm" style="max-width:320px">
                                <span class="input-group-text">فیلتر نوع</span>
                                <select class="form-select" wire:model.live="filterType">
                                    <option value="all">همه</option>
                                    @foreach ($typeLabels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>تاریخ</th>
                                        <th>نوع</th>
                                        <th>شرح</th>
                                        <th>کاربر</th>
                                        <th class="text-start">مبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $tx)
                                        <tr wire:key="tx-{{ $tx->id }}">
                                            <td class="small text-muted">{{ jalaliDateTime($tx->created_at) }}</td>
                                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $tx->typeText() }}</span></td>
                                            <td class="small">{{ $tx->description ?? '—' }}</td>
                                            <td class="small">{{ $tx->user?->name ?? '—' }}</td>
                                            <td class="text-start fw-bold {{ $tx->isPositive() ? 'text-success' : 'text-danger' }}">
                                                {{ $tx->isPositive() ? '+' : '−' }} {{ number_format(abs((float) $tx->amount)) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">گردشی ثبت نشده است.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{ $transactions->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- مودال صندوق --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="cb-form-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $cashboxId ? 'ویرایش صندوق' : 'صندوق جدید' }}</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">نام صندوق <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نوع</label>
                                    <select class="form-select" wire:model="type">
                                        <option value="cash">نقدی</option>
                                        <option value="bank">حساب بانکی</option>
                                        <option value="wallet">کیف پول الکترونیکی</option>
                                        <option value="other">سایر</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نام بانک</label>
                                    <input type="text" class="form-control" wire:model="bank_name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">شماره حساب</label>
                                    <input type="text" class="form-control" wire:model="account_number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">شبا</label>
                                    <input type="text" class="form-control" wire:model="iban">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">موجودی افتتاحیه</label>
                                    <input type="number" class="form-control" wire:model="opening_balance">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="cb_default" wire:model="is_default">
                                        <label class="form-check-label" for="cb_default">صندوق پیش‌فرض</label>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="cb_active" wire:model="is_active">
                                        <label class="form-check-label" for="cb_active">فعال</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">یادداشت</label>
                                    <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary">ذخیره</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال تراکنش --}}
    @if ($showTxModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="cb-tx-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="saveTx">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ ['deposit' => 'واریز به صندوق', 'withdraw' => 'برداشت از صندوق', 'transfer' => 'انتقال وجه', 'adjustment' => 'اصلاح موجودی'][$txType] }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            @if ($txType === 'transfer')
                                <div class="mb-3">
                                    <label class="form-label">صندوق مقصد <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="txToCashbox">
                                        <option value="">انتخاب کنید...</option>
                                        @foreach ($cashboxes as $cb)
                                            @if ($cb->id !== $selected?->id)
                                                <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label">مبلغ (تومان) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('txAmount') is-invalid @enderror" wire:model="txAmount" min="0">
                                @error('txAmount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($txType === 'adjustment')
                                    <small class="text-muted">برای کاهش، مبلغ منفی وارد کنید.</small>
                                @endif
                            </div>
                            <div>
                                <label class="form-label">شرح</label>
                                <input type="text" class="form-control" wire:model="txDescription">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                            <button type="submit" class="btn btn-primary">ثبت</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال حذف --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="cb-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">حذف صندوق</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">این صندوق حذف شود؟</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
