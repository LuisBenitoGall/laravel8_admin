<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

//Models:
use App\Models\Quiz;
use App\Models\QuizEditions;

class QuizEditionShowMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next){
        $edition = QuizEditions::select('quiz_editions.id', 'quiz_editions.status AS qe_status', 'quizzes.status')
        ->join('quizzes', 'quiz_editions.quiz_id', '=', 'quizzes.id')
        ->where('quiz_editions.id', $request->segments()[2])
        ->first();

        if(!$edition){
            $alert = trans('textos.concurso_no_existe');
            return redirect()->route('dashboard')->with(compact('alert'));
            exit;     
        }

        if(!$edition->qe_status || !$edition->status){
            $alert = trans('textos.concurso_no_activo');
            return redirect()->route('dashboard')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
