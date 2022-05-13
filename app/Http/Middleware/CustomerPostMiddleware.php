<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CustomerProviders;

class CustomerPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        //Comprobamos que sea cliente de la empresa en sesión:
        $exist = CustomerProviders::select('id')
        ->where('customer_id', $request->customer_id)
        ->where('provider_id', session('currentCompany'))
        ->first();

        if(!$exist){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('customers.index')->with(compact('alert'));     
        }
        
        return $next($request);
    }
}
