<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CoachRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->coach_management)],
            'password' => ['string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'nullable'],
            'gender' => ['required', 'string', Rule::in('male', 'female')],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string'],
            'phoneNumber' => ['required', 'string'],
            'zipCode' => ['required', 'numeric'],
            'foto' => ['image', 'nullable'],
            'country_id' => ['required'],
            'status' => [Rule::in('1', '0'), 'nullable'],
            'state_id' => ['required'],
            'city_id' => ['required'],
            'hireDate' => ['required', 'date'],
            'certificationLevel' => ['required', 'string'],
            'specialization' => ['required', 'string'],
            'height' => ['required', 'numeric'],
            'weight' => ['required', 'numeric'],
        ];
    }
}
