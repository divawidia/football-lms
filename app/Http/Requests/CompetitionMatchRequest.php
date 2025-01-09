<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompetitionMatchRequest extends FormRequest
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
            'place' => ['required', 'string'],
            'date' => ['required', 'date'],
            'startTime' => ['required', 'date_format:H:i'],
            'endTime' => ['required', 'after:startTime', 'date_format:H:i'],
            'externalTeamName' => ['nullable', 'string'],
            'homeTeamId' => ['required', Rule::exists('teams', 'id')],
            'awayTeamId' => ['nullable', Rule::exists('teams', 'id')],
        ];
    }
}
