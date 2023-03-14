<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Invoice;

class InvoicePostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $company = Invoice::select('orders.provider_id', 'orders.customer_id')
        ->join('orders', 'invoices.order_id', '=', 'orders.id')
        ->where('invoices.id', $request->invoice_id)
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('invoices.index')->with(compact('alert'));
            exit; 
        } 

        if($company->provider_id != session('currentCompany') && $company->customer_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('invoices.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
