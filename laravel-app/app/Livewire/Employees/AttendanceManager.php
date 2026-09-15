<?php

namespace App\Livewire\Employees;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AttendanceManager extends Component
{
    use AuthorizesActions;

    public string $monthJalali = ''; // 1405-06

    public string $employeeFilter = '';

    // فرم ثبت سریع
    public ?int $formEmployeeId = null;
    public string $formDateJalali = '';
    public string $formStatus = 'present';
    public ?string $formCheckIn = null;
    public ?string $formCheckOut = null;
    public $formLateMinutes = 0;
    public $formOvertimeHours = 0;
    public ?string $formNotes = null;
    public bool $formApplyToAll = false;

    public bool $showFormModal = false;

    // مودال گزارش کامل ماه
    public bool $showReportModal = false;
    public ?int $reportEmployeeId = null;

    public function mount(): void
    {
        $this->monthJalali = Verta::now()->format('Y-m');
        $this->formDateJalali = Verta::now()->format('Y-m-d');
    }

    public function getMonthRange(): array
    {
        [$y, $m] = array_map('intval', explode('-', $this->monthJalali));

        $first = Verta::parse($y.'-'.str_pad((string) $m, 2, '0', STR_PAD_LEFT).'-01');
        $start = $first->toCarbon()->toDateString();
        $end = $first->copy()->endMonth()->toCarbon()->toDateString();

        return [$start, $end];
    }

    public function daysInMonth(): int
    {
        [$y, $m] = array_map('intval', explode('-', $this->monthJalali));

        $first = Verta::parse($y.'-'.str_pad((string) $m, 2, '0', STR_PAD_LEFT).'-01');

        return (int) $first->copy()->endMonth()->format('j');
    }

    public function changeMonth(string $delta): void
    {
        [$y, $m] = array_map('intval', explode('-', $this->monthJalali));

        $v = Verta::parse($y.'-'.str_pad((string) $m, 2, '0', STR_PAD_LEFT).'-01');
        $v = $delta === 'prev' ? $v->copy()->subMonth() : $v->copy()->addMonth();

        $this->monthJalali = $v->format('Y-m');
    }

    public function openForm(int $employeeId): void
    {
        $this->authorizeAction('attendance.edit');

        $this->reset(['formStatus', 'formCheckIn', 'formCheckOut', 'formLateMinutes', 'formOvertimeHours', 'formNotes']);
        $this->formEmployeeId = $employeeId;
        $this->formStatus = 'present';
        $this->formDateJalali = Verta::now()->format('Y-m-d');
        $this->formApplyToAll = false;
        $this->showFormModal = true;
    }

    public function openFormFor(int $employeeId, string $date): void
    {
        $this->authorizeAction('attendance.edit');

        $record = AttendanceRecord::where('employee_id', $employeeId)->where('date', $date)->first();

        $this->formEmployeeId = $employeeId;

        if ($record) {
            $this->formStatus = $record->status;
            $this->formCheckIn = $this->normalizeTime($record->check_in);
            $this->formCheckOut = $this->normalizeTime($record->check_out);
            $this->formLateMinutes = (float) $record->late_minutes;
            $this->formOvertimeHours = (float) $record->overtime_hours;
            $this->formNotes = $record->notes;
        } else {
            $this->reset(['formStatus', 'formCheckIn', 'formCheckOut', 'formLateMinutes', 'formOvertimeHours', 'formNotes']);
            $this->formStatus = 'present';
        }

        $this->formDateJalali = gregorianToJalaliInput($date) ?? $date;
        $this->formApplyToAll = false;
        $this->showFormModal = true;
    }

    public function save(): void
    {
        $this->authorizeAction('attendance.edit');

        $gregorian = jalaliToGregorian($this->formDateJalali);

        if ($gregorian === null) {
            $this->addError('formDateJalali', 'تاریخ وارد‌شده معتبر نیست. مثال: 1405-06-01');

            return;
        }

        $this->formCheckIn = $this->normalizeTime($this->formCheckIn);
        $this->formCheckOut = $this->normalizeTime($this->formCheckOut);

        $this->validate([
            'formStatus' => ['required', Rule::in(['present', 'absent', 'leave', 'half', 'holiday'])],
            'formCheckIn' => ['nullable', 'date_format:H:i'],
            'formCheckOut' => ['nullable', 'date_format:H:i'],
            'formLateMinutes' => ['nullable', 'numeric', 'min:0'],
            'formOvertimeHours' => ['nullable', 'numeric', 'min:0'],
        ]);

        AttendanceRecord::updateOrCreate(
            ['employee_id' => $this->formEmployeeId, 'date' => $gregorian],
            [
                'status' => $this->formStatus,
                'check_in' => $this->formCheckIn,
                'check_out' => $this->formCheckOut,
                'late_minutes' => (float) ($this->formLateMinutes ?: 0),
                'overtime_hours' => (float) ($this->formOvertimeHours ?: 0),
                'notes' => $this->formNotes,
            ],
        );

        $message = 'سابقه حضور ثبت شد.';

        if ($this->formStatus === 'holiday' && $this->formApplyToAll) {
            $existing = AttendanceRecord::where('date', $gregorian)->pluck('employee_id')->all();

            $missingIds = Employee::active()
                ->whereNotIn('id', $existing)
                ->pluck('id');

            if ($missingIds->isNotEmpty()) {
                DB::transaction(function () use ($missingIds, $gregorian) {
                    $now = now();
                    $rows = $missingIds->map(fn ($id) => [
                        'employee_id' => $id,
                        'date' => $gregorian,
                        'status' => 'holiday',
                        'notes' => $this->formNotes,
                        'late_minutes' => 0,
                        'overtime_hours' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();

                    AttendanceRecord::insert($rows);
                });

                $message = 'تعطیلی برای '.($missingIds->count() + 1).' کارمند ثبت شد.';
            }
        }

        session()->flash('success', $message);
        $this->showFormModal = false;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showReportModal = false;
    }

    private function normalizeTime(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // تبدیل ارقام فارسی/عربی به لاتین
        $value = str_replace(['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹',
            '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'], range(0, 9), $value);

        if (preg_match('/^(\d{1,2}):(\d{1,2})(:\d{1,2})?$/', $value, $m)) {
            return str_pad($m[1], 2, '0', STR_PAD_LEFT).':'.str_pad($m[2], 2, '0', STR_PAD_LEFT);
        }

        return $value;
    }

    public function openReport(int $employeeId): void
    {
        $this->reportEmployeeId = $employeeId;
        $this->showReportModal = true;
    }

    public function buildReport(): array
    {
        if (! $this->showReportModal || ! $this->reportEmployeeId) {
            return ['reportRows' => collect(), 'reportEmployee' => null, 'reportTotals' => []];
        }

        $employee = Employee::find($this->reportEmployeeId,
            ['id', 'first_name', 'last_name', 'job_title']);

        [$start, $end] = $this->monthRangeSafe();
        $daysInMonth = $this->daysInMonthSafe();

        $records = AttendanceRecord::where('employee_id', $this->reportEmployeeId)
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(fn ($r) => $r->date->format('Y-m-d'));

        $rows = collect();
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $jalali = $this->monthJalali . '-' . str_pad((string) $d, 2, '0', STR_PAD_LEFT);
            $greg = \Hekmatinasser\Verta\Verta::parse($jalali)->toCarbon()->toDateString();
            $rec = $records->get($greg);

            $worked = null;
            if ($rec?->check_in && $rec?->check_out) {
                $in = \Carbon\Carbon::parse($greg . ' ' . $rec->check_in);
                $out = \Carbon\Carbon::parse($greg . ' ' . $rec->check_out);
                if ($out->lt($in)) {
                    $out->addDay();
                }
                $worked = round($in->diffInHours($out), 2);
            }

            $rows->push([
                'day' => $d,
                'jalali' => $jalali,
                'weekday' => \Hekmatinasser\Verta\Verta::parse($jalali)->format('l'),
                'rec' => $rec,
                'worked' => $worked,
            ]);
        }

        $totals = [
            'present' => $rows->filter(fn ($r) => $r['rec']?->status === 'present')->count(),
            'absent' => $rows->filter(fn ($r) => $r['rec']?->status === 'absent')->count(),
            'leave' => $rows->filter(fn ($r) => $r['rec']?->status === 'leave')->count(),
            'half' => $rows->filter(fn ($r) => $r['rec']?->status === 'half')->count(),
            'holiday' => $rows->filter(fn ($r) => $r['rec']?->status === 'holiday')->count(),
            'overtime' => round($rows->sum(fn ($r) => (float) ($r['rec']->overtime_hours ?? 0)), 2),
            'late' => round($rows->sum(fn ($r) => (float) ($r['rec']->late_minutes ?? 0)), 1),
            'worked' => round($rows->sum(fn ($r) => (float) ($r['worked'] ?? 0)), 2),
        ];

        return [
            'reportRows' => $rows,
            'reportEmployee' => $employee,
            'reportTotals' => $totals,
        ];
    }

    public function render()
    {
        [$start, $end] = $this->monthRangeSafe();

        $employees = Employee::active()
            ->when($this->employeeFilter !== '', fn ($q) => $q->search($this->employeeFilter))
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'job_title']);

        $records = AttendanceRecord::with('employee:id,first_name,last_name')
            ->whereBetween('date', [$start, $end])
            ->when($this->employeeFilter !== '', fn ($q) => $q->whereIn('employee_id', $employees->pluck('id')))
            ->get()
            ->groupBy(fn ($r) => $r->employee_id.'|'.$r->date->format('Y-m-d'));

        $summary = AttendanceRecord::selectRaw('employee_id,
                SUM(CASE WHEN status = \'present\' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = \'absent\' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = \'leave\' THEN 1 ELSE 0 END) as leave_days,
                SUM(CASE WHEN status = \'half\' THEN 1 ELSE 0 END) as half_days,
                SUM(overtime_hours) as total_overtime,
                SUM(late_minutes) as total_late')
            ->whereBetween('date', [$start, $end])
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        $daysInMonth = $this->daysInMonthSafe();
        $days = range(1, $daysInMonth);

        // عنوان ماه جاری برای هدر
        [$y, $m] = array_map('intval', explode('-', $this->monthJalali));
        $monthTitle = $y.'/'.str_pad((string) $m, 2, '0', STR_PAD_LEFT);

        return view('livewire.employees.attendance-manager', [
            'employees' => $employees,
            'records' => $records,
            'summary' => $summary,
            'days' => $days,
            'start' => $start,
            'end' => $end,
            'monthTitle' => $monthTitle,
            ...$this->buildReport(),
        ]);
    }

    private function monthRangeSafe(): array
    {
        try {
            return $this->getMonthRange();
        } catch (\Throwable) {
            $this->monthJalali = Verta::now()->format('Y-m');

            return $this->getMonthRange();
        }
    }

    private function daysInMonthSafe(): int
    {
        try {
            return $this->daysInMonth();
        } catch (\Throwable) {
            $this->monthJalali = Verta::now()->format('Y-m');

            return $this->daysInMonth();
        }
    }
}
