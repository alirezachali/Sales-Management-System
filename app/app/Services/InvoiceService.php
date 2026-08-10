<?php

namespace App\Services;

use App\Models\Sale;

class InvoiceService
{
    public function __construct(
        protected SettingService $settingService
    ) {
    }

    public function data(Sale $sale): array
    {
        return [
            'sale' => $sale,

            'settings' => [
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
            ],
        ];
    }
}