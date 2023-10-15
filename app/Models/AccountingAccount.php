<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class AccountingAccount extends Model{
    /**
     * 1. Obtener cuentas para autocomplete.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'accounting_accounts';

    protected $dates = ['deleted_at'];

    /**
     * 1. Obtener cuentas para autocomplete.
     */
    public static function getAccounts($search){
        return AccountingAccount::select('id', 'account', 'name')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->where(function($query) use ($search){
            $query->where('name', 'like', '%'.$search.'%')
            ->orWhere('account', 'like', '%'.$search.'%');
        })
        ->orderBy('name', 'ASC')
        ->limit(15)
        ->get();
    }
}
