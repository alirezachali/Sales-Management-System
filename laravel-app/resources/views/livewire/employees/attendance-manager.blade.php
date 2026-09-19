<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div class="btn-group" role="group">
            <button class="btn btn-outline-secondary" wire:click="changeMonth('prev')"><i
                    class="bi bi-chevron-right"></i></button>
            <button class="btn btn-secondary text-white fw-bold">{{ $monthTitle }}</button>
            <button class="btn btn-outline-secondary" wire:click="changeMonth('next')"><i
                    class="bi bi-chevron-left"></i></button>
        </div>

        <input type="text" class="form-control" style="max-width:260px" placeholder="جستجوی کارمند..."
            wire:model.live.debounce.400ms="employeeFilter">
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-calendar-check text-primary"></i>
                    حضور و غیاب کارکنان
                </h3>
                <small class="text-muted">روی هر خانه کلیک کنید تا سابقه‌ی آن روز ثبت/ویرایش شود. رنگ‌ها:
                    <span class="badge att-legend att-present">حاضر</span>
                    <span class="badge att-legend att-absent">غایب</span>
                    <span class="badge att-legend att-leave">مرخصی</span>
                    <span class="badge att-legend att-half">نیمه‌وقت</span>
                    <span class="badge att-legend att-holiday">تعطیل</span>
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0 attendance-grid">
                    <thead>
                        <tr>
                            <th style="min-width:100px" class="sticky-col">نام کارمند</th>
                            @foreach ($days as $day)
                                <th class="text-center p-1" style="width:26px">{{ $day }}</th>
                            @endforeach
                            {{-- <th class="text-center">حاضر</th> --}}
                            {{-- <th class="text-center">غایب</th> --}}
                            {{-- <th class="text-center">اضافه‌کار</th> --}}
                            <th class="text-center" style="width:40px">گزارش</th>
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
                                        $greg = \Hekmatinasser\Verta\Verta::parse(
                                            $monthJalali . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT),
                                        )
                                            ->toCarbon()
                                            ->toDateString();
                                        $rec = optional($records->get($employee->id . '|' . $greg))->first();
                                    @endphp
                                    <td class="text-center p-0"
                                        @can('attendance.edit') wire:click="openFormFor({{ $employee->id }}, '{{ $greg }}')" @endcan>
                                        <div class="att-cell {{ $rec ? 'att-' . $rec->status : 'att-empty' }}"
                                            title="{{ $rec ? $rec->statusText() : '' }}">
                                            @if ($rec)
                                                @switch($rec->status)
                                                    @case('present')<i class="bi bi-check-lg"></i>
                                                    @break
                                                    @case('absent')<i class="bi bi-x-lg"></i>
                                                    @break
                                                    @case('leave')<i class="bi bi-dash-lg"></i>
                                                    @break
                                                    @case('half')<i class="bi bi-circle-half"></i>
                                                    @break
                                                    @default<i class="bi bi-sun"></i>
                                                @endswitch
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                                {{-- <td class="text-center">
                                    <span class="badge bg-success-subtle text-success-emphasis">
                                        {{ $sum->present_days ?? 0 }}
                                    </span>
                                </td> --}}
                                {{-- <td class="text-center">
                                    <span class="badge bg-danger-subtle text-danger-emphasis">
                                        {{ $sum->absent_days ?? 0 }}
                                    </span>
                                </td> --}}
                                {{-- <td class="text-center small">
                                    {{ number_format((float) ($sum->total_overtime ?? 0), 1) }}
                                </td> --}}
                                <td class="text-center p-0">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        title="گزارش کامل ماه" wire:click="openReport({{ $employee->id }})">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($days) + 5 }}" class="text-center py-4 text-muted">کارمندی یافت
                                    نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- مودال ثبت سابقه --}}
    @if ($showFormModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);"
            wire:key="att-form-modal">
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
                                    <input type="text"
                                        class="form-control @error('formDateJalali') is-invalid @enderror"
                                        wire:model="formDateJalali" data-jdp autocomplete="off" inputmode="numeric"
                                        placeholder="1405-06-01">
                                    @error('formDateJalali')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">وضعیت</label>
                                    <select class="form-select" wire:model.live="formStatus">
                                        <option value="present">حاضر</option>
                                        <option value="absent">غایب</option>
                                        <option value="leave">مرخصی</option>
                                        <option value="half">نیمه‌وقت</option>
                                        <option value="holiday">تعطیل</option>
                                    </select>
                                </div>
                                @if ($formStatus === 'holiday')
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="applyToAll"
                                                wire:model="formApplyToAll">
                                            <label class="form-check-label" for="applyToAll">
                                                ثبت این تعطیلی برای <strong>همه کارمندان</strong> (روزهایی که قبلاً
                                                برایشان رکورد دارند تغییر نمی‌کند)
                                            </label>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label">ورود</label>
                                    <input type="text" class="form-control time-24" wire:model="formCheckIn"
                                        placeholder="14:30" maxlength="5" inputmode="numeric" autocomplete="off">
                                    <div class="form-text">۲۴ ساعته، مثال: 08:30</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">خروج</label>
                                    <input type="text" class="form-control time-24" wire:model="formCheckOut"
                                        placeholder="17:30" maxlength="5" inputmode="numeric" autocomplete="off">
                                    <div class="form-text">۲۴ ساعته، مثال: 17:30</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">تأخیر (دقیقه)</label>
                                    <input type="number" class="form-control" wire:model="formLateMinutes"
                                        min="0" step="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">اضافه‌کاری (ساعت)</label>
                                    <input type="number" class="form-control" wire:model="formOvertimeHours"
                                        min="0" step="0.5">
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

    {{-- مودال گزارش کامل ماه --}}
    @if ($showReportModal && $reportEmployee)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);"
            wire:key="att-report-modal">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-file-earmark-text text-primary"></i>
                            گزارش کامل {{ $reportEmployee->full_name }}
                            <span class="text-muted small">— {{ $monthTitle }}</span>
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge att-legend att-present">حاضر: {{ $reportTotals['present'] }}</span>
                            <span class="badge att-legend att-absent">غایب: {{ $reportTotals['absent'] }}</span>
                            <span class="badge att-legend att-leave">مرخصی: {{ $reportTotals['leave'] }}</span>
                            <span class="badge att-legend att-half">نیمه‌وقت: {{ $reportTotals['half'] }}</span>
                            <span class="badge att-legend att-holiday">تعطیل: {{ $reportTotals['holiday'] }}</span>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">جمع کارکرد:
                                {{ number_format($reportTotals['worked'], 1) }} ساعت</span>
                            <span
                                class="badge bg-secondary-subtle text-secondary-emphasis">اضافه‌کاری:
                                {{ number_format($reportTotals['overtime'], 1) }} ساعت</span>
                            <span
                                class="badge bg-secondary-subtle text-secondary-emphasis">تأخیر:
                                {{ number_format($reportTotals['late'], 0) }} دقیقه</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">روز</th>
                                        <th class="text-center">تاریخ</th>
                                        <th class="text-center">وضعیت</th>
                                        <th class="text-center">ورود</th>
                                        <th class="text-center">خروج</th>
                                        <th class="text-center">کارکرد (ساعت)</th>
                                        <th class="text-center">تأخیر</th>
                                        <th class="text-center">اضافه‌کاری</th>
                                        <th>یادداشت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportRows as $row)
                                        <tr @if (!$row['rec']) style="opacity:.45" @endif>
                                            <td class="text-center">{{ $row['day'] }}</td>
                                            <td class="text-center small">{{ $row['jalali'] }}
                                                <div class="text-muted" style="font-size:.7rem">{{ $row['weekday'] }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if ($row['rec'])
                                                    <span
                                                        class="badge att-legend att-{{ $row['rec']->status }}">{{ $row['rec']->statusText() }}</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center small">{{ $row['rec']->check_in ?? '—' }}</td>
                                            <td class="text-center small">{{ $row['rec']->check_out ?? '—' }}</td>
                                            <td class="text-center small">
                                                {{ $row['worked'] !== null ? number_format($row['worked'], 1) : '—' }}
                                            </td>
                                            <td class="text-center small">
                                                {{ $row['rec'] && $row['rec']->late_minutes > 0 ? number_format((float) $row['rec']->late_minutes, 0) . ' دقیقه' : '—' }}
                                            </td>
                                            <td class="text-center small">
                                                {{ $row['rec'] && $row['rec']->overtime_hours > 0 ? number_format((float) $row['rec']->overtime_hours, 1) . ' ساعت' : '—' }}
                                            </td>
                                            <td class="small text-muted">{{ $row['rec']->notes ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
