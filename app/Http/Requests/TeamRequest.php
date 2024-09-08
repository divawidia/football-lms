<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
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
            'logo' => ['image', 'nullable', 'max:1024'],
            'teamName' => ['required', 'string', Rule::unique('teams', 'teamName')->ignore($this->team)],
            'ageGroup' => ['required', 'string'],
            'players' => ['nullable', Rule::exists('players', 'id')],
            'coaches' => ['nullable', Rule::exists('coaches', 'id')],
        ];
    }
}
