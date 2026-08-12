<?php

namespace App\Services;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    public function __construct(
        private SettingService $settingService
    ) {
    }

    public function generate(): string
    {
        $prefix = $this->settingService->get(
            'barcode_internal_prefix',
            '200000'
        );

        $prefix = preg_replace('/\D/', '', (string) $prefix);

        if ($prefix === '') {
            $prefix = '200000';
        }

        do {

            $barcode = $prefix . str_pad(
                random_int(0, 999999),
                6,
                '0',
                STR_PAD_LEFT
            );

        } while (
            Product::where('barcode', $barcode)->exists()
        );

        return $barcode;
    }

    public function render(string $barcode): string
    {
        $generator = new BarcodeGeneratorSVG();

        return $generator->getBarcode(
            $barcode,
            $generator::TYPE_CODE_128
        );
    }
}