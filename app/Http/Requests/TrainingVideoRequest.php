<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrainingVideoRequest extends FormRequest
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
            'previewPhoto' => ['image', 'required', 'max:1024'],
            'trainingTitle' => ['required', 'string', Rule::unique('training_videos', 'trainingTitle')->ignore($this->training)],
            'description' => ['required', 'string'],
            'level' => ['required', Rule::in('beginer', 'intermediate', 'expert')]
        ];
    }
}
