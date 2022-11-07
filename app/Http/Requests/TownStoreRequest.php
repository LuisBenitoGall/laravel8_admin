<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TownStoreRequest extends FormRequest
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
            'province_id' => ['required', 'exists:provinces,id'],
            'name' => ['required', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'status' => ['nullable', 'numeric'],
        ];
    }
}
