<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CompanySettings;

class ManagementCustomersMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company_settings = CompanySettings::where('company_id', session('currentCompany'))->first();

        if(!$company_settings || $company_settings->customers_management == 0){
            $alert = trans('textos.clientes_gestion_off');
            return redirect()->route('company-settings.index')->with(compact('alert'));
        }

        return $next($request);
    }
}
