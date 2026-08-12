<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerRole;

class CustomerController extends Controller
{
    
    public function index(Request $request, Customer $customer)
    {
        
        $customers = Customer::query()
            ->with('role')
            ->search($request->search)
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

  
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'مشتری حذف شد.');
    }

    // برای جستجوی مشتری در صندوق فروش
    public function search(Request $request)
    {
        $customers = Customer::query()
            ->active()
            ->search($request->search)
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->full_name,
                    'mobile' => $customer->mobile,
                ];
            }),
        ]);
    }
    
}