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
        <div class="col-6 col-lg-2">
            <div class="card border-3">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-primary">{{ $counts['total'] }}</div>
                    <div class="subheader">همه</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-3">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-warning">{{ $counts['pending'] }}</div>
                    <div class="subheader">در انتظار</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-3">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-info">{{ $counts['in_progress'] }}</div>
                    <div class="subheader">در حال انجام</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-3">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-success">{{ $counts['completed'] }}</div>
                    <div class="subheader">تکمیل شده</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-3">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-danger">{{ $counts['high_priority_pending'] }}</div>
                    <div class="subheader">اولویت بالا</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-3">
                <div class="card-body text-center">
                    <div class="h2 mb-0 text-secondary">{{ $counts['due_soon'] }}</div>
                    <div class="subheader">نزدیک سررسید</div>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card mb-4 border-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="جستجو در عنوان یا توضیحات...">
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach ($statusLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="filterPriority" class="form-select">
                        <option value="">همه اولویت‌ها</option>
                        @foreach ($priorityLabels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterAssignee" class="form-select">
                        <option value="">همه کاربران</option>
                        @foreach ($allUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                        پاک کردن فیلترها
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول کارها --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-check2-square text-primary"></i>
                    لیست کارها
                </h3>
                <small class="text-muted">مدیریت و پیگیری کارهای روزانه</small>
            </div>
            <div>
                <button type="button" class="btn btn-primary" wire:click="openCreateModal">
                    <i class="bi bi-plus-circle"></i>
                    افزودن کار جدید
                </button>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40">ردیف</th>
                        <th>عنوان</th>
                        <th width="90">وضعیت</th>
                        <th width="80">اولویت</th>
                        <th width="90">کاربر</th>
                        <th width="100">انجام‌دهنده</th>
                        <th width="160">سررسید</th>
                        <th width="160">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todos as $todo)
                        <tr wire:key="todo-{{ $todo->id }}" class="{{ $todo->isCompleted() ? 'text-muted opacity-75' : '' }}">
                            <td>{{ $loop->iteration + ($todos->currentPage() - 1) * $todos->perPage() }}</td>
                            <td>
                                <div class="fw-bold {{ $todo->isCompleted() ? 'text-decoration-line-through' : '' }}">
                                    {{ $todo->title }}
                                </div>
                                @if ($todo->description)
                                    <small class="text-muted d-block" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $todo->description }}
                                    </small>
                                @endif
                            </td>
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
                                <span class="badge bg-dark">{{ $todo->user?->name ?? '—' }}</span>
                            </td>
                            <td>
                                @if ($todo->assignee)
                                    <span class="badge bg-secondary text-dark">{{ $todo->assignee?->name }}</span>
                                    @if ($todo->isCompleted())
                                        <i class="bi bi-check-circle-fill text-success ms-1" title="تکمیل شده"></i>
                                    @else
                                        <i class="bi bi-hourglass-split text-warning ms-0" title="در انتظار انجام"></i>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
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
                                <button type="button"
                                    class="btn btn-sm {{ $todo->isCompleted() ? 'btn-success text-dark' : 'btn-success text-dark' }}"
                                    wire:click="toggleComplete({{ $todo->id }}"
                                    title="{{ $todo->isCompleted() ? 'برگرداندن به در انتظار' : 'تکمیل کردن' }}">
                                    <i class="bi {{ $todo->isCompleted() ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info text-dark"
                                    wire:click="openDetails({{ $todo->id }})" title="مشاهده جزئیات">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning text-dark"
                                    wire:click="openEditModal({{ $todo->id }})" title="ویرایش">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger text-dark"
                                    wire:click="confirmDelete({{ $todo->id }})" title="حذف">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                کاری یافت نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $todos->links() }}</div>
        </div>
    </div>

    {{-- ============================ مودال افزودن/ویرایش ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? 'ویرایش کار' : 'افزودن کار جدید' }}
                            </h5>
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

                                <div class="col-md-4">
                                    <label class="form-label">وضعیت</label>
                                    <select wire:model="status" class="form-select">
                                        @foreach ($statusLabels as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">اولویت</label>
                                    <select wire:model="priority" class="form-select">
                                        @foreach ($priorityLabels as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تاریخ سررسید</label>
                                    <input type="date" wire:model="due_date" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        <i class="bi bi-person-check"></i>
                                        ثبت کار برای (انجام‌دهنده)
                                    </label>
                                    <select wire:model="assigned_to" class="form-select">
                                        @foreach ($allUsers as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->id === auth()->id() ? $user->name . ' (خودم)' : $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        اگر این کار را برای کاربر دیگری ثبت می‌کنید، او را از این فهرست انتخاب کنید.
                                    </small>
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="alert alert-light border mb-0 w-100 py-0">
                                        <div class="subheader">ثبت‌کننده</div>
                                        <div class="fw-bold">{{ auth()->user()->name ?? '' }}</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModals">
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"></span>
                                {{ $editingId ? 'ذخیره تغییرات' : 'ثبت کار' }}
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ============================ مودال جزئیات ============================ --}}
    @if ($showDetailsModal && $detailsTodo)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-card-checklist"></i>
                            جزئیات کار
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th class="w-40">عنوان</th>
                                    <td>{{ $detailsTodo->title }}</td>
                                </tr>
                                <tr>
                                    <th>توضیحات</th>
                                    <td>{{ $detailsTodo->description ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>وضعیت</th>
                                    <td>
                                        <span class="badge bg-{{ $detailsTodo->status === 'completed' ? 'success' : ($detailsTodo->status === 'in_progress' ? 'info' : 'secondary') }} text-dark">
                                            {{ $detailsTodo->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>اولویت</th>
                                    <td>
                                        <span class="badge bg-{{ $detailsTodo->priority_color }} text-dark">
                                            {{ $detailsTodo->priority_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>سررسید</th>
                                    <td>{{ $detailsTodo->due_date ? jalaliDate($detailsTodo->due_date) : '—' }}</td>
                                </tr>
                                <tr>
                                    <th>ثبت‌کننده</th>
                                    <td>{{ $detailsTodo->user?->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>انجام‌دهنده</th>
                                    <td>{{ $detailsTodo->assignee?->name ?? '—' }}</td>
                                </tr>
                                @if ($detailsTodo->completed_at)
                                    <tr>
                                        <th>زمان تکمیل</th>
                                        <td>{{ jalaliDateTime($detailsTodo->completed_at) }}</td>
                                    </tr>
                                @endif
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

    {{-- ============================ مودال تایید حذف ============================ --}}
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف کار</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این کار مطمئن هستید؟ این عملیات قابل بازگشت نیست.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>