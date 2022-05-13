<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Workplace;

class WorkplacePostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $alert = Workplace::workplaceMiddleware($request->workplace_id, $request->business_relationship);

        if($alert){
            return redirect()->route('workplaces.index')->with(compact('alert')); 
        }

        return $next($request);
    }
}
