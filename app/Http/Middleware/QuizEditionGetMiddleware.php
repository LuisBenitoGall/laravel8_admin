<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\QuizEditions;

class QuizEditionGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = QuizEditions::select('quizzes.company_id')
        ->join('quizzes', 'quiz_editions.quiz_id', '=', 'quizzes.id')
        ->where('quiz_editions.id', $request->segments()[2])
        ->first();

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

        return $next($request);
    }
}
