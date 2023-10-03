<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

//Models:
use App\Models\Project;
use App\Models\GroupMembers;

class ProjectGetMiddleware{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        $project = Project::select('id', 'company_id', 'owner', 'team', 'groups')->find($request->segments()[2]);

        if(!$project){
            $alert = trans('textos.registro_inexistente');
            return redirect()->route('projects.index')->with(compact('alert'));
            exit; 
        } 

        if($project->company_id != session('currentCompany')){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('projects.index')->with(compact('alert'));
            exit; 
        }

        //Permisos del usuario:
        $members = $project->team? explode(',', $project->team):[];
        array_push($members, $project->owner);

        //Si el usuario en session tiene permisos de SuperAdmin, puede acceder:
        if(Auth::user()->hasRole(config('constants.SUPER_ADMIN_'))){
            array_push($members, Auth::user()->id);    
        }

        //Equipos:
        $groups = $project->groups? explode(',', $project->groups):[];

        $group_members = GroupMembers::join('users', 'group_members.user_id', '=', 'users.id')
        ->where('users.status', 1)
        ->whereIn('group_members.group_id', $groups)
        ->pluck('group_members.user_id')
        ->toArray();

        if(!in_array(Auth::user()->id, $members) && !in_array(Auth::user()->id, $group_members)){
            $alert = trans('textos.no_permisos_accion');   
            return redirect()->route('projects.index')->with(compact('alert'));
            exit;     
        }

        return $next($request);
    }
}
