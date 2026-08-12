<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\SaleService;
use App\Services\InvoiceService;
use App\Http\Requests\Sale\CheckoutRequest;

class SaleController extends Controller
{
    public function __construct(
        SaleService $saleService,
        InvoiceService $invoiceService
    ) {
        $this->saleService = $saleService;
        $this->invoiceService = $invoiceService;
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
            $data['paid_amount'],
        );

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
        ]);
    }

    private SaleService $saleService;

    private InvoiceService $invoiceService;

    public function invoice(Sale $sale)
    {
        $sale->load([
            'items.product',
            'user',
            'customer',
            'payments',
        ]);

        $data = $this->invoiceService->data($sale);

        return view('sales.invoice', $data);
    }
}