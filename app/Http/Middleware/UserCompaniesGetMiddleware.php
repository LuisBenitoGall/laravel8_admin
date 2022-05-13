<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// Models:
use App\Models\UserCompanies;

class UserCompaniesGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = UserCompanies::select('company_id')
        ->where('user_id', $request->segments()[2])
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
