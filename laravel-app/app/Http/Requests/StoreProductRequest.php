<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'barcode' => [
                'required',
                'string',
                'max:50',
                'unique:products,barcode'
            ],


            'name' => [
                'required',
                'string',
                'max:255'
            ],


            'category_id' => [
                'nullable',
                'exists:categories,id'
            ],


            'buy_price' => [
                'required',
                'numeric',
                'min:0'
            ],


            'sell_price' => [
                'required',
                'numeric',
                'min:0'
            ],


            'stock' => [
                'required',
                'numeric',
                'min:0'
            ],


            'unit' => [
                'required',
                'string',
                'max:20'
            ],


            'is_active' => [
                'required',
                'boolean'
            ],
        ];
    }
}
