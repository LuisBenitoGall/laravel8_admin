<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Helpers;

class InvoiceModalityMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $modalities = helpers::invoiceModalities();

        if(!in_array($request->modality, $modalities)){
            if(!is_numeric($request->modality)){
                $alert = trans('textos.no_permisos_accion');   
                return redirect()->route('invoices.index')->with(compact('alert'));
                exit;     
            }
        } 

        return $next($request);
    }
}
