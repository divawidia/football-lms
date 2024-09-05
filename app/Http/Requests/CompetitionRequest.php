<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CompetitionRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'type' => ['required', Rule::in('League', 'Tournament')],
            'logo' => ['nullable', 'image', 'max:10240'],
            'startDate' => ['required', 'date', 'after_or_equal:today'],
            'endDate' => ['required', 'date', 'after:startDate'],
            'location' => ['required', 'string'],
            'contactName' => ['nullable', 'string'],
            'contactPhone' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'division' => ['required', 'string'],
            'teams' => ['required', Rule::exists('teams', 'id')],
            'opponentTeams' => ['required', Rule::exists('opponent_teams', 'id')],
        ];
    }
}
