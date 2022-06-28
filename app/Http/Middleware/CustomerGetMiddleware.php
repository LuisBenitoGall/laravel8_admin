<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CompanyAccounts;
use App\Models\CustomerProviders;

class CustomerGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company_id = $request->segments()[2];

        $relation = CustomerProviders::customerProvider($company_id, session('currentCompany'));

        if(!$company_id || !$relation){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('customers.index')->with(compact('alert'));
            exit;
        }

        //Si la empresa se encuentra entre las empresas gestionadas por el usuario:
        $belongs = false;
        foreach(\Session::get('companies') as $co){
            if($co->id == $company_id){
                $belongs = true; 
                break;
            }
        }

        //Si la empresa en session es guardián del cliente:
        if(!$belongs){
            $guardian = CompanyAccounts::where('company_id', $company_id)
            ->where('guardian', session('currentCompany'))
            ->where('status', 1)
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();

            if($guardian) $belongs = true;
        }

        if(!$belongs){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('customers.index')->with(compact('alert'));
            exit;
        }

        return $next($request);
    }
}
