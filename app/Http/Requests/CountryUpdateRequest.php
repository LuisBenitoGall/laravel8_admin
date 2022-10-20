<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryUpdateRequest extends FormRequest
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
            'slug' => ['nullable', 'max:255', 'string'],
            'code' => ['nullable', 'max:255', 'string'],
            'alfa2' => ['nullable', 'max:2'],
            'alfa3' => ['nullable', 'max:3'],
            'flag' => ['nullable', 'max:255', 'string'],
            'status' => ['required', 'numeric'],
        ];
    }
}
