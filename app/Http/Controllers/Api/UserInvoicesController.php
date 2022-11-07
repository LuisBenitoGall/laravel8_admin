<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;

class UserInvoicesController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        // $this->authorize('view', $user);

        $invoices = $user
            ->invoices()
            ->latest()
            ->paginate();

        return new InvoiceCollection($invoices);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        //$this->authorize('create', Invoice::class);

        $validated = $request->validate([
            'refund' => ['required', 'max:255'],
            'fiscal_date' => ['required', 'date'],
            'expiration_date' => ['required', 'date'],
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
        ]);

        $invoice = $user->invoices()->create($validated);

        return new InvoiceResource($invoice);
    }
}
