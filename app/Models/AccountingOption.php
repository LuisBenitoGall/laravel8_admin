<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class AccountingOption extends Model{
    /**
     * 1. Opciones contables por empresa.
     */

    use HasFactory;

    protected $table = 'accounting_options';

    /**
     * 1. Opciones contables por empresa.
     */
    public static function getCompanyOptions($company_id = false){
        $companyId = $company_id? $company_id:Session::get('currentCompany');

        return AccountingOption::where('company_id', $companyId)
        ->first();
    }
}

