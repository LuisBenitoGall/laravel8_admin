<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class CompanyAccounts extends Model{
    /**
     * 1. Obtener cuenta por empresa.
     * 2. Verificar situación de guardián de empresa.
     */
    
    use HasFactory;
    use SoftDeletes;

    protected $table = 'company_accounts';

    protected $fillable = ['company_id', 'guardian', 'account_id', 'start_date', 'end_date', 'price', 'payment_date', 'status'];   

    /**
     * 1. Obtener cuenta por empresa.
     */
    public static function getCompanyAccount($company_id, $status = 1){
        return CompanyAccounts::where('company_id', $company_id)
        ->where('status', $status)
        ->orderBy('id', 'DESC')
        ->first();
    } 

    /**
     * 2. Verificar situación de guardián de empresa.
     */
    public static function getGuardian($company_id){
        return CompanyAccounts::where('company_id', $company_id)
        ->where('guardian', session('currentCompany'))
        ->where('status', 1)
        ->where('end_date', '>=', date('Y-m-d'))
        ->first();
    }
    
}
