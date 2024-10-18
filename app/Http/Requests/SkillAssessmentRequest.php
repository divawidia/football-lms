<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SkillAssessmentRequest extends FormRequest
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
            'controlling' => ['string', 'required'],
            'recieving' => ['string', 'required'],
            'dribbling' => ['string', 'required'],
            'passing' => ['string', 'required'],
            'shooting' => ['string', 'required'],
            'crossing' => ['string', 'required'],
            'turning' => ['string', 'required'],
            'ballHandling' => ['string', 'required'],
            'powerKicking' => ['string', 'required'],
            'goalKeeping' => ['string', 'required'],
            'offensivePlay' => ['string', 'required'],
            'defensivePlay' => ['string', 'required'],
        ];
    }
}
