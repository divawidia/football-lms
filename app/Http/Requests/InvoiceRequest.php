<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
            'receiverUserId' => ['required', Rule::exists('users', 'id')],
            'taxId' => ['nullable'],
            'products.*.productId' => ['required', Rule::exists('products', 'id')],
            'products.*.qty' => ['required', 'numeric', 'min:1'],
//            'products.*.price' => ['required', 'numeric', 'min:1'],
            'products.*.ammount' => ['required', 'numeric', 'min:1'],
        ];
    }
}
