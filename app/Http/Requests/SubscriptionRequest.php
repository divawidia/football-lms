<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
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
            'productId' => ['required', Rule::exists('products', 'id')],
            'productPrice' => ['required', 'numeric', 'min:1'],
        ];
    }
}
