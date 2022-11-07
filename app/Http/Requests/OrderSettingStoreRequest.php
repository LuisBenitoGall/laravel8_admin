<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderSettingStoreRequest extends FormRequest
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
            'company_id' => ['nullable', 'exists:companies,id'],
            'conditions' => ['nullable', 'max:255', 'string'],
            'payment_method' => ['nullable', 'max:255', 'string'],
            'observations' => ['nullable', 'max:255', 'string'],
            'footer_legend' => ['nullable', 'max:255', 'string'],
            'featured' => ['required', 'numeric'],
            'status' => ['required', 'numeric'],
        ];
    }
}
