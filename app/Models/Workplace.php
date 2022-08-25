<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workplace extends Model{
    /**
	 * 1. Empresa del centro de trabajo. 
     * 2. Validación del centro de trabajo para middleware.
     * 3. Centros de trabajo por empresa.
	 */

    use HasFactory;
    use SoftDeletes;

	protected $table = 'workplaces';

    protected $fillable = ['company_id', 'name', 'slug', 'featured', 'logo', 'address', 'cp', 'town_id', 'nif', 'website', 'description'];    

    /**
     * 1. Empresa del centro de trabajo.
     */
    public function company(){
    	return $this->belongsTo(Company::class);
    }	

    /**
     * 2. Validación del centro de trabajo para middleware.
     */
    static public function workplaceMiddleware($workplace_id, $business_relationship = false){
        $company = Workplace::select('company_id')->find($workplace_id);

        $resp = false;

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
            
        //Comprobamos que la empresa mantiene relación de cliente o proveedor y es guardián:
        if(!$belongs && $business_relationship){
            $exist = false;

            //Si la empresa en session es guardián de la empresa editada:
            $guardian = CompanyAccounts::getGuardian($company->company_id);
            if($guardian) $exist = true;

            //Verificar cliente:
            if(!$exist && $business_relationship == 'customers'){
                $exist = CustomerProviders::customerProvider($company->company_id, session('currentCompany'));

            }elseif(!$exist && $business_relationship == 'providers'){
                $exist = CustomerProviders::customerProvider(session('currentCompany'), $company->company_id);
            }

            if(!$exist){
                $resp = trans('textos.no_permisos_accion');
            }
        }

        return $resp;
    }

    /**
     * 3. Centros de trabajo por empresa.
     */
    public static function companyWorkplaces($company_id){
        $data = Workplace::select('workplaces.id', 'workplaces.name', 'towns.name AS Town')
        ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
        ->where('workplaces.company_id', $company_id)
        ->where('workplaces.status', 1)
        ->orderBy('workplaces.name', 'ASC')
        ->get();   

        return $data; 
    }
}
