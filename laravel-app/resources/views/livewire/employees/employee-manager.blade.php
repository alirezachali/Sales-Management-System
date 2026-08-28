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
                    <div class="subheader">تعداد کارکنان</div>
                    <div class="h1 mb-0">{{ number_format($totalEmployees) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">کارکنان فعال</div>
                    <div class="h1 mb-0 text-success">{{ number_format($activeEmployees) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">کارکنان غیرفعال</div>
                    <div class="h1 mb-0 text-danger">{{ number_format($inactiveEmployees) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-3">
                <div class="card-body">
                    <div class="subheader">مجموع حقوق پایه ماهانه</div>
                    <div class="h1 mb-0">
                        {{ number_format($monthlySalaryTotal) }} {{ setting('currency', '') }}
                    </div>
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
                        placeholder="جستجو نام، موبایل، کد ملی یا عنوان شغلی...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="active">فعال</option>
                        <option value="inactive">غیرفعال</option>
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

    {{-- جدول کارکنان --}}
    <div class="card shadow-sm border-3" wire:loading.class="opacity-50">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-person-badge text-primary"></i>
                    مدیریت کارکنان
                </h3>
                <small class="text-muted">مدیریت اطلاعات کارکنان و حقوق پایه آن‌ها</small>
            </div>
            <div class="d-flex gap-3">
                <button type="button" class="btn btn-primary" wire:click="openCreateModal" title="افزودن کارمند جدید">
                    <i class="bi bi-person-plus"></i>
                    افزودن کارمند
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40">ردیف</th>
                        <th>نام کارمند</th>
                        <th width="110">موبایل</th>
                        <th width="110">عنوان شغلی</th>
                        <th width="170">تاریخ استخدام</th>
                        <th width="130">حقوق پایه</th>
                        <th width="60">وضعیت</th>
                        <th width="90">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr wire:key="employee-{{ $employee->id }}">
                            <td>{{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold"
                                        style="width: 32px; height: 32px;">
                                        {{ $employee->initials }}
                                    </span>
                                    <div>
                                        <div class="fw-bold">{{ $employee->full_name }}</div>
                                        <small class="text-muted">{{ $employee->job_title }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->mobile ?? '—' }}</td>
                            <td>{{ $employee->job_title ?? '—' }}</td>
                            <td>{{ jalaliDate($employee->hired_at) }}</td>
                            <td>
                                {{ number_format($employee->base_salary) }} {{ setting('currency', '') }}
                            </td>
                            <td>
                                @if ($employee->is_active)
                                    <span class="badge bg-success text-dark">فعال</span>
                                @else
                                    <span class="badge bg-danger text-dark">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning text-dark"
                                    wire:click="openEditModal({{ $employee->id }})" title="ویرایش کارمند">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger text-dark"
                                    wire:click="confirmDelete({{ $employee->id }})" title="حذف کارمند">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                هیچ کارمندی ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $employees->links() }}</div>
        </div>
    </div>

    {{-- ============================ مودال افزودن/ویرایش کارمند ============================ --}}
    @if ($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.5);"
            wire:key="employee-form-modal">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <form wire:submit="save">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                {{ $editingId ? 'ویرایش کارمند' : 'افزودن کارمند جدید' }}
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
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
                                    <label class="form-label">کد ملی</label>
                                    <input type="text" wire:model="national_code"
                                        class="form-control @error('national_code') is-invalid @enderror">
                                    @error('national_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">عنوان شغلی</label>
                                    <input type="text" wire:model="job_title" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">تاریخ استخدام</label>
                                    <input type="date" wire:model="hired_at" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">حقوق پایه (ماهانه)</label>
                                    <input type="number" step="0.01" min="0" wire:model="base_salary"
                                        class="form-control @error('base_salary') is-invalid @enderror">
                                    @error('base_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" wire:model="is_active" class="form-check-input"
                                            id="employee-is-active">
                                        <label class="form-check-label" for="employee-is-active">فعال</label>
                                    </div>
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
                                {{ $editingId ? 'ذخیره تغییرات' : 'ذخیره کارمند' }}
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
            wire:key="employee-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف کارمند</h5>
                        <button type="button" class="btn-close" wire:click="closeModals" title="بستن"></button>
                    </div>
                    <div class="modal-body">
                        آیا از حذف این کارمند مطمئن هستید؟ این عملیات قابل بازگشت نیست.
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
