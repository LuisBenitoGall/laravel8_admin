<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CostCenters extends Model{
    /**
     * 1. Validación de centro de coste para middleware.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'cost_centers';

    protected $fillable = ['name', 'slug'];

    /**
     * 1. Validación de centro de coste para middleware.
     */
    static public function costCenterMiddleware($cc_id, $business_relationship = false){
        $company = CostCenters::select('company_id')->find($cc_id);

        $resp = false;

        //Si no existe:
        if(!$company){
            $resp = trans('textos.registro_inexistente');
        }

        //Si la empresa se encuentra entre las empresas gestionadas por el usuario:
        $belongs = false;
        foreach(\Session::get('companies') as $co){
            if($co->id == $company->company_id){
                $belongs = true;
                break;
            }
        }
        if(!$belongs){
            $resp = trans('textos.no_permisos_accion');
        }

        //Comprobamos que la empresa mantiene relación de cliente o proveedor:
        if(!$belongs && $business_relationship){
            //Verificar cliente:
            if($business_relationship == 'customers'){
                $exist = CustomerProviders::customerProvider($company->company_id, session('currentCompany'));

            }elseif($business_relationship == 'providers'){
                $exist = CustomerProviders::customerProvider(session('currentCompany'), $company->company_id);
            }else{
                $exist = false;
            }

            if(!$exist){
                $resp = trans('textos.no_permisos_accion');
            }
        }

        return $resp;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_cost_center_id');
    }

    public function providerCostCenter()
    {
        return $this->hasMany(Order::class, 'provider_cost_center_id');
    }


}
