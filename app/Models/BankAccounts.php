<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccounts extends Model{
    /**
     * 1. Cuentas bancarias por empresa.
     */

    use HasFactory;
    use SoftDeletes;

    /**
     * 1. Cuentas bancarias por empresa.
     */
    public static function getBankAccounts($company_id, $status = true){
        $data = BankAccounts::select('bank_accounts.*', 'banks.name', 'banks.tradename')
        ->join('banks', 'bank_accounts.bank_id', '=', 'banks.id')
        ->where('bank_accounts.status', $status)
        ->where('bank_accounts.company_id', $company_id)
        ->orderBy('bank_accounts.featured', 'DESC')
        ->orderBy('banks.name', 'ASC')
        ->get();

        return $data;
    }
}
