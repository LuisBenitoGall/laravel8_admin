<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceUpdateRequest extends FormRequest
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
            'refund' => ['required', 'max:255'],
            'fiscal_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date'],
            'order_id' => ['required', 'exists:orders,id'],
            'user_id' => ['required', 'exists:users,id'],
            'pattern_id' => ['required', 'exists:invoice_patterns,id'],
            'observations' => ['required', 'max:255', 'string'],
            'printed' => ['required', 'numeric'],
            'status' => ['required', 'numeric'],
            'invoice_num' => ['required', 'max:255', 'string'],
            'num_cardinal' => ['required', 'max:255', 'string'],
            'invoice_old_num' => ['required', 'max:255', 'string'],
            'amount' => ['required', 'numeric'],
            'with_iva' => ['required', 'numeric'],
            'amount_with_iva' => ['required', 'numeric'],
            'doc' => ['required', 'max:255', 'string'],
            'blocked' => ['required', 'boolean'],
            'template_id' => ['required', 'exists:invoice_settings,id'],
        ];
    }
}
