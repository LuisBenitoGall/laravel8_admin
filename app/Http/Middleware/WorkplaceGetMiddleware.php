<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Workplace;

class WorkplaceGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $alert = Workplace::workplaceMiddleware($request->segments()[2]);

        if($alert){
            return redirect()->route('workplaces.index')->with(compact('alert')); 
        }
        
        return $next($request);
    }
}
