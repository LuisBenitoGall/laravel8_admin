<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class BankAccounts extends Model{
    /**
     * 1. Cuentas bancarias por empresa.
     * 2. Cuentas bancarias por empresa (array).
     */

    use HasFactory;
    use SoftDeletes;

    /**
     * 1. Cuentas bancarias por empresa.
     */
    public static function getBankAccounts($company_id, $status = true){
        return BankAccounts::select(DB::raw("CONCAT(banks.name,' ',bank_accounts.iban,'-',bank_accounts.entity,'-',bank_accounts.office,'-',bank_accounts.dc,'-',bank_accounts.digits) as cuenta"), 'banks.tradename', 'bank_accounts.id', 'bank_accounts.account', 'bank_accounts.featured')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->where('bank_accounts.status', $status)
        ->where('bank_accounts.company_id', $company_id)
        ->orderBy('bank_accounts.featured', 'DESC')
        ->orderBy('banks.name', 'ASC')
        ->get();
    }

    /**
     * 2. Cuentas bancarias por empresa (array).
     */
    public static function getBankAccountsArray($company_id){
        return BankAccounts::select(DB::raw("CONCAT(banks.name,' ',bank_accounts.iban,'-',bank_accounts.entity,'-',bank_accounts.office,'-',bank_accounts.dc,'-',bank_accounts.digits) as cuenta"), 'bank_accounts.id')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->where('bank_accounts.company_id', $company_id)
        ->where('bank_accounts.status', 1)
        ->pluck('cuenta', 'bank_accounts.id')
        ->toArray();
    }
}
