<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Quiz;

class QuizGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = Quiz::select('company_id', 'owner', 'managers')->find($request->segments()[2]);

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('quizzes.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('quizzes.index')->with(compact('alert'));
            exit; 
        }

        //Sólo acceso para propietario o managers:
        $managers = $company->managers? explode(',', $company->managers):[];
        if($company->owner != Auth::user()->id && !in_array(Auth::user()->id, $managers)){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('quizzes.index')->with(compact('alert'));
            exit;     
        }

        return $next($request);
    }
}
