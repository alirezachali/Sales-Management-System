<div wire:poll.{{ $pollingSeconds }}s="$refresh">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-3 mb-4" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center mb-2">
            <h3 class="fw-bold mb-1">
                <i class="bi bi-cash-stack text-success"></i>
                داشبورد صندوقدار
            </h3>
            <small class="text-muted d-flex align-items-center gap-1">
                <span wire:loading.flex wire:target="$refresh" class="align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm"></span>
                    در حال به‌روزرسانی...
                </span>
                <span wire:loading.remove wire:target="$refresh">
                    به‌صورت خودکار هر {{ $pollingSeconds }} ثانیه به‌روزرسانی می‌شود
                </span>
            </small>
        </div>

        <div class="card-body">
            @can('pos.view')
                <a href="{{ route('pos.index') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-cart-check"></i>
                    رفتن به صندوق فروش
                </a>
            @endcan
        </div>
    </div>

    {{-- کارت‌های آماری --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-body">
                    <!-- کارت آمار فروش امروز همین صندوقدار -->
                    <div class="dashboard-title">
                        <h2>💰 فروش امروز من</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-success">{{ number_format($todaySales) }}
                            {{ setting('currency', '') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-body">
                    <!-- کارت آمار فاکتورهای امروز همین صندوقدار -->
                    <div class="dashboard-title">
                        <h2>🧾 فاکتورهای امروز من</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-info">{{ $todayInvoices }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-body">
                    <!-- کارت آمار کالاهای فروش‌رفته امروز توسط همین صندوقدار -->
                    <div class="dashboard-title">
                        <h2>📦 کالاهای فروش‌رفته امروز</h2>
                    </div>
                    <div class="dashboard-number">
                        <div class="h1 mb-0 text-primary">{{ number_format($todayItemsSold) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-3">

        <!-- کارت آخرین فروش‌ها -->
        <div class="col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-warning text-dark opacity-70">
                    <strong>آخرین فروش‌ها</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>مشتری</th>
                                <th>مبلغ</th>
                                <th>تاریخ</th>
                                <th width="60">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSales as $sale)
                                <tr wire:key="latest-sale-{{ $sale->id }}">
                                    <td>{{ $sale->customer->name ?? '-' }}</td>
                                    <td>{{ number_format($sale->final_price) }}</td>
                                    <td>{{ jalaliDateTime($sale->created_at) }}</td>
                                    <td>
                                        <a href="{{ route('invoice', $sale) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            👁️
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        هنوز فروشی ثبت نشده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- کارت لیست کارهای من -->
        <div class="col-md-6">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-info text-dark d-flex justify-content-between align-items-center opacity-70">
                    <strong>
                        <i class="bi bi-card-checklist text-primary"></i>
                        لیست کارهای من
                    </strong>
                    <div class="d-flex gap-2">
                        @can('todos.create')
                            <button type="button" class="btn btn-sm btn-success" wire:click="openCreateModal">
                                <i class="bi bi-plus-lg"></i>
                                افزودن کار جدید
                            </button>
                        @endcan
                        @can('todos.view')
                            <a href="{{ route('todos.index') }}" class="btn btn-sm btn-primary">
                                مشاهده همه
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>وضعیت</th>
                                <th>اولویت</th>
                                <th>سررسید</th>
                                <th width="60">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todos as $todo)
                                <tr wire:key="cashier-todo-{{ $todo->id }}">
                                    <td class="fw-bold">{{ $todo->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $todo->status === 'completed' ? 'success' : ($todo->status === 'in_progress' ? 'info' : 'secondary') }} text-dark">
                                            {{ $todo->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $todo->priority_color }} text-dark">
                                            {{ $todo->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($todo->due_date)
                                            <span class="{{ $todo->due_date->isPast() && !$todo->isCompleted() ? 'text-danger fw-bold' : '' }}">
                                                {{ jalaliDate($todo->due_date) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('todos.edit')
                                            <button type="button"
                                                class="btn btn-sm {{ $todo->isCompleted() ? 'btn-warning' : 'btn-success' }} text-dark"
                                                wire:click="toggleComplete({{ $todo->id }})"
                                                title="{{ $todo->isCompleted() ? 'برگرداندن به در انتظار' : 'تکمیل کردن' }}">
                                                <i class="bi {{ $todo->isCompleted() ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        کاری برای شما ثبت نشده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- کارت مشتریان بدهکار -->
        <div class="col-12">
            <div class="card dashboard-card border-3">
                <div class="card-header bg-danger text-dark opacity-70">
                    <strong>
                        <i class="bi bi-person-exclamation"></i>
                        مشتریان بدهکار
                    </strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>مشتری</th>
                                <th>شماره تماس</th>
                                <th>مبلغ کل بدهی</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($debtors as $debtor)
                                <tr wire:key="customer-debtor-{{ $debtor->customer_id }}">
                                    <td class="fw-bold">{{ $debtor->customer->full_name }}</td>
                                    <td>
                                        @if ($debtor->customer->mobile)
                                            <span class="text-muted">{{ $debtor->customer->mobile }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger-emphasis fs-6">
                                            {{ number_format($debtor->debt_amount) }} {{ setting('currency', '') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        مشتری بدهکاری وجود ندارد.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================ مودال افزودن کار جدید ============================ --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">افزودن کار جدید</h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-12">
                                    <label class="form-label">عنوان کار</label>
                                    <input type="text" wire:model="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        placeholder="عنوان کار را وارد کنید...">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">توضیحات</label>
                                    <textarea wire:model="description" class="form-control" rows="3"
                                        placeholder="توضیحات اختیاری..."></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">اولویت</label>
                                    <select wire:model="priority" class="form-select">
                                        @foreach ($priorityLabels as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">تاریخ سررسید (شمسی)</label>
                                    <input type="text" wire:model="due_date_jalali" data-jdp
                                        autocomplete="off" inputmode="numeric" placeholder="1405/06/11"
                                        class="form-control @error('due_date_jalali') is-invalid @enderror">
                                    @error('due_date_jalali')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                ثبت کار
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
