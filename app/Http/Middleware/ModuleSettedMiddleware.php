<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// Models:
use App\Models\Module;

class ModuleSettedMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $module){
        $associatedModule = Module::select('id')
            ->where('slug', $module)
            ->first();

        if(!session('modules') || !in_array($associatedModule->id, session('modules'))){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('dashboard')->with(compact('alert'));
        }
        
        return $next($request);
    }
}
