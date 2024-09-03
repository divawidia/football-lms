<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpponentTeamRequest extends FormRequest
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
            'logo' => ['image', 'nullable'],
            'teamName' => ['required', 'string', Rule::unique('opponent_teams', 'teamName')->ignore($this->team)],
            'ageGroup' => ['required', 'string'],
            'coachName' => ['string'],
            'directorName' => ['string'],
            'totalPlayers' => ['numeric'],
            'academyName' => ['string']
        ];
    }
}
