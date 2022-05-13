<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\BusinessAreas;

class BusinessAreaGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $alert = BusinessAreas::businessAreaMiddleware($request->segments()[2]);

        if($alert){
            return redirect()->route('business-areas.index')->with(compact('alert')); 
        }

        return $next($request);
    }
}
