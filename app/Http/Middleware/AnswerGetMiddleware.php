<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Answer;

class AnswerGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = Answer::select('questions.company_id')
        ->join('questions', 'answers.question_id', '=', 'questions.id')
        ->where('answers.id', $request->segments()[2])
        ->first();

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('questions.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('questions.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
