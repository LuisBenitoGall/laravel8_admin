<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Order;
use App\Models\Project;

class ProjectPurchasesMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = Project::select('company_id')->find($request->project_id);

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('projects.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('projects.index')->with(compact('alert'));
            exit; 
        }

        //La empresa debe ser cliente del pedido:
        $company_ = Order::select('customer_id')
        ->where('id', $request->order_id)
        ->where('customer_id', session('currentCompany'))
        ->first();

        if(!$company_){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('orders.index')->with(compact('alert'));
            exit; 
        } 

        return $next($request);
    }
}
