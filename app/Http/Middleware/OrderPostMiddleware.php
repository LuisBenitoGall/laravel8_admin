<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Order;

class OrderPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $company = Order::select('provider_id', 'customer_id')
        ->where('id', $request->order_id)
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('orders.index')->with(compact('alert'));
            exit; 
        } 

        if($company->provider_id != session('currentCompany') && $company->customer_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('orders.index')->with(compact('alert'));
            exit; 
        }      

        return $next($request);
    }
}
