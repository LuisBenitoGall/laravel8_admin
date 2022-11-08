<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetSettingStoreRequest extends FormRequest
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
            'conditions' => ['nullable', 'max:255', 'string'],
            'payment_method' => ['nullable', 'max:255', 'string'],
            'observations' => ['nullable', 'max:255', 'string'],
            'footer_legend' => ['nullable', 'max:255', 'string'],
            'featured' => ['required', 'numeric'],
            'status' => ['required', 'max:255'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ];
    }
}
