<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CompanyAccounts;
use App\Models\CustomerProviders;

class CompanyAjaxMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company_id = $request->segments()[3];

        $belongs = false;

        //Si la empresa se encuentra entre las empresas gestionadas por el usuario:
        foreach(\Session::get('companies') as $row){
            if($row->id == $company_id) $belongs = true;
        }

        if(!$belongs){
            //Si la empresa en session es guardián de la empresa editada:
            $guardian = CompanyAccounts::getGuardian($company_id);

            if($guardian) $belongs = true;   
        }

        if(!$belongs){
            return false;
        }else{
            return $next($request);
        }
    }
}
