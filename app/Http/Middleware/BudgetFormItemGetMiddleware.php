<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\BudgetFormItems;

class BudgetFormItemGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = BudgetFormItems::select('budget_forms.company_id')
        ->join('budget_forms', 'budget_form_items.form_id', '=', 'budget_forms.id')
        ->where('budget_form_items.id', $request->segments()[2])
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('budget-forms.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('budget-forms.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
