<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\BudgetOrderSharedFields;

class BudgetPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = BudgetOrderSharedFields::select('provider_id')
        ->where('budget_id', $request->budget_id)
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('budgets.index')->with(compact('alert'));
            exit; 
        } 

        if($company->provider_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('budgets.index')->with(compact('alert'));
            exit; 
        }        

        return $next($request);
    }
}
