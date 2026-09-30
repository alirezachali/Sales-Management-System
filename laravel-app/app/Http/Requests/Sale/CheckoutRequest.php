<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
                'min:1',
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
                Rule::in(['cash', 'card', 'mixed', 'credit']),
            ],
            'payments' => [
                'nullable',
                'array',
            ],
            'payments.*.type' => [
                'required_with:payments',
                Rule::in(['cash', 'card']),
            ],
            'payments.*.amount' => [
                'required_with:payments',
                'numeric',
                'min:0',
            ],
            'points_to_redeem' => [
                'nullable',
                'integer',
                'min:0',
            ],
            // مشتری اگر انتخاب نشد مشکلی نیست. ولی اگر انتخاب شد حتما میبایست داخل جدول مشتریها وجود داشته باشد
            'customer_id' => [
                'nullable',
                'integer',
                'exists:customers,id',
                Rule::requiredIf(fn () => $this->input('payment_type') === 'credit'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cart.required' => 'سبد فروش خالی است.',
            'cart.min' => 'سبد فروش خالی است.',
            'cart.*.id.required' => 'شناسه کالا الزامی است.',
            'cart.*.id.exists' => 'یکی از کالاهای سبد در سیستم یافت نشد.',
            'cart.*.quantity.required' => 'تعداد کالا الزامی است.',
            'cart.*.quantity.min' => 'تعداد هر کالا باید حداقل ۱ باشد.',
            'discount.min' => 'تخفیف نمی‌تواند منفی باشد.',
            'payment_type.required' => 'روش پرداخت را انتخاب کنید.',
            'payment_type.in' => 'روش پرداخت نامعتبر است.',
            'payments.*.type.in' => 'نوع پرداخت فقط نقدی یا کارتخوان مجاز است.',
            'payments.*.amount.min' => 'مبلغ پرداخت نمی‌تواند منفی باشد.',
            'points_to_redeem.min' => 'تعداد امتیاز مصرفی نامعتبر است.',
            'customer_id.required' => 'برای فروش نسیه باید مشتری انتخاب شده باشد.',
            'customer_id.exists' => 'مشتری انتخاب‌شده معتبر نیست.',
        ];
    }

    public function attributes(): array
    {
        return [
            'cart' => 'سبد فروش',
            'discount' => 'تخفیف',
            'payment_type' => 'روش پرداخت',
            'customer_id' => 'مشتری',
            'payments' => 'پرداخت‌ها',
            'points_to_redeem' => 'امتیاز',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('payment_type') === 'credit' && ! $this->filled('customer_id')) {
                $validator->errors()->add('customer_id', 'برای فروش نسیه باید مشتری انتخاب شده باشد.');
            }

            $payments = $this->input('payments', []);

            if (! is_array($payments)) {
                return;
            }

            $sum = 0.0;
            foreach ($payments as $payment) {
                $sum += (float) ($payment['amount'] ?? 0);
            }

            if (in_array($this->input('payment_type'), ['cash', 'card', 'mixed'], true) && $sum <= 0) {
                $validator->errors()->add('payments', 'مبلغ پرداختی باید بزرگ‌تر از صفر باشد.');
            }
        });
    }
}
