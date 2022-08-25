<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CompanyUtes;

class CompanyUtesPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $companies = CompanyUtes::where('ute_id', $request->ute_id)->get();

        //Comprobando porcentajes de participación:
        if($request->participation_percent > 0){
            $percent_accumulated = $request->participation_percent;

            foreach($companies as $co){
                $percent_accumulated = $percent_accumulated + $co->participation_percent;  
            }

            //El porcentaje no puede superar el 100%
            if($percent_accumulated > 100){
                $alert = trans('textos.porcentaje_mayor_100');
                return redirect()->route('company-utes.index', $request->ute_id)->with(compact('alert'));
                exit;     
            }
        }

        return $next($request);
    }
}
