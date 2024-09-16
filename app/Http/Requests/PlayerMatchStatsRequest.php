<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerMatchStatsRequest extends FormRequest
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
            'minutesPlayed' => ['required', 'numeric', 'min:0'],
            'shots' => ['required', 'numeric', 'min:0'],
            'passes' => ['required', 'numeric', 'min:0'],
            'fouls' => ['required', 'numeric', 'min:0'],
            'yellowCards' => ['required', 'numeric', 'min:0'],
            'redCards' => ['required', 'numeric', 'min:0'],
            'saves' => ['required', 'numeric', 'min:0'],
        ];
    }
}
