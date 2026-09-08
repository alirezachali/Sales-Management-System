{{-- resources/views/reports/sales.blade.php --}}
@extends('layouts.app')

@section('title', 'گزارش فاکتورهای خرید')

@section('content')
    <div class="container-fluid">
        <livewire:reports.purchase-report />
    </div>
    {{-- استایل تقویم شمسی (فقط همین صفحه) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.css">
@endsection

@section('scripts')
    {{-- تقویم شمسی سبک و بدون وابستگی برای انتخاب بازه گزارش --}}
    <script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@1.0.0/dist/jalalidatepicker.min.js"></script>
    <script>
        function initSalesReportJalaliPicker() {
            if (!window.jalaliDatepicker) return;
            jalaliDatepicker.startWatch({
                time: false,
                persianDigits: false,
                showTodayBtn: true,
                showEmptyBtn: true,
            });
        }

        document.addEventListener('DOMContentLoaded', initSalesReportJalaliPicker);
        // بعد از هر به‌روزرسانی Livewire هم تقویم را دوباره فعال می‌کنیم
        document.addEventListener('livewire:init', () => {
            initSalesReportJalaliPicker();
            Livewire.hook('morph.updated', initSalesReportJalaliPicker);
        });
    </script>
    {{-- اسکریپت مربوط به دکمه چاپ فاکتور خرید در مودال مشاهده جزئیات --}}
    <script>
        function printPurchaseInvoiceDetails() {
            const area = document.getElementById('purchase-invoice-print-area');
            if (!area) {
                return;
            }

            const win = window.open('', '_blank', 'width=900,height=700');
            if (!win) {
                return;
            }

            win.document.write(
               `<!DOCTYPE html>
                <html lang="fa" dir="rtl">
                    <head>
                        <meta charset="UTF-8">
                        <title>جزئیات فاکتور خرید</title>
                        <style>

                            body {
                                font-family: Tahoma, Vazirmatn, sans-serif;
                                padding: 16px;
                                color: #111;
                                background: #fff;
                            }

                            h6 { margin: 0 0 12px; }

                            table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }

                            th, td { border: 1px solid #333; padding: 6px 8px; text-align: right; font-size: 15px; }

                            th { background: #f3f3f3; }

                            tr { width: 100%; }

                            .row { width: 100%; display: flex; gap: 8px; }

                            .col-lg-4 { flex: 1; }

                            .border { border: 1px solid #333; border-radius: 15px; padding: 8px; }

                            .text-muted { color: #555; font-size: 12px; }

                            .fw-bold { font-weight: bold; }

                            .fs-5 { font-size: 18px; }

                            .table-responsive { overflow: visible; }

                            @page { margin: 12mm; }

                        </style>
                    </head>

                    <body>
                        <h3 style="margin-top:0;">جزئیات فاکتور خرید</h3>
                        ${area.innerHTML}
                    </body>

                </html>`
            );

            win.document.close();
            win.focus();
            setTimeout(function () {
                win.print();
                win.close();
            }, 250);
        }
    </script>
@endsection
