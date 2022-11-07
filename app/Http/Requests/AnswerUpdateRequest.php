<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerUpdateRequest extends FormRequest
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
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['required', 'max:255', 'string'],
            'answer' => ['required', 'max:255', 'string'],
            'points' => ['nullable', 'numeric'],
            'is_true' => ['nullable', 'boolean'],
            'status' => ['required', 'numeric'],
            'question_id' => ['required', 'exists:questions,id'],
        ];
    }
}
