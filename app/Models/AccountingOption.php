<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class AccountingOption extends Model{
    /**
     * 1. Validez de factura.
     */

    use HasFactory;

    protected $table = 'accounting_options';

    /**
     * 1. Validez de factura.
     *
     * Comprobamos que la factura tenga una fecha que no esté bloqueada.
     */
    public static function validInvoiceDate($date, $type, $company_id){
        $p = explode('-', $date);

        return AccountingOption::select('id')
        ->where('company_id', $company_id)
        ->where('type', $type)
        ->where('year', $p[0])
        ->where('month', $p[1])
        ->first();
    }
}

