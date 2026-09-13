<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show glass-card" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div class="btn-group" role="group">
            <button class="btn btn-outline-secondary" wire:click="changeMonth('prev')"><i class="bi bi-chevron-right"></i></button>
            <button class="btn btn-secondary text-white fw-bold">{{ $monthTitle }}</button>
            <button class="btn btn-outline-secondary" wire:click="changeMonth('next')"><i class="bi bi-chevron-left"></i></button>
        </div>

        <input type="text" class="form-control" style="max-width:260px" placeholder="جستجوی کارمند..."
            wire:model.live.debounce.400ms="employeeFilter">
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h3 class="fw-bold mb-1"><i class="bi bi-calendar-check text-primary"></i> حضور و غیاب کارکنان</h3>
            <small class="text-muted">روی هر خانه کلیک کنید تا سابقه‌ی آن روز ثبت/ویرایش شود. رنگ‌ها:
                <span class="badge bg-success">حاضر</span>
                <span class="badge bg-danger">غایب</span>
                <span class="badge bg-warning">مرخصی</span>
                <span class="badge bg-info">نیمه‌وقت</span>
            </small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0 attendance-grid">
                    <thead>
                        <tr>
                            <th style="min-width:150px" class="sticky-col">کارمند</th>
                            @foreach ($days as $day)
                                <th class="text-center p-1" style="width:26px">{{ $day }}</th>
                            @endforeach
                            <th class="text-center">حاضر</th>
                            <th class="text-center">غایب</th>
                            <th class="text-center">اضافه‌کار</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            @php $sum = $summary->get($employee->id); @endphp
                            <tr wire:key="emp-{{ $employee->id }}">
                                <td class="sticky-col fw-semibold small">
                                    {{ $employee->full_name }}
                                    <div class="text-muted" style="font-size:.7rem">{{ $employee->job_title }}</div>
                                </td>
                                @foreach ($days as $day)
                                    @php
                                        $greg = \Hekmatinasser\Verta\Verta::parse($monthJalali.'-'.str_pad((string) $day, 2, '0', STR_PAD_LEFT))->toCarbon()->toDateString();
                                        $rec = $records->get($employee->id.'|'.$greg)?->first();
                                    @endphp
                                    <td class="text-center p-0"
                                        @can('attendance.edit') wire:click="openFormFor({{ $employee->id }}, '{{ $greg }}')" @endcan>
                                        <div class="att-cell {{ $rec ? 'att-'.$rec->status : 'att-empty' }}" title="{{ $rec ? $rec->statusText() : '' }}">
                                            @if ($rec)
                                                @if ($rec->status === 'present')<i class="bi bi-check-lg"></i>
                                                @elseif ($rec->status === 'absent')<i class="bi bi-x-lg"></i>
                                                @elseif ($rec->status === 'leave')<i class="bi bi-dash-lg"></i>
                                                @elseif ($rec->status === 'half')<i class="bi bi-circle-half"></i>
                                                @else<i class="bi bi-sun"></i>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                                <td class="text-center"><span class="badge bg-success-subtle text-success-emphasis">{{ $sum->present_days ?? 0 }}</span></td>
                                <td class="text-center"><span class="badge bg-danger-subtle text-danger-emphasis">{{ $sum->absent_days ?? 0 }}</span></td>
                                <td class="text-center small">{{ number_format((float) ($sum->total_overtime ?? 0), 1) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($days) + 4 }}" class="text-center py-4 text-muted">کارمندی یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- مودال ثبت سابقه --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="att-form-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h5 class="modal-title">ثبت حضور و غیاب</h5>
                            <button type="button" class="btn-close" wire:click="closeModals"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">تاریخ (شمسی)</label>
                                    <input type="text" class="form-control @error('formDateJalali') is-invalid @enderror" wire:model="formDateJalali" placeholder="1405-06-01">
                                    @error('formDateJalali')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">وضعیت</label>
                                    <select class="form-select" wire:model="formStatus">
                                        <option value="present">حاضر</option>
                                        <option value="absent">غایب</option>
                                        <option value="leave">مرخصی</option>
                                        <option value="half">نیمه‌وقت</option>
                                        <option value="holiday">تعطیل</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">ورود</label>
                                    <input type="time" class="form-control" wire:model="formCheckIn">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">خروج</label>
                                    <input type="time" class="form-control" wire:model="formCheckOut">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">تأخیر (دقیقه)</label>
                                    <input type="number" class="form-control" wire:model="formLateMinutes" min="0" step="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">اضافه‌کاری (ساعت)</label>
                                    <input type="number" class="form-control" wire:model="formOvertimeHours" min="0" step="0.5">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">یادداشت</label>
                                    <textarea class="form-control" rows="2" wire:model="formNotes"></textarea>
                                </div>
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

</div>
