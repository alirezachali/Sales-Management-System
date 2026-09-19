<div dir="{{ app()->getLocale() === 'fa' ? 'rtl' : 'ltr' }}">

    @include('partials.flash-messages')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div class="btn-group" role="group">
            <button class="btn btn-outline-secondary" wire:click="changeMonth('prev')"><i class="bi bi-chevron-right"></i></button>
            <button class="btn btn-secondary text-white fw-bold">ماه {{ $monthTitle }}</button>
            <button class="btn btn-outline-secondary" wire:click="changeMonth('next')"><i class="bi bi-chevron-left"></i></button>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            <div class="card stat-card border-0 py-1 px-3">
                <span class="small text-muted">جمع پرداختی ماه: </span>
                <span class="fw-bold text-success">{{ number_format($this->totalPayable) }}</span>
                <span class="small text-muted">تومان</span>
            </div>
            @can('payrolls.create')
                <button class="btn btn-outline-info" wire:click="calculate"><i class="bi bi-calculator"></i> محاسبه از حضور و غیاب</button>
                <button class="btn btn-primary glow-btn" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                    <i class="bi bi-save"></i> ذخیره فیش‌ها
                </button>
            @endcan
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-cash-coin text-success"></i>
                    حقوق و دستمزد 💲 برج 👈  
                    <span class="text-success">{{ $monthTitle }}</span>
                </h3>
                <small class="text-muted">
                    مقادیر از حقوق پایه کارکنان و رکوردهای حضور/غیاب/اضافه‌کاری آن ماه به‌صورت خودکار پیش‌بینی می‌شوند؛ قابل ویرایش هستند.
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            @if (empty($rows))
                <div class="text-center text-muted py-4">کارمند فعالی برای محاسبه وجود ندارد.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="min-width:160px">کارمند</th>
                                <th width="120">حقوق پایه</th>
                                <th width="60">روز</th>
                                <th width="60">غیبت</th>
                                <th width="65">اضافه‌کار(ساعت)</th>
                                <th width="110">مزایا+پاداش</th>
                                <th width="100">کسری</th>
                                <th width="90">بیمه</th>
                                <th width="80">مالیات</th>
                                <th width="120" class="text-success">خالص</th>
                                <th width="190" class="text-center">وضعیت / عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $employeeId => $row)
                                @php $payroll = $payrolls->firstWhere('employee_id', $employeeId); @endphp
                                <tr wire:key="pr-{{ $employeeId }}">
                                    <td>
                                        <div class="fw-semibold small">{{ $row['name'] }}</div>
                                        <div class="text-muted" style="font-size:.7rem">{{ $row['job_title'] }}</div>
                                    </td>
                                    <td>
                                        @if ($row['editable'])
                                            <input type="number" class="form-control form-control-sm" wire:model.live="rows.{{ $employeeId }}.base_salary" step="1000">
                                        @else
                                            <span class="small">{{ number_format($row['base_salary']) }}</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-success-subtle text-success-emphasis">{{ $row['workdays'] }}</span></td>
                                    <td><span class="badge bg-danger-subtle text-danger-emphasis">{{ $row['absent_days'] }}</span></td>
                                    <td><span class="small">{{ rtrim(rtrim(number_format($row['overtime_hours'], 1, '.', ''), '0'), '.') }}</span></td>
                                    <td>
                                        @if ($row['editable'])
                                            <div class="d-flex gap-1">
                                                <input type="number" class="form-control form-control-sm" wire:model.live="rows.{{ $employeeId }}.bonus" placeholder="پاداش" step="1000">
                                                <input type="number" class="form-control form-control-sm" wire:model.live="rows.{{ $employeeId }}.allowances" placeholder="مزایا" step="1000">
                                            </div>
                                        @else
                                            <span class="small">{{ number_format($row['bonus'] + $row['allowances']) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row['editable'])
                                            <input type="number" class="form-control form-control-sm" wire:model.live="rows.{{ $employeeId }}.deductions" step="1000">
                                        @else
                                            <span class="small">{{ number_format($row['deductions']) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row['editable'])
                                            <input type="number" class="form-control form-control-sm" wire:model.live="rows.{{ $employeeId }}.insurance" step="1000">
                                        @else
                                            <span class="small">{{ number_format($row['insurance']) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row['editable'])
                                            <input type="number" class="form-control form-control-sm" wire:model.live="rows.{{ $employeeId }}.tax" step="1000">
                                        @else
                                            <span class="small">{{ number_format($row['tax']) }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-success">{{ number_format($this->getNetPay($employeeId)) }}</td>
                                    <td class="text-center">
                                        @if ($payroll)
                                            <span class="badge bg-{{ $payroll->statusColor() }}-subtle text-{{ $payroll->statusColor() }}-emphasis mb-1">{{ $payroll->statusText() }}</span>
                                            <div class="d-flex gap-1 justify-content-center">
                                                <button class="btn btn-sm btn-outline-info" wire:click="printSlip({{ $payroll->id }})" title="فیش">
                                                    <i class="bi bi-receipt"></i>
                                                </button>
                                                @if ($payroll->status === 'draft')
                                                    @can('payrolls.approve')
                                                        <button class="btn btn-sm btn-outline-primary" wire:click="approve({{ $payroll->id }})" title="تأیید">
                                                            <i class="bi bi-check2"></i>
                                                        </button>
                                                    @endcan
                                                    @can('payrolls.edit')
                                                        <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $payroll->id }})" title="حذف">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endcan
                                                @elseif ($payroll->status === 'approved')
                                                    @can('payrolls.pay')
                                                        <button class="btn btn-sm btn-success" wire:click="pay({{ $payroll->id }})" wire:confirm="پرداخت حقوق از صندوق و ثبت گردش؟">
                                                            <i class="bi bi-cash-stack"></i> پرداخت
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                        @else
                                            <span class="small text-muted">ذخیره نشده</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- فیش حقوقی --}}
    @if ($viewPayrollId && $payroll = $payrolls->firstWhere('id', $viewPayrollId))
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="slip-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">فیش حقوقی — {{ $payroll->employee?->full_name }} ({{ $payroll->periodLabel() }})</h5>
                        <button type="button" class="btn-close" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr><td>حقوق پایه</td><td class="text-start">{{ number_format((float) $payroll->base_salary) }}</td></tr>
                                <tr><td>اضافه‌کاری</td><td class="text-start">{{ number_format((float) $payroll->overtime_amount) }}</td></tr>
                                <tr><td>پاداش</td><td class="text-start">{{ number_format((float) $payroll->bonus) }}</td></tr>
                                <tr><td>سایر مزایا</td><td class="text-start">{{ number_format((float) $payroll->allowances) }}</td></tr>
                                <tr class="table-primary"><td class="fw-bold">جمع ناخالص</td><td class="text-start fw-bold">{{ number_format($payroll->grossPay()) }}</td></tr>
                                <tr><td>کسری (غیبت/مسئله)</td><td class="text-start text-danger">({{ number_format((float) $payroll->deductions) }})</td></tr>
                                <tr><td>بیمه</td><td class="text-start text-danger">({{ number_format((float) $payroll->insurance) }})</td></tr>
                                <tr><td>مالیات</td><td class="text-start text-danger">({{ number_format((float) $payroll->tax) }})</td></tr>
                                <tr class="table-success"><td class="fw-bold">خالص قابل پرداخت</td><td class="text-start fw-bold">{{ number_format((float) $payroll->net_pay) }}</td></tr>
                                <tr><td>روزهای کارکرد / غیبت / مرخصی</td><td class="text-start">{{ $payroll->workdays }} / {{ $payroll->absent_days }} / {{ $payroll->leave_days }}</td></tr>
                                <tr><td>وضعیت</td><td class="text-start"><span class="badge bg-{{ $payroll->statusColor() }}-subtle text-{{ $payroll->statusColor() }}-emphasis">{{ $payroll->statusText() }}</span>
                                    @if ($payroll->status === 'paid' && $payroll->cashbox) — صندوق: {{ $payroll->cashbox->name }} @endif
                                </td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> چاپ</button>
                        <button class="btn btn-secondary" wire:click="closeModals">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- مودال حذف --}}
    @if ($showDeleteModal)
        <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,.55);" wire:key="pr-delete-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">حذف فیش حقوقی</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModals"></button>
                    </div>
                    <div class="modal-body">این فیش حذف شود؟</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModals">انصراف</button>
                        <button class="btn btn-danger" wire:click="delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
