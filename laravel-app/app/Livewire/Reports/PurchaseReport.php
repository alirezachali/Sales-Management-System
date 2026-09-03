<?php

namespace App\Livewire\Reports;

use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseReport extends Component
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
    public string $dateFromJalali = '';
    public string $dateToJalali = '';
    public string $filterType = ''; // 'invoice', 'entry', 'exit'
    public string $filterPaymentMethod = '';

    public array $dateErrors = [];

    // public array $paymentMethodLabels = [
    //     'cash' => 'نقدی',
    //     'card' => 'کارت',
    //     'transfer' => 'کارت به کارت / حواله',
    //     'credit' => 'نسیه',
    //     'other' => 'سایر',
    // ];

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

    public function updatedFilterPaymentMethod(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo', 'filterType', 'dateFromJalali', 'dateToJalali', 'dateErrors', 'filterPaymentMethod']);
        $this->resetPage();
    }

    public function render()
    {
        // --- 1. دریافت فاکتورهای خرید ---
        // $invoiceQuery = PurchaseInvoice::query()
        //     ->with(['supplier', 'user'])
        //     ->when($this->dateFrom, fn ($q) => $q->whereDate('purchase_date', '>=', $this->dateFrom))
        //     ->when($this->dateTo, fn ($q) => $q->whereDate('purchase_date', '<=', $this->dateTo));

        // --- 2. دریافت گردش‌های انبار ---
        $movementQuery = StockMovement::query()
            ->with(['product', 'user'])
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        // فیلتر نوع operação
        if ($this->filterType === 'initial') {
            $movementQuery->where('type', 'initial');
            
        } elseif ($this->filterType === 'purchase') {
            $movementQuery->where('type', 'purchase');
            
        } elseif ($this->filterType === 'sale') {
            $movementQuery->where('type', 'sale');
        }

        // Execute both queries
        // $invoices = $invoiceQuery->get();
        $movements = $movementQuery->get();

        // --- 3. تبدیل هر کدوم به فرم یکنواخت و ترکیب ---
        $records = collect();

        // Add invoices
        // foreach ($invoices as $invoice) {
        //     $records->push([
        //         'id' => $invoice->id,
        //         'type' => 'invoice',
        //         'description' => 'فاکتور خرید از ' . $invoice->supplier->name,
        //         'related_name' => $invoice->supplier->name,
        //         'date' => \Hekmatinasser\Verta\Verta::parse($invoice->purchase_date)->format('Y/m/d'),
        //         'quantity' => 1,
        //         'total_amount' => $invoice->total_amount,
        //         'payment_method' => $invoice->payment_method,
        //         'user_name' => $invoice->user?->name ?? '',
        //         'operation' => 'خرید',
        //     ]);
        // }

        // Add movements
        foreach ($movements as $movement) {
            $records->push([
                'id' => $movement->id,
                'type' => $movement->type, // 'purchase' or 'sale'
                'description' => $movement->description,
                'related_name' => $movement->product?->name ?? '',
                'date' => \Hekmatinasser\Verta\Verta::parse($movement->created_at)->format('Y/m/d'),
                'quantity' => $movement->quantity,
                'total_amount' => null,
                'payment_method' => null,
                'user_name' => $movement->user?->name ?? '',
                'operation' => $movement->type,
            ]);
        }

        // Sort by date descending
        $records = $records->sortByDesc(function ($record) {
            $date = $record['date'];
            if ($date) {
                return strtotime(str_replace('/', '-', $date));
            }
            return 0;
        })->values();

        // Paginate manually
        $perPage = 100;
        $currentPage = $this->page ?? 1;
        $totalItems = $records->count();
        $offset = ($currentPage - 1) * $perPage;
        $currentItems = $records->slice($offset, $perPage)->all();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $totalItems,
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        $totals = $this->calculateTotals($movements);

        return view('livewire.reports.purchase-report', [
            'records' => $paginator,
            'totals' => $totals,
        ]);
    }

    /*
     |--------------------------------------------------------------------|
     * |                         محاسبه جمع کل                               |
     * |--------------------------------------------------------------------|
     */
    private function calculateTotals($movements): object
    {
        // $invoiceCount = $invoices->count();
        // $invoiceTotal = $invoices->sum(fn ($i) => (float) ($i->total_amount ?? 0));

        $movementCount = $movements->count();

        // Count purchase movements (entry)
        $entryCount = $movements->where('type', 'purchase')->count();
        // Count sale movements (exit)
        $exitCount = $movements->where('type', 'sale')->count();

        return (object) [
            'count' => $movementCount,
            // 'invoice_count' => $invoiceCount,
            'movement_count' => $movementCount,
            // 'invoice_total' => $invoiceTotal,
            'entry_count' => $entryCount,
            'exit_count' => $exitCount,
        ];
    }

    /*
     |--------------------------------------------------------------------|
     * |                         خروجی اکسل و CSV                             |
     * |--------------------------------------------------------------------|
     */
    private function exportRows(): Collection
    {
        // Re-query without pagination limit
        // $invoiceQuery = PurchaseInvoice::query()
        //     ->when($this->dateFrom, fn ($q) => $q->whereDate('purchase_date', '>=', $this->dateFrom))
        //     ->when($this->dateTo, fn ($q) => $q->whereDate('purchase_date', '<=', $this->dateTo));

        $movementQuery = StockMovement::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo));

        if ($this->filterType === 'purchase') {
            $movementQuery->where('type', 'purchase');
        } elseif ($this->filterType === 'sale') {
            $movementQuery->where('type', 'sale');
        }

        // $invoices = $invoiceQuery->get();
        $movements = $movementQuery->get();

        $records = collect();

        // foreach ($invoices as $invoice) {
        //     $records->push([
        //         'type' => 'invoice',
        //         'description' => 'فاکتور خرید از ' . $invoice->supplier->name,
        //         'date' => \Hekmatinasser\Verta\Verta::parse($invoice->purchase_date)->format('Y/m/d'),
        //         'related_name' => $invoice->supplier->name,
        //         'quantity' => 1,
        //         'total_amount' => $invoice->total_amount,
        //         'payment_method' => $invoice->payment_method,
        //         'user_name' => $invoice->user?->name ?? '',
        //     ]);
        // }

        foreach ($movements as $movement) {
            $records->push([
                'type' => $movement->type,
                'description' => $movement->description,
                'date' => \Hekmatinasser\Verta\Verta::parse($movement->created_at)->format('Y/m/d'),
                'related_name' => $movement->product?->name ?? '',
                'quantity' => $movement->type === 'purchase' ? $movement->quantity : '-' . $movement->quantity,
                'total_amount' => null,
                'payment_method' => null,
                'user_name' => $movement->user?->name ?? '',
            ]);
        }

        return $records->sortByDesc(fn ($r) => strtotime(str_replace('/', '-', $r['date'])))->values();
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
                fputcsv($handle, [
                    $row['type'] === 'initial' ? 'موجودی اولیه' : ($row['type'] === 'purchase' ? 'ورود کالا' : 'خروج کالا'),
                    $row['description'],
                    $row['date'],
                    $row['related_name'],
                    $row['quantity'] ?? '',
                    $row['total_amount'] ?? '',
                    $row['payment_method'] ?? '',
                    $row['user_name'] ?? '',
                ]);
            }

            // Add totals row
            $totals = $this->calculateTotals(
                // PurchaseInvoice::query()
                    // ->when($this->dateFrom, fn ($q) => $q->whereDate('purchase_date', '>=', $this->dateFrom))
                    // ->when($this->dateTo, fn ($q) => $q->whereDate('purchase_date', '<=', $this->dateTo)),
                StockMovement::query()
                    ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
                    ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            );

            fputcsv($handle, [
                'جمع کل',
                '',
                '',
                '',
                number_format((float) $totals['count'] ?? 0, 0),
                // number_format((float) $totals['invoice_total'] ?? 0, 2),
                '',
                '',
            ]);

            fclose($handle);
        };

        $filename = 'purchase-report-' . now()->format('Y-m-d-His') . '.csv';

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

        $filename = 'purchase-report-' . now()->format('Y-m-d-His') . '.xls';

        return Response::stream($callback, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function spreadsheetXml(Collection $rows): string
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>'
. '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel" '
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"></Workbook>'
);

$worksheet = $xml->addChild('Worksheet');
$worksheet->addAttribute('ss:Name', 'گزارش خرید');

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

// Add total row - use last record's total_amount or 0
$lastRecord = $rows->last() ?? (object) [];
$totalRow = $table->addChild('Row');
$totalValues = [
'جمع کل',
'',
'',
'',
number_format((float) ($lastRecord['total_amount'] ?? 0), 0),
number_format((float) ($lastRecord['total_amount'] ?? 0), 2),
'',
'',
];
foreach ($totalValues as $value) {
$cell = $totalRow->addChild('Cell');
$cell->addAttribute('ss:StyleID', 'Bold');
$data = $cell->addChild('Data', htmlspecialchars((string) $value, ENT_XML1, 'UTF-8'));
$data->addAttribute('ss:Type', 'String');
}

return $xml->asXML();
}

private function headerLabels(): array
{
return [
'نوع عملیات',
'شرح',
'تاریخ',
'محصول',
'تعداد',
// 'مبلغ کل',
// 'پرداخت',
'توسط',
];
}
}