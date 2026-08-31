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
                    <div class="subheader">هزینه‌ی امروز</div>
                    <div class="h1 mb-0">
                        {{ number_format($totalToday) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">هزینه‌ی این ماه</div>
                    <div class="h1 mb-0 text-warning">
                        {{ number_format($totalMonth) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مجموع کل هزینه‌ها</div>
                    <div class="h1 mb-0 text-danger">
                        {{ number_format($totalAll) }} {{ setting('currency', '') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">تعداد هزینه‌ها</div>
                    <div class="h1 mb-0">{{ number_format($expenseCount) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو عنوان یا شماره مرجع...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterCategoryId" class="form-select">
                        <option value="">همه دسته‌ها</option>
                        @foreach ($allCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterPaymentMethod" class="form-select">
                        <option value="">همه روش‌ها</option>
                        @foreach ($paymentMethods as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterPeriod" class="form-select">
                        <option value="">همه زمان‌ها</option>
                        <option value="today">امروز</option>
                        <option value="month">این ماه</option>
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

    {{-- جدول هزینه‌ها --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-wallet2 text-danger"></i>
                    مدیریت هزینه‌ها
                </h3>
                <small class="text-muted">ثبت و پیگیری هزینه‌ها و دسته‌بندی آن‌ها</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-info text-dark" wire:click="openCategoryCreateModal"
                    title="مدیریت دسته‌بندی هزینه‌ها">
                    <i class="bi bi-tags"></i>
                    افزودن دسته‌بندی
                </button>
                <button type="button" class="btn btn-primary" wire:click="openCreateModal" title="افزودن هزینه جدید">
                    <i class="bi bi-plus-circle"></i>
                    ثبت هزینه
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40">ردیف</th>
                        <th width="160">عنوان</th>
                        <th width="70">دسته‌بندی</th>
                        <th width="100">کارمند</th>
                        <th width="135">تاریخ</th>
                        <th width="60">روش پرداخت</th>
                        <th width="130">مبلغ</th>
                        <th width="120">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr wire:key="expense-{{ $expense->id }}">
                            <td>{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                            <td>
                                <div class="fw-bold">{{ $expense->title }}</div>
                                @if ($expense->reference_number)
                                    <small class="text-muted">مرجع: {{ $expense->reference_number }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary text-dark">{{ $expense->category?->name }}</span>
                            </td>
                            <td>
                                @if ($expense->employee)
                                    <span class="badge bg-info text-dark">{{ $expense->employee->full_name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ jalaliDate($expense->expense_date) }}</td>
                            <td>{{ $expense->payment_method_text }}</td>
                            <td class="fw-bold text-danger">
                                {{ number_format($expense->amount) }} {{ setting('currency', '') }}
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-secondary text-dark"
                                    wire:click="openDetails({{ $expense->id }})" title="مشاهده جزئیات">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning text-dark"
                                    wire:click="openEditModal({{ $expense->id }})" title="ویرایش هزینه">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger text-dark"
                                    wire:click="confirmDelete({{ $expense->id }})" title="حذف هزینه">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                هیچ هزینه‌ای ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $expenses->links() }}</div>
        </div>
    </div>

    {{-- کارت پرهزینه‌ترین دسته‌بندی‌های این ماه --}}
    @if ($topCategories->isNotEmpty())
        <div class="card mt-4 border-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart text-warning"></i>
                    پرهزینه‌ترین دسته‌بندی‌های این ماه
                </h5>
            </div>
            <div class="card-body">
                @php $maxTotal = $topCategories->max('total'); @endphp
                @foreach ($topCategories as $tc)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-secondary-lt text-dark" style="min-width: 140px;">
                            {{ $tc->category?->name ?? 'بدون دسته' }}
                        </span>
                        <div class="progress flex-grow-1" style="height: 12px;">
                            <div class="progress-bar bg-warning" style="width: {{ $maxTotal > 0 ? ($tc->total / $maxTotal) * 100 : 0 }}%"></div>
                        </div>
                        <span class="fw-bold text-nowrap">
                            {{ number_format($tc->total) }} {{ setting('currency', '') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============================ مودال افزودن/ویرایش هزینه ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="expense-form-modal">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? 'ویرایش هزینه' : 'ثبت هزینه جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">عنوان هزینه</label>
                                    <input type="text" wire:model="title"
                                        class="form-control @error('title') is-invalid @enderror">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">دسته‌بندی</label>
                                    <select wire:model="expense_category_id"
                                        class="form-select @error('expense_category_id') is-invalid @enderror">
                                        <option value="">انتخاب دسته‌بندی...</option>
                                        @foreach ($allCategories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }} {{ $category->is_active ? '' : '(غیرفعال)' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('expense_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">مبلغ</label>
                                    <input type="number" step="0.01" min="0" wire:model="amount"
                                        class="form-control @error('amount') is-invalid @enderror">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تاریخ هزینه</label>
                                    <input type="date" wire:model="expense_date"
                                        class="form-control @error('expense_date') is-invalid @enderror">
                                    @error('expense_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">روش پرداخت</label>
                                    <select wire:model="payment_method" class="form-select">
                                        @foreach ($paymentMethods as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">کارمند (برای حقوق و مزایا)</label>
                                    <select wire:model="employee_id" class="form-select">
                                        <option value="">بدون کارمند</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">شماره مرجع (اختیاری)</label>
                                    <input type="text" wire:model="reference_number" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">توضیحات</label>
                                    <textarea wire:model="description" class="form-control" rows="3"></textarea>
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
                                {{ $editingId ? 'ذخیره تغییرات' : 'ثبت هزینه' }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال جزئیات هزینه ============================ --}}
    @if ($showDetailsModal && $detailsExpense)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="expense-details-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt"></i>
                            جزئیات هزینه
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th class="w-40 bg-light">عنوان</th>
                                    <td>{{ $detailsExpense->title }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">دسته‌بندی</th>
                                    <td>{{ $detailsExpense->category?->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">کارمند</th>
                                    <td>{{ $detailsExpense->employee?->full_name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">مبلغ</th>
                                    <td class="fw-bold text-danger">
                                        {{ number_format($detailsExpense->amount) }} {{ setting('currency', '') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">تاریخ</th>
                                    <td>{{ jalaliDate($detailsExpense->expense_date) }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">روش پرداخت</th>
                                    <td>{{ $detailsExpense->payment_method_text }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">شماره مرجع</th>
                                    <td>{{ $detailsExpense->reference_number ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">ثبت‌کننده</th>
                                    <td>{{ $detailsExpense->user?->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">توضیحات</th>
                                    <td>{{ $detailsExpense->description ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================ مودال تایید حذف هزینه ============================ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="expense-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف هزینه</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این هزینه مطمئن هستید؟ این عملیات قابل بازگشت نیست.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================ مودال افزودن/ویرایش دسته‌بندی ============================ --}}
    @if ($showCategoryModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="expense-category-form-modal">
            <div class="modal-dialog modal-dialog-centered">
                <form wire:submit="saveCategory">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $categoryId ? 'ویرایش دسته‌بندی' : 'افزودن دسته‌بندی هزینه' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-12">
                                    <label class="form-label">نام دسته‌بندی</label>
                                    <input type="text" wire:model="category_name"
                                        class="form-control @error('category_name') is-invalid @enderror">
                                    @error('category_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">توضیحات</label>
                                    <textarea wire:model="category_description" class="form-control" rows="2"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" wire:model="category_is_active"
                                            class="form-check-input" id="category-is-active">
                                        <label class="form-check-label" for="category-is-active">فعال</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals"
                                title="انصراف">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="saveCategory">
                                <span wire:loading wire:target="saveCategory"
                                    class="spinner-border spinner-border-sm"></span>
                                {{ $categoryId ? 'ذخیره تغییرات' : 'ذخیره دسته‌بندی' }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال تایید حذف دسته‌بندی ============================ --}}
    @if ($showCategoryDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="expense-category-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف دسته‌بندی</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این دسته‌بندی مطمئن هستید؟ هزینه‌های مربوط به این دسته‌بندی نیز حذف خواهند شد.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteCategory">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
