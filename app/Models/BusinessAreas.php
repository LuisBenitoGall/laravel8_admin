<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BusinessAreas extends Model
{
    /**
     * 1. Configuraciones del área de negocio.
     * 2. Validación de área de negocio para middleware.
     */

    use HasFactory;
    use SoftDeletes;

    protected $table = 'business_areas';

    protected $fillable = ['name', 'slug'];

    protected $dates = ['deleted_at']; //Columna de borrado lógico

    /**
     * 1. Configuraciones del área de negocio.
     */
    public function settings($provider_id)
    {
        return BusinessAreaSettings::where('ba_id', $this->id)
            ->where('provider_id', $provider_id)
            ->first();
    }

    /**
     * 2. Validación de área de negocio para middleware.
     */
    static public function businessAreaMiddleware($ba_id, $business_relationship = false)
    {
        $company = BusinessAreas::select('company_id')->find($ba_id);

        $resp = false;

        //Si no existe:
        if (!$company) {
            $resp = trans('textos.registro_inexistente');
        }

        //Si la empresa se encuentra entre las empresas gestionadas por el usuario:
        $belongs = false;
        foreach (\Session::get('companies') as $co) {
            if ($co->id == $company->company_id) {
                $belongs = true;
                break;
            }
        }
        if (!$belongs) {
            $resp = trans('textos.no_permisos_accion');
        }

        //Comprobamos que la empresa mantiene relación de cliente o proveedor:
        if (!$belongs && $business_relationship) {
            //Verificar cliente:
            if ($business_relationship == 'customers') {
                $exist = CustomerProviders::customerProvider($company->company_id, session('currentCompany'));

            } elseif ($business_relationship == 'providers') {
                $exist = CustomerProviders::customerProvider(session('currentCompany'), $company->company_id);
            } else {
                $exist = false;
            }

            if (!$exist) {
                $resp = trans('textos.no_permisos_accion');
            }
        }

        return $resp;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_business_area_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function providerBusinessArea()
    {
        return $this->hasMany(Order::class, 'provider_business_area_id');
    }
}
