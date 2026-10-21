<?php

namespace App\Services;

use App\Models\Sale;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceService
{
    public function __construct(
        protected SettingService $settingService,
        protected BarcodeService $barcodeService,
    ) {
    }

    public function data(Sale $sale): array
    {
        $settings = [
            'paper_size' => $this->settingService->get('paper_size', '80'),

            'print_copies' => (int) $this->settingService->get(
                'print_copies',
                1
            ),

            'auto_print' => (int) $this->settingService->get(
                'auto_print',
                0
            ),

            'print_logo' => (int) $this->settingService->get(
                'print_logo',
                1
            ),

            'print_address' => (int) $this->settingService->get(
                'print_address',
                1
            ),

            'print_phone' => (int) $this->settingService->get(
                'print_phone',
                1
            ),

            'print_barcode' => (int) $this->settingService->get(
                'print_barcode',
                0
            ),

            'print_qrcode' => (int) $this->settingService->get(
                'print_qrcode',
                0
            ),

            'print_datetime' => (int) $this->settingService->get(
                'print_datetime',
                1
            ),

            'receipt_footer' => $this->settingService->get(
                'receipt_footer',
                'از خرید شما سپاسگزاریم'
            ),

            'currency' => $this->settingService->get('currency', 'تومان'),

            'store_name' => $this->settingService->get(
                'store_name',
                ''
            ),

            'phone' => $this->settingService->get(
                'phone',
                ''
            ),

            'mobile' => $this->settingService->get(
                'mobile',
                ''
            ),

            'address' => $this->settingService->get(
                'address',
                ''
            ),

            'website' => $this->settingService->get(
                'website',
                ''
            ),

            'store_logo' => $this->settingService->get(
                'store_logo',
                ''
            ),
        ];

        return [
            'sale' => $sale,
            'settings' => $settings,

            // بارکد/کیوآر کد فقط وقتی در تب چاپ فعال شده باشند ساخته می‌شوند.
            'barcodeSvg' => $settings['print_barcode']
                ? $this->barcodeSvg($sale)
                : null,

            'qrSvg' => $settings['print_qrcode']
                ? $this->qrSvg($sale)
                : null,
        ];
    }

    /** بارکد شماره فاکتور برای اسکن سریع در بازگشت کالا. */
    protected function barcodeSvg(Sale $sale): ?string
    {
        try {
            // عرض ماژول ۱ و ارتفاع ۳۶ برای جا شدن در ۷۲ میلی‌متر.
            return $this->barcodeService->render($sale->invoice_number, 1, 36);
        } catch (\Throwable) {
            return null;
        }
    }

    /** کیوآر کد شامل شماره فاکتور و مبلغ نهایی. */
    protected function qrSvg(Sale $sale): ?string
    {
        try {
            $payload = implode('|', [
                $sale->invoice_number,
                (string) (float) $sale->final_price,
                optional($sale->created_at)->toDateTimeString() ?? '',
            ]);

            return (string) QrCode::size(160)
                ->margin(0)
                ->format('svg')
                ->encoding('UTF-8')
                ->generate($payload);
        } catch (\Throwable) {
            return null;
        }
    }
}
