<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

//Models:
use App\Models\User;

class UserGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        if (Auth::user()->id != $request->segments()[2]) {
            $user = User::find($request->segments()[2]);

            //El usuario debe pertenecer a la empresa en sesión:
            $companies = $user->companies();
            $exist = false;
            foreach ($companies as $c) {
                if ($c->id == session('currentCompany')) {
                    $exist = true;
                    break;
                }
            }
            if(!$exist && !Auth::user()->hasRole(config('constants.SUPER_ADMIN_'))){
                $alert = trans('textos.no_permisos_edicion');
                return redirect()->route('users.index')->with(compact('alert'));
                exit;
            }

            //Si es Super Admin:
            if (Auth::user()->hasRole(config('constants.SUPER_ADMIN_'))) {
                //Para editar a otro Super Admin, comprobamos que tenga permisos de Omni User:
                if ($user->hasRole(config('constants.SUPER_ADMIN_')) && !in_array(Auth::user()->id, config('constants.OMNI_USERS_'))) {
                    $alert = trans('textos.no_permisos_edicion');
                    return redirect()->route('users.index')->with(compact('alert'));
                }

            //No es Super Admin:
            } else {
                //Un rol NO Super Admin no puede editar a un Super Admin
                if ($user->hasRole(config('constants.SUPER_ADMIN_'))) {
                    $alert = trans('textos.no_permisos_edicion');
                    return redirect()->route('users.index')->with(compact('alert'));
                }

                //Comprobamos que tenga permiso de edición de usuarios:
                if (!Auth::user()->hasPermissionTo('users.edit')) {
                    $alert = trans('textos.no_permisos_edicion');
                    return redirect()->route('users.index')->with(compact('alert'));
                }
            }
        }

        return $next($request);
    }
}
