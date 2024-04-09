<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Helpers\helpers;

class AccountingAccount extends Model{
    /**
     * 1. Obtener cuentas para autocomplete.
     * 2. Crear cuenta contable clientes y proveedores.
     * 2.1. Setear número de cuenta.
     * 3. Obtener última cuenta por tipología y empresa.
     * 4. Cuentas de IVA por pedido para asientos contables.
     * 5. Array de cuentas contables por empresa y modo.
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
            $account_types = helpers::accountingAccountSpecificPrefixes();
            $prefix = $account_types[$data['account_to']];

            //Número de la cuenta:
            $account = self::setAccountNum($prefix, $company_owner);

            if($account){
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

                //Guardando número de cliente o proveedor, a partir de los últimos números -la variable $accounting_account_digits-  de la cuenta contable:
                if($aa->mode == 1 || $aa->mode == 2){
                    $customer_ = $aa->mode == 1? $aa->company_target:$aa->company_id;
                    $provider_ = $aa->mode == 2? $aa->company_target:$aa->company_id;
                    $define = $aa->mode == 1? 'customer':'provider'; //Si la numeración se aplica a cliente o proveedor.

                    //Número customizado:
                    $num_account = substr($aa->account, -$accounting_account_digits);
                    CustomerProviders::setCustomerProviderNum($customer_, $provider_, $num_account, $define);
                }

                return $aa;  

            }else{
                return false;
            }

        }else{
            return false;
        } 
    }

    /**
     * 2.1. Setear número de cuenta.
     */
    public static function setAccountNum($prefix, $company_owner){
        //Última cuenta relativa:
        $last_account = AccountingAccount::getLastAccount($prefix, $company_owner); 

        //Nº de dígitos configurados por la empresa:
        $accounting_account_digits = CompanySettings::co_account_digits();

        if($last_account && $last_account->account){
            $account = (int)$last_account->account + 1;   
        }else{
            $pre_zeros = '';
            $z = 1;
            while($z < $accounting_account_digits){
                $pre_zeros .= '0';
                $z++;
            }
            $account = $prefix.$pre_zeros.'1';
        }

        //Comprobamos que no exista la cuenta:
        $exist_account = AccountingAccount::select('id')
        ->where('company_id', $company_owner)
        ->where('account', $account)
        ->first();  

        if($exist_account){
            return false;
        }else{
            return $account;
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

    /**
     * 4. Cuentas de IVA por pedido para asientos contables.
     */
    public static function getIvaAccountsByOrder($order){
        //Obtenemos líneas del pedido:
        $items = Item::select('id', 'qty', 'price', 'item_no_price', 'iva', 'equivalence_surcharge', 'irpf', 'discount', 'calculated_by_percent', 'days', 'days_continue', 'days_continue_discount')
        ->where('order_id', $order->id)
        ->get();

        //Importe:
        $amounts = $order->itemsAmount($items);

        dd($items, $amounts);
    }

    /**
     * 5. Array de cuentas contables por empresa y modo.
     */
    public static function arrayAccountingAccounts($company_id, $mode = false){
        $data = AccountingAccount::select('id', DB::raw("CONCAT(account, ' - ', name) as accounting_account"))
        ->where('company_id', $company_id)
        ->where('status', 1)
        ->when($mode, function($q) use($mode) {
            $q->where('mode', $mode);
        })
        ->pluck('accounting_account', 'id')
        ->toArray();   

        return $data; 
    }
}
