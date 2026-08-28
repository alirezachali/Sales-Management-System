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

    {{-- کارت‌های آماری --}}
    <div class="row row-cards mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد مشتریان</div>
                    <div class="h1 mb-0">{{ $totalCustomers }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مشتریان فعال</div>
                    <div class="h1 mb-0 text-success">{{ $activeCustomers }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مشتریان غیرفعال</div>
                    <div class="h1 mb-0 text-danger">{{ $inactiveCustomers }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">بدون رده</div>
                    <div class="h1 mb-0 text-warning">{{ $noRoleCustomers }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-7">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو نام یا موبایل...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterRoleId" class="form-select">
                        <option value="">همه رده‌ها</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
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

    {{-- جدول مشتریان --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-person-standing-dress text-primary"></i>
                باشگاه مشتریان
            </h3>
            <small class="text-muted">مدیریت اطلاعات مشتریان و رده های باشگاه مشتریان</small>
            </div>
            <div class="d-flex gap-3">

                <a href="{{ route('customer-roles.index') }}">
                    <button class="btn btn-info text-dark" title="مدیریت رده‌های باشگاه مشتریان">
                        <i class="bi bi-award"></i>
                        مدیریت رده‌های باشگاه
                    </button>
                </a>

                <button type="button" class="btn btn-primary" wire:click="openCreateModal" title="افزودن مشتری جدید">
                    <i class="bi bi-person-plus"></i>
                    افزودن مشتری
                </button>

            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40">ردیف</th>
                        <th>نام مشتری</th>
                        <th width="90">موبایل</th>
                        <th width="100">رده باشگاه</th>
                        <th width="60">تعداد خرید</th>
                        <th>مبلغ کل خرید</th>
                        <th width="80">وضعیت</th>
                        <th width="160">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr wire:key="customer-{{ $customer->id }}">
                            <td>{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                            <td>{{ $customer->full_name }}</td>
                            <td>{{ $customer->mobile }}</td>
                            <td>
                                @if ($customer->role)
                                    <span class="badge bg-{{ $customer->role->color }} text-dark">
                                        <i class="bi {{ $customer->role->icon }}"></i>
                                        {{ $customer->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-lt text-dark">بدون رده</span>
                                @endif
                            </td>
                            <td>{{ number_format($customer->purchase_count) }}</td>
                            <td>{{ number_format($customer->total_purchase_amount) }} {{ setting('currency', '') }}
                            </td>
                            <td>
                                @if ($customer->is_active)
                                    <span class="badge bg-success text-dark">فعال</span>
                                @else
                                    <span class="badge bg-danger text-dark">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning text-dark"
                                    wire:click="openEditModal({{ $customer->id }})" title="ویرایش مشتری">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info text-dark"
                                    wire:click="openLedger({{ $customer->id }})" title="مشاهده گردش حساب مشتری">
                                    <i class="bi bi-wallet-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-light"
                                    wire:click="recalculateRole({{ $customer->id }})"
                                    title="بازمحاسبه‌ی رده‌ی این مشتری بر اساس آمار خرید فعلی">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger text-dark"
                                    wire:click="confirmDelete({{ $customer->id }})" title="حذف مشتری">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                هیچ مشتری‌ای ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $customers->links() }}</div>
        </div>
    </div>

    {{-- ============================ مودال افزودن/ویرایش مشتری ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="customer-form-modal">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? 'ویرایش مشتری' : 'افزودن مشتری جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals"
                                title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label">نام</label>
                                    <input type="text" wire:model="first_name"
                                        class="form-control @error('first_name') is-invalid @enderror">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">نام خانوادگی</label>
                                    <input type="text" wire:model="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">موبایل</label>
                                    <input type="text" wire:model="mobile"
                                        class="form-control @error('mobile') is-invalid @enderror">
                                    @error('mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تلفن ثابت</label>
                                    <input type="text" wire:model="phone" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">کد ملی</label>
                                    <input type="text" wire:model="national_code" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تاریخ تولد</label>
                                    <input type="date" wire:model="birth_date" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">جنسیت</label>
                                    <select wire:model="gender" class="form-select">
                                        <option value="">نامشخص</option>
                                        <option value="male">آقا</option>
                                        <option value="female">خانم</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">رده‌ی باشگاه مشتریان</label>
                                    <select wire:model="customer_role_id" class="form-select">
                                        <option value="">بدون رده (خودکار تعیین می‌شود)</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="customer-is-active">
                                        <label class="form-check-label" for="customer-is-active">فعال</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">استان</label>
                                    <input type="text" wire:model="province" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">شهر</label>
                                    <input type="text" wire:model="city" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">کد پستی</label>
                                    <input type="text" wire:model="postal_code" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">آدرس</label>
                                    <textarea wire:model="address" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">یادداشت‌ها</label>
                                    <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals"
                                title="انصراف">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $editingId ? 'ذخیره تغییرات' : 'ذخیره مشتری' }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال تایید حذف ============================ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="customer-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف مشتری</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این مشتری مطمئن هستید؟ این عملیات قابل بازگشت نیست.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================ مودال گردش حساب مشتری ============================ --}}
    @if ($showLedgerModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="customer-ledger-modal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-wallet2"></i>
                            گردش حساب:
                            {{ $ledgerCustomer?->full_name }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">

                        @if ($ledgerCustomer)
                            <div class="alert {{ $ledgerBalance > 0 ? 'alert-warning' : 'alert-success' }}">
                                مانده حساب فعلی:
                                <strong>{{ number_format($ledgerBalance) }} {{ setting('currency', '') }}</strong>
                                @if ($ledgerBalance > 0)
                                    (بدهکار)
                                @else
                                    (تسویه)
                                @endif
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>تاریخ</th>
                                            <th>نوع</th>
                                            <th>مبلغ</th>
                                            <th>توضیحات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ledgerTransactions as $transaction)
                                            <tr wire:key="ledger-{{ $transaction->id }}">
                                                <td>{{ $transaction->created_at }}</td>
                                                <td>
                                                    @switch($transaction->type)
                                                        @case('sale')
                                                            <span class="badge bg-danger">خرید (بدهکار)</span>
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
                                                        هیچ تراکنشی برای این مشتری ثبت نشده است.
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
