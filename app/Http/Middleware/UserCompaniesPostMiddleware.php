<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\UserCompanies;

class UserCompaniesPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        //Comprobamos que la empresa transferida esté asociada al usuario de la sesión:
        $exist = UserCompanies::where('user_id', Auth::user()->id)
        ->where('company_id', $request->company_id)
        ->first();

        if(!$exist){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('companies.index')->with(compact('alert'));    
        }

        return $next($request);
    }
}
