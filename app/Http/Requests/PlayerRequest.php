<?php

namespace App\Http\Requests;

use App\Models\Admin;
use App\Models\PlayerPosition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PlayerRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'nullable'],
            'gender' => ['required', 'string', Rule::in('male', 'female')],
            'dob' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string'],
            'phoneNumber' => ['required', 'string'],
            'zipCode' => ['required', 'numeric'],
            'foto' => ['image', 'max:1024', 'nullable'],
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'status' => [Rule::in('1'), 'nullable'],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'city_id' => ['required', Rule::exists('cities', 'id')],
            'joinDate' => ['required', 'date', 'before_or_equal:today'],
            'positionId' => ['required', Rule::exists('player_positions', 'id')],
            'skill' => ['required', 'string', Rule::in('Beginner', 'Intermediate', 'Advance')],
            'strongFoot' => ['required', 'string', Rule::in('left', 'right')],
            'height' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'firstName2' => ['string', 'required'],
            'lastName2' => ['string', 'required'],
            'email2' => ['email', 'required'],
            'phoneNumber2' => ['string', 'required'],
            'relations' => ['string', 'required', Rule::in('Father', 'Mother', 'Brother', 'Sister', 'Others')],
            'team' => ['nullable', Rule::exists('teams', 'id')],
        ];
    }
}
