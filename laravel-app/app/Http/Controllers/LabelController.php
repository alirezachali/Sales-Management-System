<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\BarcodeService;
use App\Services\SettingService;

class LabelController extends Controller
{
    public function __construct(
        private BarcodeService $barcodeService,
        private SettingService $settingService
    ) {}

    public function show(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'price' => $product->sell_price,
            'barcode_svg' => $this->barcodeService->render(
                $product->barcode
            ),
            'label_width' => (int) $this->settingService->get(
                'label_width',
                50
            ),
            'label_height' => (int) $this->settingService->get(
                'label_height',
                30
            ),
            'label_show_name' => (int) $this->settingService->get(
                'label_show_name',
                1
            ),
            'label_show_price' => (int) $this->settingService->get(
                'label_show_price',
                1
            ),
            'label_show_barcode' => (int) $this->settingService->get(
                'label_show_barcode',
                1
            ),
            'label_show_code' => (int) $this->settingService->get(
                'label_show_code',
                1
            ),
            'label_show_unit' => (int) $this->settingService->get(
                'label_show_unit',
                0
            ),
        ]);
    }
}