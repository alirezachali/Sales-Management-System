<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Services\SaleService;
use App\Http\Requests\Sale\CheckoutRequest;
use Illuminate\Http\JsonResponse;


class SaleController extends Controller
{

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }


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

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $data = $request->validated();

        $sale = $this->saleService->checkout(
            $data['cart'],
            $data['discount'] ?? 0,
            $data['payment_type'],
            $data['customer_id'] ?? null,
        );

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
        ]);
    }

    private SaleService $saleService;

    

    public function invoice(\App\Models\Sale $sale)
    {
        $sale->load([
            'items.product',
            'user'
        ]);

        return view('sales.invoice', compact('sale'));
    }
}