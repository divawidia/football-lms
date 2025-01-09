<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamStandingRequest extends FormRequest
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
            'matchPlayed' => ['required', 'integer', 'min:0'],
            'won' => ['required', 'integer', 'min:0'],
            'drawn' => ['required', 'integer', 'min:0'],
            'lost' => ['required', 'integer', 'min:0'],
            'goalsFor' => ['required', 'integer', 'min:0'],
            'goalsAgainst' => ['required', 'integer', 'min:0'],
            'goalsDifference' => ['required', 'integer', 'min:0'],
            'points' => ['required', 'integer', 'min:0'],
            'standingPositions' => ['required', 'integer', 'min:0']
        ];
    }
}
