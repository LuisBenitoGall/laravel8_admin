<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use Spatie\Permission\Models\Role;

class RoleGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $role = Role::find($request->segments()[2]); 

        if(!$role){
            $alert = trans('textos.role_no_existe');
            return redirect()->route('roles.index')->with(compact('alert'));   
            exit; 
        }

        if($role->company_id && $role->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('budgets.index')->with(compact('alert'));    
        }

        return $next($request);
    }
}
