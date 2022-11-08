<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'question' => ['nullable', 'max:255', 'string'],
            'options' => ['nullable', 'max:255', 'string'],
            'right_option' => ['nullable', 'max:255', 'string'],
            'videos' => ['nullable', 'max:255', 'string'],
            'type' => ['nullable', 'numeric'],
            'answer_type' => ['nullable', 'numeric'],
            'keywords' => ['nullable', 'max:255', 'string'],
            'keywords_min' => ['required', 'numeric'],
            'time' => ['nullable', 'numeric'],
            'status' => ['required', 'numeric'],
        ];
    }
}
