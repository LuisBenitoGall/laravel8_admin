<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserSituationMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        //Comprobamos si el usuario está activo:
        if(!auth()->user()->status){
            return redirect('/');
        }
        
        return $next($request);
    }
}
