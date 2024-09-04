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
            'ageGroup' => ['required', 'string', Rule::in(['U-6', 'U-7', 'U-8', 'U-9', 'U-10', 'U-11', 'U-12', 'U-13', 'U-14', 'U-15', 'U-16', 'U-17', 'U-18', 'U-19', 'U-20', 'U-21'])],
            'coachName' => ['nullable', 'string'],
            'directorName' => ['nullable','string'],
            'totalPlayers' => ['nullable','numeric', 'min:0'],
            'academyName' => ['nullable','string']
        ];
    }
}
