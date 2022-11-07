<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchUpdateRequest extends FormRequest
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
            'deadline' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'initial_qty' => ['required', 'numeric'],
            'doc_num' => ['nullable', 'max:255', 'string'],
            'doc' => ['nullable', 'max:255', 'string'],
            'product_id' => ['required', 'exists:products,id'],
            'work_order_id' => ['required', 'exists:work_orders,id'],
        ];
    }
}
