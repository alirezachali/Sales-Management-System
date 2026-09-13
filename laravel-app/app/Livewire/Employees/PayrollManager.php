<?php

namespace App\Livewire\Employees;

use App\Livewire\Concerns\AuthorizesActions;
use App\Models\Cashbox;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\CashboxService;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PayrollManager extends Component
{
    use WithPagination;
    use AuthorizesActions;

    public string $monthJalali = ''; // 1405-06

    public array $rows = []; // employee_id => [field => value]

    public ?int $viewPayrollId = null;

    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->monthJalali = Verta::now()->format('Y-m');
    }

    public function changeMonth(string $delta): void
    {
        [$y, $m] = array_map('intval', explode('-', $this->monthJalali));

        $v = Verta::parse($y.'-'.str_pad((string) $m, 2, '0', STR_PAD_LEFT).'-01');
        $v = $delta === 'prev' ? $v->copy()->subMonth() : $v->copy()->addMonth();

        $this->monthJalali = $v->format('Y-m');
        $this->loadRows();
    }

    public function updatedMonthJalali(): void
    {
        $this->loadRows();
    }

    protected function monthPeriod(): array
    {
        [$y, $m] = array_map('intval', explode('-', $this->monthJalali));

        $first = Verta::parse($y.'-'.str_pad((string) $m, 2, '0', STR_PAD_LEFT).'-01');

        return [
            'year' => $y,
            'month' => $m,
            'start' => $first->toCarbon()->toDateString(),
            'end' => $first->copy()->endMonth()->toCarbon()->toDateString(),
        ];
    }

    /**
     * ساخت ردیف‌های محاسبه‌شده برای همه کارکنان فعال.
     */
    public function loadRows(): void
    {
        $period = $this->monthPeriod();
        $this->rows = [];

        $employees = Employee::active()->orderBy('first_name')->get();

        $attendance = DB::table('attendance_records')
            ->selectRaw('employee_id,
                SUM(CASE WHEN status = \'present\' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN status = \'absent\' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN status = \'leave\' THEN 1 ELSE 0 END) as leave_days,
                SUM(CASE WHEN status = \'half\' THEN 1 ELSE 0 END) as half_days,
                SUM(overtime_hours) as total_overtime')
            ->whereBetween('date', [$period['start'], $period['end']])
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        $existing = Payroll::where('year', $period['year'])
            ->where('month', $period['month'])
            ->get()
            ->keyBy('employee_id');

        foreach ($employees as $employee) {
            $att = $attendance->get($employee->id);
            $payroll = $existing->get($employee->id);

            $baseSalary = (float) ($payroll?->base_salary ?? $employee->base_salary);
            $workdays = (int) (($att->present_days ?? 0) + ($att->half_days ?? 0) * 0.5 + ($att->leave_days ?? 0));
            $absentDays = (int) ($att->absent_days ?? 0);
            $overtimeHours = (float) ($att->total_overtime ?? 0);

            $dailyWage = $baseSalary / 30;
            $hourlyWage = $dailyWage / 8;
            $absentDeduction = round($dailyWage * $absentDays);
            $overtimeAmount = round($overtimeHours * $hourlyWage * 1.4); // ۴۰٪ بیشتر از نرخ ساعتی

            $this->rows[$employee->id] = [
                'name' => $employee->full_name,
                'job_title' => $employee->job_title,
                'payroll_id' => $payroll?->id,
                'status' => $payroll?->status ?? 'draft',
                'editable' => ! $payroll || $payroll->status === 'draft',
                'base_salary' => (float) ($payroll?->base_salary ?? $employee->base_salary),
                'workdays' => $payroll ? (int) $payroll->workdays : $workdays,
                'absent_days' => $payroll ? (int) $payroll->absent_days : $absentDays,
                'leave_days' => $payroll ? (int) $payroll->leave_days : (int) ($att->leave_days ?? 0),
                'overtime_hours' => $overtimeHours,
                'overtime_amount' => (float) ($payroll?->overtime_amount ?? $overtimeAmount),
                'bonus' => (float) ($payroll?->bonus ?? 0),
                'allowances' => (float) ($payroll?->allowances ?? 0),
                'deductions' => (float) ($payroll?->deductions ?? $absentDeduction),
                'insurance' => (float) ($payroll?->insurance ?? 0),
                'tax' => (float) ($payroll?->tax ?? 0),
            ];
        }
    }

    public function getNetPay(int $employeeId): float
    {
        $row = $this->rows[$employeeId] ?? [];

        $gross = (float) ($row['base_salary'] ?? 0)
            + (float) ($row['overtime_amount'] ?? 0)
            + (float) ($row['bonus'] ?? 0)
            + (float) ($row['allowances'] ?? 0);

        $deductions = (float) ($row['deductions'] ?? 0)
            + (float) ($row['insurance'] ?? 0)
            + (float) ($row['tax'] ?? 0);

        return max(0, $gross - $deductions);
    }

    public function calculate(): void
    {
        $this->authorizeAction('payrolls.create');

        $this->loadRows();
        session()->flash('success', 'محاسبات از روی حضور و غیاب ماه بازخوانی شد.');
    }

    public function save(): void
    {
        $this->authorizeAction('payrolls.create');

        $period = $this->monthPeriod();
        $count = 0;

        DB::transaction(function () use ($period, &$count) {
            foreach ($this->rows as $employeeId => $row) {
                if (! ($row['editable'] ?? false)) {
                    continue;
                }

                Payroll::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'year' => $period['year'],
                        'month' => $period['month'],
                    ],
                    [
                        'base_salary' => (float) $row['base_salary'],
                        'overtime_amount' => (float) $row['overtime_amount'],
                        'bonus' => (float) $row['bonus'],
                        'allowances' => (float) $row['allowances'],
                        'deductions' => (float) $row['deductions'],
                        'insurance' => (float) $row['insurance'],
                        'tax' => (float) $row['tax'],
                        'workdays' => (int) $row['workdays'],
                        'absent_days' => (int) $row['absent_days'],
                        'leave_days' => (int) $row['leave_days'],
                        'created_by' => auth()->id(),
                    ]
                );

                $count++;
            }
        });

        // محاسبه خالص برای همه فیش‌های ماه
        Payroll::where('year', $period['year'])
            ->where('month', $period['month'])
            ->each(function (Payroll $p) {
                $p->recalculateNetPay();
                $p->save();
            });

        $this->loadRows();
        session()->flash('success', $count.' فیش حقوقی ذخیره شد.');
    }

    public function approve(int $id): void
    {
        $this->authorizeAction('payrolls.approve');

        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'draft') {
            session()->flash('error', 'فقط فیش پیش‌نویس قابل تأیید است.');

            return;
        }

        $payroll->recalculateNetPay();
        $payroll->update(['status' => 'approved']);
        session()->flash('success', 'فیش حقوقی تأیید شد.');
        $this->loadRows();
    }

    public function pay(int $id, CashboxService $cashboxService): void
    {
        $this->authorizeAction('payrolls.pay');

        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'approved') {
            session()->flash('error', 'ابتدا فیش را تأیید کنید.');

            return;
        }

        DB::transaction(function () use ($payroll, $cashboxService) {
            $cashbox = Cashbox::where('type', 'cash')->where('is_active', true)
                ->orderByDesc('is_default')->first();

            if (! $cashbox || (float) $cashbox->balance < (float) $payroll->net_pay) {
                $cashbox = Cashbox::active()->whereRaw('balance >= ?', [$payroll->net_pay])
                    ->orderByDesc('balance')->first();
            }

            if (! $cashbox) {
                throw new \RuntimeException('صندوقی با موجودی کافی برای پرداخت این حقوق وجود ندارد.');
            }

            $cashboxService->transaction(
                $cashbox,
                'withdraw',
                (float) $payroll->net_pay,
                'حقوق '.$payroll->periodLabel().' — '.$payroll->employee?->full_name,
                $payroll,
            );

            $payroll->update([
                'status' => 'paid',
                'cashbox_id' => $cashbox->id,
            ]);
        });

        session()->flash('success', 'حقوق پرداخت و از صندوق کسر شد.');
        $this->loadRows();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $this->authorizeAction('payrolls.edit');

        $payroll = Payroll::findOrFail($this->deletingId);

        if ($payroll->status === 'paid') {
            session()->flash('error', 'فیش پرداخت‌شده قابل حذف نیست.');
            $this->showDeleteModal = false;

            return;
        }

        $payroll->delete();
        session()->flash('success', 'فیش حذف شد.');
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->loadRows();
    }

    public function printSlip(int $id): void
    {
        $this->viewPayrollId = $id;
    }

    public function closeModals(): void
    {
        $this->showDeleteModal = false;
        $this->viewPayrollId = null;
    }

    public function getTotalPayableProperty(): float
    {
        return collect(array_keys($this->rows))->sum(fn ($id) => $this->getNetPay((int) $id));
    }

    public function render()
    {
        if (empty($this->rows)) {
            $this->loadRows();
        }

        $payrolls = Payroll::with(['employee:id,first_name,last_name', 'cashbox:id,name'])
            ->where('year', $this->monthPeriod()['year'])
            ->where('month', $this->monthPeriod()['month'])
            ->get();

        return view('livewire.employees.payroll-manager', [
            'payrolls' => $payrolls,
            'monthTitle' => str_replace('-', '/', $this->monthJalali),
        ]);
    }
}
