<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccountingAccount extends Model{
    /**
     * 1. Obtener cuentas para autocomplete.
     * 2. Crear cuenta contable clientes y proveedores.
     * 3. Obtener última cuenta por tipología y empresa.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'accounting_accounts';

    protected $dates = ['deleted_at'];

    public function user(){
        return $this->belongsTo(User::class, 'created_by');
    }

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
        ->groupBy('account')
        ->orderBy('name', 'ASC')
        ->limit(15)
        ->get();
    }

    /**
     * 2. Crear cuenta contable clientes y proveedores.
     */
    public static function setAccountingAccount($data, $company_owner = false){
        $company_target = Company::select('id', 'name')->find($data['company_target']);

        if($company_target){
            //Empresa propietaria:
            $company_owner = $company_owner? $company_owner:session('currentCompany');

            //Tipos de cuentas específicos:
            $account_types = [
                'provider' => '4000',
                'creditor' => '4100',
                'customer' => '4300'
            ];
            $prefix = $account_types[$data['account_to']];

            //Última cuenta relativa:
            $last_account = AccountingAccount::getLastAccount($prefix, $company_owner); 

            if($last_account && $last_account->account){
                $account = (int)$last_account->account + 1;   
            }else{
                $pre_zeros = '';
                $z = 1;
                //Nº de dígitos configurados por la empresa:
                $accounting_account_digits = CompanySettings::co_account_digits();
                while($z < $accounting_account_digits){
                    $pre_zeros .= '0';
                    $z++;
                }
                $account = $prefix.$pre_zeros.'1';
            }

            //Tipo balance:
            //18/12/23: revisar type (activo, pasivo, ganancias-pérdidas con Didac);
            //$type = $data['account_to'] == 'customer'? 'c':'p';
            $type = NULL;

            //Cuenta principal:
            $featured_account = AccountingAccount::select('id', 'featured')
            ->where('company_id', $company_owner)
            ->where('company_target', $data['company_target'])
            ->where('mode', $data['mode'])
            ->where('featured', 1)
            ->first();
            $featured = $featured_account? false:true;

            $aa = new AccountingAccount();
            $aa->company_id = $company_owner;
            $aa->account = $account;
            $aa->name = $company_target->name? $company_target->name:'';
            $aa->mode = $data['mode'];
            $aa->type = $type;
            //$aa->earnings_loses = $earnings_loses;
            $aa->company_target = $data['company_target'];
            $aa->status = 1;
            $aa->featured = $featured;
            $aa->with_iva = $data['with_iva'];
            $aa->created_by = Auth::user()->id;
            $aa->save(); 

            return $aa;  

        }else{
            return false;
        } 
    }

    /**
     * 3. Obtener última cuenta por tipología y empresa.
     */
    public static function getLastAccount($prefix, $company_id){
        return AccountingAccount::select('id', 'account')
            ->where('company_id', $company_id)
            ->where('account', 'LIKE', $prefix.'%')
            ->orderBy('account', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->first();          
    }
}
