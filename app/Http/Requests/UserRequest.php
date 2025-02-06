<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(Auth::user()->id)],
            'gender' => ['required', 'string', Rule::in('male', 'female', 'others')],
            'dob' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string'],
            'phoneNumber' => ['required', 'string'],
            'zipCode' => ['required', 'numeric'],
            'foto' => ['image', 'max:1024', 'nullable'],
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'status' => [Rule::in('1'), 'nullable'],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'city_id' => ['required', Rule::exists('cities', 'id')],
        ];
    }
}
