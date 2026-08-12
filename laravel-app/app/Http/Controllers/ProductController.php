<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q
                    ->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalProducts = Product::count();

        $activeProducts = Product::where('is_active', true)->count();

        $inactiveProducts = Product::where('is_active', false)->count();

        $lowStockProducts = Product::where('stock', '<=', 5)->count();

        $categories = \App\Models\Category::all();

        return view('products.index', compact(
            'products',
            'totalProducts',
            'activeProducts',
            'inactiveProducts',
            'lowStockProducts',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::all();

        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $product = Product::create($data);

        if ($product->stock > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'initial',
                'quantity' => $product->stock,
                'description' => 'موجودی اولیه کالا',
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'کالا با موفقیت ثبت شد');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();

        return view('products.edit', compact(
            'product',
            'categories'
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'کالا با موفقیت ویرایش شد');
    }

    public function stock(Product $product)
    {
        $movements = $product
            ->stockMovements()
            ->latest()
            ->paginate(20);

        return view(
            'products.stock',
            compact(
                'product',
                'movements'
            )
        );
    }

    public function createStock(Product $product)
    {
        return view(
            'products.stock-create',
            compact('product')
        );
    }

    public function storeStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'description' => 'nullable|string'
        ]);

        DB::transaction(function () use ($data, $product) {
            $product->increment(
                'stock',
                $data['quantity']
            );

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'purchase',
                'quantity' => $data['quantity'],
                'description' => $data['description']
                    ?? 'ورود کالا از خرید'
            ]);
        });

        return redirect()
            ->route('products.stock', $product)
            ->with(
                'success',
                'ورود کالا با موفقیت ثبت شد'
            );
    }

    public function createSale(Product $product)
    {
        return view(
            'products.sale-create',
            compact('product')
        );
    }

    public function storeSale(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'description' => 'nullable|string'
        ]);

        if ($product->stock < $data['quantity']) {
            return back()
                ->with(
                    'error',
                    'موجودی کالا کافی نیست'
                );
        }

        DB::transaction(function () use ($data, $product) {
            $product->decrement(
                'stock',
                $data['quantity']
            );

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => $data['quantity'],
                'description' => $data['description']
                    ?? 'فروش کالا'
            ]);
        });

        return redirect()
            ->route('products.stock', $product)
            ->with(
                'success',
                'خروج کالا ثبت شد'
            );
    }
}