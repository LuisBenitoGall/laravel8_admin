<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

//Models:
use App\Models\Cheat;
use App\Models\User;

class SuperAdminValidateMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $user = User::select('id', 'isAdmin', 'status')->find($request->segments()[2]);

        //Si el usuario en sesión no es SuperAdmin, no puede otorgar permisos de SuperAdmin:
        if($request->roles && $request->roles == config('constants.ROLE_SUPERADMIN_') && !Auth::user()->hasRole('Super Admin')){
            User::where('id', Auth::user()->id)
            ->update(['isAdmin' => 0, 'status' => 0]);

            //Detectar IP:
            $ip = request()->ip();

            //Registro de tramposos.
            Cheat::create([
                'user_id' => Auth::user()->id,
                'cheat' => 'Suplantación Super Admin',
                'model_type' => 'App\Models\User',
                'ip' => $ip
            ]);

            Auth::user()->delete();

            return redirect()->route('home');
        }

        return $next($request);
    }
}
