<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todaySales = Sale::whereDate('created_at', $today)
            ->sum('final_price');

        $todayInvoices = Sale::whereDate('created_at', $today)
            ->count();

        $productsCount = Product::count();

        $lowStockProducts = Product::where('stock', '<=', 5)
            ->count();

        $latestSales = Sale::with('user')
            ->latest()
            ->take(10)
            ->get(); 
            
        $lowStockList = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->take(10)
            ->get();

        $period = CarbonPeriod::create(now()->subDays(29), now());

        $labels = [];
        $data = [];

        foreach ($period as $date) {

            $labels[] = $date->format('m/d');

            $data[] = Sale::whereDate('created_at', $date)
                ->sum('final_price');

        }

        return view('dashboard.index', compact(
            'todaySales',
            'todayInvoices',
            'productsCount',
            'lowStockProducts',
            'latestSales',
            'lowStockList',
            'labels',
            'data',
        ));
    }
}
