<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictIpMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $restricted_ip = "";    //Pasar string de IP´s separadas por coma.
        $ipsDeny = explode(',',preg_replace('/\s+/', '', $restricted_ip));
        if(count($ipsDeny) >= 1){
            if(in_array(request()->ip(), $ipsDeny)){
                \Log::warning("Unauthorized access, IP address was => ".request()->ip);
                return response()->json(['Unauthorized!'],400);
            }
        }

        return $next($request);
    }
}
