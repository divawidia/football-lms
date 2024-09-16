<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'teamAOwnGoal' => ['required', 'numeric', 'min:0'],
            'teamAPossession' => ['required', 'numeric', 'min:0'],
            'teamAShotOnTarget' => ['required', 'numeric', 'min:0'],
            'teamAShots' => ['required', 'numeric', 'min:0'],
            'teamATouches' => ['required', 'numeric', 'min:0'],
            'teamATackles' => ['required', 'numeric', 'min:0'],
            'teamAClearances' => ['required', 'numeric', 'min:0'],
            'teamACorners' => ['required', 'numeric', 'min:0'],
            'teamAOffsides' => ['required', 'numeric', 'min:0'],
            'teamAYellowCards' => ['required', 'numeric', 'min:0'],
            'teamARedCards' => ['required', 'numeric', 'min:0'],
            'teamAFoulsConceded' => ['required', 'numeric', 'min:0'],
            'teamAPasses' => ['required', 'numeric', 'min:0'],
            'teamBTeamScore' => ['required', 'numeric', 'min:0'],
            'teamBOwnGoal' => ['required', 'numeric', 'min:0'],
            'teamBPossession' => ['required', 'numeric', 'min:0'],
            'teamBShotOnTarget' => ['required', 'numeric', 'min:0'],
            'teamBShots' => ['required', 'numeric', 'min:0'],
            'teamBTouches' => ['required', 'numeric', 'min:0'],
            'teamBTackles' => ['required', 'numeric', 'min:0'],
            'teamBClearances' => ['required', 'numeric', 'min:0'],
            'teamBCorners' => ['required', 'numeric', 'min:0'],
            'teamBOffsides' => ['required', 'numeric', 'min:0'],
            'teamBYellowCards' => ['required', 'numeric', 'min:0'],
            'teamBRedCards' => ['required', 'numeric', 'min:0'],
            'teamBFoulsConceded' => ['required', 'numeric', 'min:0'],
            'teamBPasses' => ['required', 'numeric', 'min:0'],
        ];
    }
}
