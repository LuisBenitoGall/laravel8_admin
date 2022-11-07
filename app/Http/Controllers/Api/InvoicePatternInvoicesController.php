<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\InvoicePattern;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;

class InvoicePatternInvoicesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoicePattern $invoicePattern
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, InvoicePattern $invoicePattern)
    {
        // $this->authorize('view', $invoicePattern);

        $invoices = $invoicePattern
            ->invoices()
            ->latest()
            ->paginate();

        return new InvoiceCollection($invoices);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InvoicePattern $invoicePattern
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, InvoicePattern $invoicePattern)
    {
        //$this->authorize('create', Invoice::class);

        $validated = $request->validate([
            'refund' => ['required', 'max:255'],
            'fiscal_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date'],
            'user_id' => ['required', 'exists:users,id'],
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
        ]);

        $invoice = $invoicePattern->invoices()->create($validated);

        return new InvoiceResource($invoice);
    }
}
