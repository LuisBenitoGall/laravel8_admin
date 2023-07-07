<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Cash;
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
        $company = Item::select('orders.provider_id', 'orders.customer_id')
        ->join('orders', 'items.order_id', '=', 'orders.id')
        ->where('items.id', $request->segments()[2])
        ->first();

        //Comprobando empresa por Cash:
        if(!$company){
            $company = Item::select('workplaces.company_id AS provider_id')
            ->join('cashes', 'items.cash_id', '=', 'cashes.id')
            ->join('workplaces', 'cashes.workplace_id', '=', 'workplaces.id')
            ->find($request->segments()[2]);
        }

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
