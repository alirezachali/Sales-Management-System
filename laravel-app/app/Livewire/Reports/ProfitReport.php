<?php

namespace App\Livewire\Reports;

use App\Livewire\Concerns\AuthorizesActions;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Sale;
use App\Models\SaleItem;
use Hekmatinasser\Verta\Verta;
use Livewire\Component;

class ProfitReport extends Component
{
    use AuthorizesActions;

    public string $monthJalali = ''; // 1405-06

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
    }

    protected function period(): array
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

    public function render()
    {
        $this->authorizeAction('reports.profit');

        $period = $this->period();


        // کدی که تغییر دادم
        $sales = Sale::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$period['start'].' 00:00:00', $period['end'].' 23:59:59'])
            ->with('items');


        $revenue = (float) (clone $sales)->sum('final_price');
        $discounts = (float) (clone $sales)->sum('discount');
        $invoiceCount = (clone $sales)->count();

        $cogs = (float) SaleItem::whereIn('sale_id', (clone $sales)->pluck('id'))->sum(DB::raw('cost_price * quantity'));

        $operatingExpenses = (float) Expense::whereBetween('expense_date', [$period['start'], $period['end']])->sum('amount');

        $payrollCost = (float) Payroll::where('year', $period['year'])->where('month', $period['month'])->sum('net_pay');

        $grossProfit = $revenue - $cogs;
        $netProfit = $grossProfit - $operatingExpenses - $payrollCost;
        $margin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;

        $trend = $this->buildTrend($period['year'], $period['month']);

        $topProducts = $this->buildTopProducts((clone $sales)->pluck('id'));

        return view('livewire.reports.profit-report', [
            'period' => $period,
            'monthTitle' => str_replace('-', '/', $this->monthJalali),
            'revenue' => $revenue,
            'discounts' => $discounts,
            'cogs' => $cogs,
            'grossProfit' => $grossProfit,
            'operatingExpenses' => $operatingExpenses,
            'payrollCost' => $payrollCost,
            'netProfit' => $netProfit,
            'margin' => $margin,
            'invoiceCount' => $invoiceCount,
            'trend' => $trend,
            'topProducts' => $topProducts,
        ]);
    }

    private function buildTrend(int $year, int $month): array
    {
        $trend = [];

        $v = Verta::parse($year.'-'.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'-01');

        for ($i = 11; $i >= 0; $i--) {
            $m = $v->copy()->subMonths($i);
            [$y2, $m2] = array_map('intval', [$m->format('Y'), $m->format('m')]);
            $start = $m->copy()->startMonth()->toCarbon()->toDateString();
            $end = $m->copy()->endMonth()->toCarbon()->toDateString();

            // کدی که تغییر دادم
            $sales = Sale::where('status', '!=', 'cancelled')
            ->whereBetween('sales.created_at', [$start.' 00:00:00', $end.' 23:59:59']);


            $revenue = (float) (clone $sales)->sum('final_price');
            $saleIds = (clone $sales)->pluck('id');

            $cogs = $saleIds->isNotEmpty()
                ? (float) SaleItem::whereIn('sale_id', $saleIds)->sum(DB::raw('cost_price * quantity'))
                : 0;

            $expenses = (float) Expense::whereBetween('expense_date', [$start, $end])->sum('amount');
            $payroll = (float) Payroll::where('year', $y2)->where('month', $m2)->sum('net_pay');

            $trend[] = [
                'label' => $y2.'/'.str_pad((string) $m2, 2, '0', STR_PAD_LEFT),
                'revenue' => $revenue,
                'cost' => $cogs + $expenses + $payroll,
                'profit' => $revenue - $cogs - $expenses - $payroll,
            ];
        }

        return $trend;
    }

    private function buildTopProducts($saleIds): array
    {
        if ($saleIds->isEmpty()) {
            return [];
        }

        return SaleItem::query()
            ->whereIn('sale_id', $saleIds)
            ->with('product:id,name,unit')
            ->selectRaw('product_id, SUM(line_total) as revenue, SUM(cost_price * quantity) as cost')
            ->groupBy('product_id')
            ->orderByRaw('(SUM(line_total) - SUM(cost_price * quantity)) DESC')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => $row->product?->name ?? 'â€”',
                    'revenue' => (float) $row->revenue,
                    'profit' => (float) $row->revenue - (float) $row->cost,
                ];
            })
            ->all();
    }
}
