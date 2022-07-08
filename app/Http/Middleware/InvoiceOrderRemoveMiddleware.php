<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Invoice;
use App\Models\Order;

class InvoiceOrderRemoveMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $company1 = Invoice::select('orders.provider_id')
        ->join('orders', 'invoices.order_id', '=', 'orders.id')
        ->where('invoices.id', $request->segments()[2])
        ->first();

        $company2 = Order::select('provider_id')->find($request->segments()[4]);

        if(!$company1 || !$company2){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('invoices.index')->with(compact('alert'));
            exit; 
        } 

        if($company1->provider_id != session('currentCompany') || $company2->provider_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('invoices.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
