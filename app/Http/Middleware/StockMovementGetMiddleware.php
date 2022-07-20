<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\StockMovements;

class StockMovementGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $company = StockMovements::select('company_id', 'basic')
        ->where('id', $request->segments()[2])
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('stock-movements.index')->with(compact('alert'));
            exit; 
        } 

        if((!$company->company_id && $company->basic) || $company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('stock-movements.index')->with(compact('alert'));
            exit;     
        }

        return $next($request);
    }
}
