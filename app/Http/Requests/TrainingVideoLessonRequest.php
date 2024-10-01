<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrainingVideoLessonRequest extends FormRequest
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
            'lessonTitle' => ['required', 'string', Rule::unique('training_video_lessons', 'lessonTitle')->ignore($this->lesson)],
            'description' => ['nullable', 'string'],
            'lessonVideoURL' => ['required', 'url'],
            'totalDuration' => ['required', 'numeric'],
            'videoId' => ['required', 'string'],
        ];
    }
}
