<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatchScoreRequest extends FormRequest
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
            'playerId' => ['required', Rule::exists('players', 'id')],
            'assistPlayerId' => ['nullable', Rule::exists('players', 'id')],
            'minuteScored' => ['required', 'min:1', 'max:160', 'numeric'],
        ];
    }
}
