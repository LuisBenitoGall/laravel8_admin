<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        //Comprobamos si es Administrador:
        if(!auth()->user()->isAdmin){
            return redirect('/login');
        }

        return $next($request);
    }
}
