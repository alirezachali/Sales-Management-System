<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Services\SaleService;

class SaleController extends Controller
{
    public function index()
    {
        return view('sales.pos');
    }

    public function findProduct(Request $request)
    {
        $request->validate([
            'barcode' => 'required'
        ]);

        $product = Product::where('barcode', $request->barcode)
            ->where('is_active', true)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'کالا پیدا نشد'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'barcode' => $product->barcode,
                'name' => $product->name,
                'price' => $product->sell_price,
                'stock' => $product->stock,
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $sale = $this->saleService->checkout(
            $request->cart,
            $request->discount ?? 0,
            $request->payment_type ?? 'cash'
        );

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id
        ]);
    }

    private SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }
}

