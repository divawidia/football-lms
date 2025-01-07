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
            'type' => ['required', Rule::in('League', 'Knockout', 'Friendly')],
            'logo' => ['nullable', 'image', 'max:1024'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:startDate'],
            'location' => ['required', 'string'],
            'isInternal' => ['required', Rule::in(1,0)],
//            'contactName' => ['nullable', 'string'],
//            'contactPhone' => ['nullable', 'string'],
//            'description' => ['nullable', 'string'],
//            'groupName' => ['required', 'string'],
//            'teams' => ['required', Rule::exists('teams', 'id')],
//            'opponentTeams' => ['required', Rule::exists('teams', 'id')],
        ];
    }
}
