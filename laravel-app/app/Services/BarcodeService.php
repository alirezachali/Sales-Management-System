<?php

namespace App\Services;

use App\Models\Product;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    public function __construct(
        private SettingService $settingService
    ) {}

    public function generate(): string
    {
        $prefix = $this->settingService->get(
            'barcode_prefix',
            '200000'
        );

        $length = (int) $this->settingService->get(
            'barcode_length',
            12
        );

        $prefix = preg_replace('/\D/', '', (string) $prefix);

        if ($prefix === '') {
            $prefix = '200000';
        }

        do {
            $randomLength = $length - strlen($prefix);

            if ($randomLength < 1) {
                $randomLength = 6;
            }

            $barcode = $prefix . str_pad(
                random_int(0, (10 ** $randomLength) - 1),
                $randomLength,
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