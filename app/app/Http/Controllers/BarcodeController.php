<?php

namespace App\Http\Controllers;

use App\Services\BarcodeService;
use Illuminate\Http\JsonResponse;

class BarcodeController extends Controller
{
    public function generate(BarcodeService $barcodeService): JsonResponse
    {
        return response()->json([
            'success' => true,
            'barcode' => $barcodeService->generate(),
        ]);
    }
}