<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'productName' => ['required', 'string', 'max:255', Rule::unique('product_categories', 'categoryName')->ignore($this->productCategory)],
            'description' => ['nullable', 'string'],
            'categoryId' => ['required', Rule::exists('product_categories', 'id')],
            'price' => ['required', 'numeric', 'max:1'],
            'priceOption' => ['required', Rule::in('subscription', 'one time payment')],
            'subscriptionCycle' => ['nullable', Rule::in('monthly','quarterly','semianually','anually')],
        ];
    }
}
