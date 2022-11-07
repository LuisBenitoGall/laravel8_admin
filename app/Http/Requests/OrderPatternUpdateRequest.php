<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderPatternUpdateRequest extends FormRequest
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
            'pattern' => ['required', 'max:255', 'string'],
            'yearly_reset' => ['required', 'boolean'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'status' => ['required', 'numeric'],
        ];
    }
}
