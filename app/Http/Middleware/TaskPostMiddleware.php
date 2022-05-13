<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Task;

class TaskPostMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $company = Task::select('projects.company_id')
        ->join('projects', 'tasks.project_id', '=', 'projects.id')
        ->where('tasks.id', $request->id)
        ->first();    

        if(!$company){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('projects.index')->with(compact('alert'));
            exit; 
        } 

        if($company->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('projects.index')->with(compact('alert'));
            exit; 
        }

        return $next($request);
    }
}
