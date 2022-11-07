<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\InvoiceSetting;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;

class InvoiceSettingInvoicesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoiceSetting $invoiceSetting
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, InvoiceSetting $invoiceSetting)
    {
        // $this->authorize('view', $invoiceSetting);

        $invoices = $invoiceSetting
            ->invoices()
            ->latest()
            ->paginate();

        return new InvoiceCollection($invoices);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoiceSetting $invoiceSetting
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, InvoiceSetting $invoiceSetting)
    {
        //$this->authorize('create', Invoice::class);

        $validated = $request->validate([
            'refund' => ['required', 'max:255'],
            'fiscal_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date'],
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
        ]);

        $invoice = $invoiceSetting->invoices()->create($validated);

        return new InvoiceResource($invoice);
    }
}
