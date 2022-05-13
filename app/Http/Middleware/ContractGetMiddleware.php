<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Contract;

class ContractGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = Contract::select('company_id')->find($request->segments()[2]);

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('contracts.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('contracts.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
