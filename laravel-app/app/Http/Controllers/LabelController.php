<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\BarcodeService;

class LabelController extends Controller
{

    public function __construct(
        private BarcodeService $barcodeService
    ) {
    }


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

        ]);

    }

}