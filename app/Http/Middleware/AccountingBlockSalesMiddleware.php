<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\AccountingOption;

class AccountingBlockSalesMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $data = AccountingOption::getCompanyOptions();

        if($data){
            //Cierre mensual ventas activo:
            if($data->monthly_closing_sales){
                $mes = date('m');
                $any = date('Y');
                
                $time = strtotime($data->monthly_closing_sales);
                $month = date("m",$time);
                $year = date("Y",$time);    
            
                if($month == $mes && $year == $any){ 
                    $alert = trans('textos.cierre_mensual_ventas_aviso');
                    return redirect()->route('invoices.index')->with(compact('alert'));
                    exit;
                }
            }
        }

        return $next($request);
    }
}
