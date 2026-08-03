<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerRole;

class CustomerController extends Controller
{
    
    public function index(Request $request, Customer $customer)
    {
        $customers = Customer::with('role')
        ->when($request->search, function ($query) use ($request) {

            $query->where(function ($q) use ($request) {

                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('mobile', 'like', "%{$request->search}%");

            });

        })
        ->latest()
        ->paginate(15);

        $roles = CustomerRole::orderBy('sort_order')->get();

        return view('customers.index', compact('customers', 'roles'));
    }

    
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([

            'first_name' => 'required|max:100',

            'last_name' => 'required|max:100',

            'mobile' => 'required|max:20|unique:customers,mobile',

            'phone' => 'nullable|max:20',

            'customer_role_id' => 'nullable|exists:customer_roles,id',

            'is_active' => 'required|boolean',

            'notes' => 'nullable',

        ]);

        Customer::create($validated);

        return redirect()
               ->route('customers.index')
               ->with('success', 'مشتری با موفقیت ثبت شد.');
    }

   
    public function show(string $id)
    {
        //
    }

    
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([

            'first_name' => 'required|max:100',

            'last_name' => 'required|max:100',

            'mobile' => 'required|max:20|unique:customers,mobile,' . $customer->id,

            'phone' => 'nullable|max:20',

            'customer_role_id' => 'nullable|exists:customer_roles,id',

            'is_active' => 'required|boolean',

            'notes' => 'nullable',

        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.index')
            ->with('success', 'مشتری بروزرسانی شد.');
    }

  
    public function destroy(string $id)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'مشتری حذف شد.');
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
}
