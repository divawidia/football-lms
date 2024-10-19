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
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->player)],
            'password' => ['string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'nullable'],
            'gender' => ['required', 'string', Rule::in('male', 'female')],
            'dob' => ['required', 'date'],
            'address' => ['required', 'string'],
            'phoneNumber' => ['required', 'string'],
            'zipCode' => ['required', 'numeric'],
            'foto' => ['image', 'nullable'],
            'country_id' => ['required'],
            'status' => [Rule::in('1'), 'nullable'],
            'state_id' => ['required'],
            'city_id' => ['required'],
            'joinDate' => ['required', 'date'],
            'positionId' => ['required', Rule::exists('player_positions', 'id')],
            'skill' => ['required', 'string'],
            'strongFoot' => ['required', 'string', Rule::in('left', 'right')],
            'height' => ['required', 'numeric'],
            'weight' => ['required', 'numeric'],
            'firstName2' => ['string'],
            'lastName2' => ['string'],
            'email2' => ['email'],
            'phoneNumber2' => ['string'],
            'relations' => ['string'],
            'team' => ['nullable', Rule::exists('teams', 'id')]
        ];
    }
}
