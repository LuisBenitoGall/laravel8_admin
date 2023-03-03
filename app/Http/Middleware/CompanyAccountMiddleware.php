<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\CompanyAccounts;

class CompanyAccountMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        //Cuenta de la empresa:
        $co_account = session('currentCompany')? CompanyAccounts::getCompanyAccount(session('currentCompany')):true;

        //Si la cuenta ha caducado, logout:
        if(!$co_account){
            $request->session()->forget(['currentCompany']);
            $request->session()->flush();
            Auth::logout(); 
            return redirect()->route('home');
            exit; 
        }

        return $next($request);
    }
}
