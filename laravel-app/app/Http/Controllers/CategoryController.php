<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    
        $categories = Category::withCount('products')
            ->latest()
            ->paginate(15);

        return view('categories.index', compact('categories'));

    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $validated = $request->validate([
        'name' => 'required|string|max:100|unique:categories,name',
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
    ]);

    Category::create([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
        'is_active' => $request->has('is_active'),
    ]);

    return redirect()
        ->route('categories.index')
        ->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
    $validated = $request->validate([
        'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        'description' => 'nullable|string',
        'is_active' => 'nullable|boolean',
    ]);

    $category->update([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? null,
        'is_active' => $request->has('is_active'),
    ]);

    return redirect()
        ->route('categories.index')
        ->with('success', 'دسته‌بندی با موفقیت ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
    if ($category->products()->count() > 0) {

        return redirect()
            ->route('categories.index')
            ->with('error', 'این دسته‌بندی دارای کالا است و قابل حذف نیست.');
    }

    $category->delete();

    return redirect()
        ->route('categories.index')
        ->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }
}
