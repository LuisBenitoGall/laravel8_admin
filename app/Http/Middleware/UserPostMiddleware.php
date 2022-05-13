<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

//Models:
use App\Models\User;

class UserPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        User::userMiddleware($request->user_id);

        return $next($request);
    }
}
