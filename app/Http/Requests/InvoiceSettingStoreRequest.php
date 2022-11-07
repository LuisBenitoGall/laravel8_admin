<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceSettingStoreRequest extends FormRequest
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
            'company_id' => ['required', 'exists:companies,id'],
            'conditions' => ['required', 'max:255', 'string'],
            'payment_method' => ['nullable', 'max:255', 'string'],
            'observations' => ['nullable', 'max:255', 'string'],
            'featured' => ['nullable', 'numeric'],
            'status' => ['nullable', 'numeric'],
        ];
    }
}
