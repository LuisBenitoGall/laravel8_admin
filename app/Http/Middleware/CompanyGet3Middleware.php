<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompanyGet3Middleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        //La empresa debe pertenecer a las empresas vinculadas al usuario.
        $company_id = isset($request->segments()[3])? $request->segments()[3]:session('currentCompany');

        if(!$company_id){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('companies.index')->with(compact('alert'));
            exit;
        }

        //Si la empresa se encuentra entre las empresas gestionadas por el usuario:
        $belongs = false;
        foreach(\Session::get('companies') as $co){
            if($co->id == $company_id) $belongs = true; break;
        }

        return $next($request);
    }
}


