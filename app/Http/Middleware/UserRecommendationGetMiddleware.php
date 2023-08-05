<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\UserRecommendation;

class UserRecommendationGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $recommendation = UserRecommendation::select('id', 'user_id')
        ->find($request->segments()[2]);

        if(!$recommendation){
            $alert = trans('textos.contenido_no_existe');
            return redirect()->route('user-recommendations.index')->with(compact('alert'));   
            exit;     
        }

        if($recommendation && $recommendation->user_id != Auth::user()->id){
            $alert = trans('textos.no_permisos_accion');
            return redirect()->route('user-recommendations.index')->with(compact('alert'));     
        }

        return $next($request);
    }
}
