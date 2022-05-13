<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\WorkOrderProducts;

class WorkOrderProductPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $company = WorkOrderProducts::select('work_orders.company_id')
        ->join('work_orders', 'work_order_products.work_order_id', '=', 'work_orders.id')
        ->where('work_order_products.id', $request->id)
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('work-orders.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('work-orders.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
