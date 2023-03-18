<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model{
    /**
     * 1. Pagos por factura.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'payments';

    protected $dates = ['deleted_at'];

    /**
     * 1. Pagos por factura.
     */
    public static function paymentsByInvoice($invoice_id){
        return Payment::select('payments.id', 'payments.amount', 'payments.payment_date', 'payments.bank_account_id', 'payments.provider_bank_account_id', 'payments.payment_method_id', 'payments.doc', 'payments.created_at', 'payments.observations', 'payment_methods.name AS method', 'bank_accounts.account', 'banks.name AS bank', 'pba.account AS provider_account', 'pb.name AS provider_bank')
        ->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
        ->leftJoin('bank_accounts', 'payments.bank_account_id', '=', 'bank_accounts.id')
        ->leftJoin('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->leftJoin('bank_accounts AS pba', 'payments.provider_bank_account_id', '=', 'pba.id')
        ->leftJoin('banks AS pb', 'pba.bank_id', '=', 'pb.id')
        ->where('payments.invoice_id', $invoice_id)
        ->orderBy('payments.created_at', 'DESC')
        ->get();
    } 
}
