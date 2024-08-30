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
            'foto' => ['image', 'nullable'],
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'dob' => ['required', 'date'],
            'team' => [Rule::exists('teams'), 'nullable'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->coach)],
            'password' => ['string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'nullable'],
            'gender' => ['required', 'string', Rule::in('male', 'female')],
            'hireDate' => ['required', 'date'],
            'address' => ['required', 'string'],
            'phoneNumber' => ['required', 'string'],
            'zipCode' => ['required', 'numeric'],
            'country_id' => ['required'],
            'state_id' => ['required'],
            'city_id' => ['required'],
            'certificationLevel' => ['required', Rule::exists('coach_certifications', 'id')],
            'specialization' => ['required', Rule::exists('coach_specializations', 'id')],
            'height' => ['required', 'numeric'],
            'weight' => ['required', 'numeric'],
            'status' => [Rule::in('1', '0'), 'nullable'],
        ];
    }
}
