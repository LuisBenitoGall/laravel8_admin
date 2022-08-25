<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Models:
use App\Models\UserCompanies;

class UserCompaniesPost2Middleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $company = UserCompanies::select('company_id')
        ->where('user_id', $request->user_id)
        ->where('company_id', session('currentCompany'))
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('users.index')->with(compact('alert'));
            exit; 
        } 

        return $next($request);
    }
}
