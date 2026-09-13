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

    public array $records = []; // product of employee_id => date => record

    // فرم ثبت سریع
    public ?int $formEmployeeId = null;
    public string $formDateJalali = '';
    public string $formStatus = 'present';
    public ?string $formCheckIn = null;
    public ?string $formCheckOut = null;
    public $formLateMinutes = 0;
    public $formOvertimeHours = 0;
    public ?string $formNotes = null;

    public bool $showFormModal = false;

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
        $this->showFormModal = true;
    }

    public function openFormFor(int $employeeId, string $date): void
    {
        $this->authorizeAction('attendance.edit');

        $record = AttendanceRecord::where('employee_id', $employeeId)->where('date', $date)->first();

        $this->formEmployeeId = $employeeId;

        if ($record) {
            $this->formStatus = $record->status;
            $this->formCheckIn = $record->check_in;
            $this->formCheckOut = $record->check_out;
            $this->formLateMinutes = (float) $record->late_minutes;
            $this->formOvertimeHours = (float) $record->overtime_hours;
            $this->formNotes = $record->notes;
        } else {
            $this->reset(['formStatus', 'formCheckIn', 'formCheckOut', 'formLateMinutes', 'formOvertimeHours', 'formNotes']);
            $this->formStatus = 'present';
        }

        $this->formDateJalali = gregorianToJalaliInput($date) ?? $date;
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

        session()->flash('success', 'سابقه حضور ثبت شد.');
        $this->showFormModal = false;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
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
