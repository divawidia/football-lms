<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatchStatsRequest extends FormRequest
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
            'teamSide' => ['nullable', Rule::in('homeTeam', 'awayTeam', 'externalTeam')],
            'teamId' => ['nullable', Rule::exists('teams', 'id')],
            'teamPossesion' => ['required', 'numeric', 'min:0', 'max:100'],
            'teamShotOnTarget' => ['required', 'numeric', 'min:0'],
            'teamShots' => ['required', 'numeric', 'min:0'],
            'teamTouches' => ['required', 'numeric', 'min:0'],
            'teamTackles' => ['required', 'numeric', 'min:0'],
            'teamClearances' => ['required', 'numeric', 'min:0'],
            'teamCorners' => ['required', 'numeric', 'min:0'],
            'teamOffsides' => ['required', 'numeric', 'min:0'],
            'teamYellowCards' => ['required', 'numeric', 'min:0'],
            'teamRedCards' => ['required', 'numeric', 'min:0'],
            'teamFoulsConceded' => ['required', 'numeric', 'min:0'],
            'teamPasses' => ['required', 'numeric', 'min:0'],
        ];
    }
}
