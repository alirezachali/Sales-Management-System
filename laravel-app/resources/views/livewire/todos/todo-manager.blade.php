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

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40">ردیف</th>
                        <th>عنوان</th>
                        <th width="100">وضعیت</th>
                        <th width="90">اولویت</th>
                        <th width="90">کاربر</th>
                        <th width="90">انجام‌دهنده</th>
                        <th width="120">سررسید</th>
                        <th width="130">عملیات</th>
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
                                <span class="badge bg-{{ $todo->status === 'completed' ? 'success' : ($todo->status === 'in_progress' ? 'info' : 'secondary') }}">
                                    {{ $todo->status_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $todo->priority_color }}">
                                    {{ $todo->priority_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-dark">{{ $todo->user?->name ?? '—' }}</span>
                            </td>
                            <td>
                                @if ($todo->assignee)
                                    <span class="badge bg-secondary">{{ $todo->assignee?->name }}</span>
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
                                    class="btn btn-sm {{ $todo->isCompleted() ? 'btn-outline-success' : 'btn-success' }}"
                                    wire:click="toggleComplete({{ $todo->id }}"
                                    title="{{ $todo->isCompleted() ? 'برگرداندن به در انتظار' : 'تکمیل کردن' }}">
                                    <i class="bi {{ $todo->isCompleted() ? 'bi-arrow-counterclockwise' : 'bi-check-lg' }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-light"
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
                                    <label class="form-label">انجام‌دهنده</label>
                                    <select wire:model="assigned_to" class="form-select">
                                        <option value="">بدون انجام‌دهنده</option>
                                        @foreach ($allUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
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
                                    <th class="w-40 bg-light">عنوان</th>
                                    <td>{{ $detailsTodo->title }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">توضیحات</th>
                                    <td>{{ $detailsTodo->description ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">وضعیت</th>
                                    <td>
                                        <span class="badge bg-{{ $detailsTodo->status === 'completed' ? 'success' : ($detailsTodo->status === 'in_progress' ? 'info' : 'secondary') }}">
                                            {{ $detailsTodo->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">اولویت</th>
                                    <td>
                                        <span class="badge bg-{{ $detailsTodo->priority_color }}">
                                            {{ $detailsTodo->priority_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">سررسید</th>
                                    <td>{{ $detailsTodo->due_date ? jalaliDate($detailsTodo->due_date) : '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">ثبت‌کننده</th>
                                    <td>{{ $detailsTodo->user?->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">انجام‌دهنده</th>
                                    <td>{{ $detailsTodo->assignee?->name ?? '—' }}</td>
                                </tr>
                                @if ($detailsTodo->completed_at)
                                    <tr>
                                        <th class="bg-light">زمان تکمیل</th>
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