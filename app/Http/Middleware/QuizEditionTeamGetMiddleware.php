<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\QuizEditionTeams;

class QuizEditionTeamGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = QuizEditionTeams::select('quizzes.company_id')
        ->join('quiz_editions', 'quiz_edition_teams.quiz_edition_id', '=', 'quiz_editions.id')
        ->join('quizzes', 'quiz_editions.quiz_id', '=', 'quizzes.id')
        ->where('quiz_edition_teams.id', $request->segments()[2])
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
