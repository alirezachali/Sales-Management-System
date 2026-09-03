<?php

namespace App\Livewire\Reports;

use App\Models\Sale;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReport extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /*
     * |--------------------------------------------------------------------|
     * |                              فیلترها                                |
     * |--------------------------------------------------------------------|
     */
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public string $filterPaymentType = '';

    /*
     * ورودی‌های شمسی کاربر (مثل 1405/06/11) برای نمایش و انتخاب.
     * معادل میلادی در dateFrom/dateTo سمت سرور نگه‌داری می‌شود.
     * خالی بودن یعنی بدون فیلتر تاریخ.
     */
    public string $dateFromJalali = '';
    public string $dateToJalali = '';

    /** خطاهای اعتبارسنجی تاریخ (کلیدها: from ،to ،range) */
    public array $dateErrors = [];

    public array $paymentTypeLabels = [
        'cash' => 'نقدی',
        'card' => 'کارت',
        'mixed' => 'ترکیبی',
        'credit' => 'نسیه',
    ];

    public function updatedDateFromJalali(): void
    {
        $this->syncJalaliFilter('dateFromJalali', 'dateFrom', 'from');
        $this->resetPage();
    }

    public function updatedDateToJalali(): void
    {
        $this->syncJalaliFilter('dateToJalali', 'dateTo', 'to');
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------|
    | همگام‌سازی ورودی شمسی با معادل میلادی سمت سرور. ورودی خالی یعنی    |
    | بدون فیلتر؛ ورودی نامعتبر فیلتری اعمال نمی‌کند و خطا نمایش می‌دهد. |
    |--------------------------------------------------------------------|
    */
    private function syncJalaliFilter(string $jalaliProp, string $gregorianProp, string $errorKey): void
    {
        $value = trim($this->{$jalaliProp});

        if ($value === '') {
            $this->{$gregorianProp} = null;
            unset($this->dateErrors[$errorKey]);
            unset($this->dateErrors['range']);
            return;
        }

        $gregorian = jalaliToGregorian($value);

        if ($gregorian === null) {
            $this->{$gregorianProp} = null;
            $this->dateErrors[$errorKey] = 'تاریخ معتبر نیست. مثال درست: 1405/06/11';
            return;
        }

        unset($this->dateErrors[$errorKey]);

        // نرمال‌سازی قالب نمایش (مثل 1405/6/1 به 1405/06/01)
        $normalized = gregorianToJalaliInput($gregorian) ?? $value;
        if ($normalized !== $this->{$jalaliProp}) {
            $this->{$jalaliProp} = $normalized;
        }

        $this->{$gregorianProp} = $gregorian;

        if ($this->dateFrom !== null && $this->dateTo !== null && $this->dateFrom > $this->dateTo) {
            $this->dateErrors['range'] = 'تاریخ شروع نمی‌تواند بعد از تاریخ پایان باشد.';
        } else {
            unset($this->dateErrors['range']);
        }
    }

    public function updatedFilterPaymentType(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo', 'filterPaymentType', 'dateFromJalali', 'dateToJalali', 'dateErrors']);
        $this->resetPage();
    }

    /*
     * |--------------------------------------------------------------------|
     * |                              رندر                                 |
     * |--------------------------------------------------------------------|
     */
    public function render()
    {
        $sales = $this->baseQuery()
            ->withCount('items')
            ->latest('created_at')
            ->paginate(15);

        return view('livewire.reports.sales-report', [
            'sales' => $sales,
            'totals' => $this->totals(),
        ]);
    }

    /*
     * |--------------------------------------------------------------------|
     * |                         ساخت کوئری و جمع کل                        |
     * |--------------------------------------------------------------------|
     */

    /**
     * کوئری پایه با اعمال فیلترهای تاریخ و روش پرداخت.
     * بدون withCount و بدون ستون‌های تجمیعی تا برای مصارف مختلف قابل استفاده باشد.
     */
    private function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = Sale::query()
            ->with(['customer', 'user']);

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->filterPaymentType !== '') {
            $query->where('payment_type', $this->filterPaymentType);
        }

        return $query;
    }

    /**
     * محاسبه جمع‌های کل بازه‌ی فیلترشده.
     * از کوئری پایه (بدون withCount و بدون sales.*) استفاده می‌شود.
     */
    private function totals(): object
    {
        return $this->baseQuery()->selectRaw(
            'COUNT(*) as count, SUM(total_price) as total_price, SUM(discount) as discount, SUM(final_price) as final_price'
        )->first();
    }

    /*
     * |--------------------------------------------------------------------|
     * |                         خروجی اکسل و CSV                           |
     * |--------------------------------------------------------------------|
     */
    private function exportRows(): \Illuminate\Support\Collection
    {
        $query = $this->baseQuery()->withCount('items');

        return $query->latest('created_at')->get()->map(function (Sale $sale) {
            return [
                $sale->invoice_number,
                $sale->customer?->full_name ?? 'مشتری ناشناس',
                jalaliDateTime($sale->created_at),
                (int) $sale->items_count,
                number_format((float) $sale->total_price, 2),
                number_format((float) $sale->discount, 2),
                number_format((float) $sale->final_price, 2),
                $this->paymentTypeLabels[$sale->payment_type] ?? $sale->payment_type,
            ];
        });
    }

    private function exportTotalsRow(): array
    {
        $totals = $this->totals();

        return [
            'جمع کل',
            '',
            '',
            '',
            number_format((float) $totals->total_price, 2),
            number_format((float) $totals->discount, 2),
            number_format((float) $totals->final_price, 2),
            '',
        ];
    }

    private function headerLabels(): array
    {
        return [
            'شماره فاکتور',
            'مشتری',
            'تاریخ',
            'تعداد اقلام',
            'جمع کل',
            'تخفیف',
            'مبلغ نهایی',
            'روش پرداخت',
        ];
    }

    public function exportCsv(): StreamedResponse
    {
        $rows = $this->exportRows();

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // BOM برای نمایش صحیح فارسی در اکسل
            fwrite($handle, "\u{FEFF}");

            fputcsv($handle, $this->headerLabels());

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fputcsv($handle, $this->exportTotalsRow());

            fclose($handle);
        };

        $filename = 'sales-report-' . now()->format('Y-m-d-His') . '.csv';

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportExcel(): StreamedResponse
    {
        $rows = $this->exportRows();

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');

            fwrite($out, $this->spreadsheetXml($rows));

            fclose($out);
        };

        $filename = 'sales-report-' . now()->format('Y-m-d-His') . '.xls';

        return Response::stream($callback, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function spreadsheetXml(\Illuminate\Support\Collection $rows): string
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>'
. '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel" '
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"></Workbook>'
);

$worksheet = $xml->addChild('Worksheet');
$worksheet->addAttribute('ss:Name', 'گزارش فروش');

$table = $worksheet->addChild('Table');

$headerRow = $table->addChild('Row');
foreach ($this->headerLabels() as $label) {
$cell = $headerRow->addChild('Cell');
$cell->addAttribute('ss:StyleID', 'Header');
$data = $cell->addChild('Data', htmlspecialchars($label, ENT_XML1, 'UTF-8'));
$data->addAttribute('ss:Type', 'String');
}

foreach ($rows as $row) {
$tableRow = $table->addChild('Row');
foreach ($row as $value) {
$cell = $tableRow->addChild('Cell');
$data = $cell->addChild('Data', htmlspecialchars((string) $value, ENT_XML1, 'UTF-8'));
$data->addAttribute('ss:Type', 'String');
}
}

$totalRow = $table->addChild('Row');
foreach ($this->exportTotalsRow() as $value) {
$cell = $totalRow->addChild('Cell');
$cell->addAttribute('ss:StyleID', 'Bold');
$data = $cell->addChild('Data', htmlspecialchars((string) $value, ENT_XML1, 'UTF-8'));
$data->addAttribute('ss:Type', 'String');
}

return $xml->asXML();
}
}