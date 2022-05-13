<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CustomerProviders;

class CustomerProviderMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $cp = CustomerProviders::find($request->segments()[2]);

        //Comprobamos que la empresa en sesión sea cliente o proveedor:
        if($cp->customer_id != session('currentCompany') && $cp->provider_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('companies.index')->with(compact('alert')); 
        }

        return $next($request);
    }
}
