<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * نمایش لیست تامین‌کنندگان
     */
    public function index()
    {
        $suppliers = Supplier::with('brands')
            ->latest()
            ->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * ذخیره تامین‌کننده جدید (از طریق مودال ساخت)
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $validated['code'] = $this->generateCode();
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'تامین‌کننده جدید با موفقیت ثبت شد.');
    }

    /**
     * بروزرسانی تامین‌کننده (از طریق مودال ویرایش)
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate($this->rules($supplier->id));
        $validated['is_active'] = $request->boolean('is_active', true);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'اطلاعات تامین‌کننده بروزرسانی شد.');
    }

    /**
     * حذف (نرم) تامین‌کننده
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'تامین‌کننده حذف شد.');
    }

    /**
     * قوانین اعتبارسنجی مشترک بین store و update
     */
    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'                 => ['required', 'string', 'max:100'],
            'company_name'         => ['nullable', 'string', 'max:150'],
            'contact_person'       => ['nullable', 'string', 'max:100'],
            'type'                 => ['required', Rule::in(['individual', 'company'])],
            'national_id'          => ['nullable', 'string', 'max:20'],
            'economic_code'        => ['nullable', 'string', 'max:30'],
            'registration_number'  => ['nullable', 'string', 'max:30'],
            'mobile'               => [
                'required', 'string', 'max:15',
                Rule::unique('suppliers', 'mobile')->ignore($ignoreId),
            ],
            'phone'                => ['nullable', 'string', 'max:15'],
            'email'                => ['nullable', 'email', 'max:100'],
            'website'              => ['nullable', 'url', 'max:150'],
            'province'             => ['nullable', 'string', 'max:100'],
            'city'                 => ['nullable', 'string', 'max:100'],
            'address'              => ['nullable', 'string'],
            'postal_code'          => ['nullable', 'string', 'max:15'],
            'credit_limit'         => ['nullable', 'numeric', 'min:0'],
            'opening_balance'      => ['nullable', 'numeric'],
            'bank_account_number'  => ['nullable', 'string', 'max:30'],
            'iban'                 => ['nullable', 'string', 'max:34'],
            'payment_terms'        => ['nullable', 'string', 'max:100'],
            'rating'               => ['nullable', 'integer', 'min:1', 'max:5'],
            'notes'                => ['nullable', 'string'],
            'is_active'            => ['nullable', 'boolean'],
        ];
    }

    /**
     * تولید کد یکتای تامین‌کننده مثل SUP-0001
     */
    private function generateCode(): string
    {
        $last = Supplier::withTrashed()->latest('id')->first();
        $nextNumber = $last ? ((int) Str::afterLast($last->code, '-') + 1) : 1;

        return 'SUP-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}