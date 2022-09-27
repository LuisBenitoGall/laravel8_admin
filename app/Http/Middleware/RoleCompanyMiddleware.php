<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

//Models:
use Spatie\Permission\Models\Role;

class RoleCompanyMiddleware{
    /**
     * Handle an incoming request.
     * El role asignado debe pertenecer a la empresa seleccionada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $role = Role::select('id')
            ->where('id', $request->role_id)
            ->where('company_id', $request->company_id);

        if(!$role){
            $alert = trans('textos.role_no_existe');
            return redirect()->route('companies.index')->with(compact('alert'));   
            exit; 
        }

        return $next($request);
    }
}
