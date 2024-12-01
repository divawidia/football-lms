<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatchScheduleRequest extends FormRequest
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
            'matchType' => ['required', Rule::in('Friendly Match', 'Competition')],
            'competitionId' => ['nullable', Rule::exists('competitions', 'id')],
            'place' => ['required', 'string'],
            'date' => ['required', 'date'],
            'startTime' => ['required', 'date_format:H:i'],
            'endTime' => ['required', 'date_format:H:i', 'after:startTime'],
            'teamId' => ['required', Rule::exists('teams', 'id')],
            'opponentTeamId' => ['required', Rule::exists('teams', 'id')],
            'isOpponentTeamMatch' => ['required', Rule::in('0', '1')],
        ];
    }
}
