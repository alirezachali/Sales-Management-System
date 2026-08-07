<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // قوانینی که در هنگام فروش باید رعایت شود
    public function rules(): array
    {
        return [
            
            'cart' => [
                'required',
                'array',
                'min:1'
            ],

            'cart.*.id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'cart.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            // روش پرداخت حتما باید انتخاب شود
            'payment_type' => [
                'required',
                Rule::in(['cash']),
            ],
            // مشتری اگر انتخاب نشد مشکلی نیست. ولی اگر انتخاب شد حتما میبایست داخل جدول مشتریها وجود داشته باشد
            'customer_id' => [
                'nullable',
                'integer',
                'exists:customers,id',
            ],
        ];
    }
}