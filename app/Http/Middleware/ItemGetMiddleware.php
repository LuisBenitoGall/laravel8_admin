<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Item;

class ItemGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = Item::select('budget_order_shared_fields.provider_id', 'budget_order_shared_fields.customer_id')
        ->join('budget_order_shared_fields', 'items.budget_order_id', '=', 'budget_order_shared_fields.id')
        ->where('items.id', $request->segments()[2])
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('budgets.index')->with(compact('alert'));
            exit; 
        } 

        if($company->provider_id != session('currentCompany') && $company->customer_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('budgets.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
