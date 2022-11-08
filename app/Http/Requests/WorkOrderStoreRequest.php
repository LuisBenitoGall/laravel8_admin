<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderStoreRequest extends FormRequest
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
            'work_order_num' => ['nullable', 'max:255', 'string'],
            'num_cardinal' => ['nullable', 'max:255', 'string'],
            'work_order_old_num' => ['nullable', 'max:255', 'string'],
            'name' => ['nullable', 'max:255', 'string'],
            'slug' => ['nullable', 'max:255', 'string'],
            'description' => ['nullable', 'max:255', 'string'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'simulation' => ['required', 'boolean'],
            'status' => ['required', 'numeric'],
            'pattern_id' => ['nullable', 'exists:work_order_patterns,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'created_by' => ['nullable', 'exists:users,id'],
            'updated_by' => ['nullable', 'exists:users,id'],
        ];
    }
}
