{{-- کارت «لیست کارهای من» + مودال افزودن کار جدید؛ بین داشبوردهای نقش‌ها مشترک است --}}
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
                        <tr wire:key="my-todo-{{ $todo->id }}">
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
