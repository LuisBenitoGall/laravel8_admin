<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Project;
use App\Models\ProjectFavourite;

class ProjectFavouriteController extends Controller{
    /**
     * 1. Activar / desactivar favorito.
     * 2. Eliminar favorito.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';

    /**
     * 1. Activar / desactivar favorito.
     */
    public function status(Request $request, $project, $status){
        if($request->ajax()){
            if($status == 'on'){
                $pf = new ProjectFavourite();
                $pf->user_id = Auth::user()->id;
                $pf->project_id = $project;
                $pf->save();
            }else{
                ProjectFavourite::where('user_id', Auth::user()->id)
                ->where('project_id', $project)
                ->delete();   
            }

            return response()->json(true);
        }    
    }

    /**
     * 2. Eliminar favorito.
     */
    public function delete($project_id){
        ProjectFavourite::where('user_id', Auth::user()->id)
        ->where('project_id', $project_id)
        ->delete();  

        $msg = trans('textos.favorito_deleted');
        return redirect()->route('projects.index')->with(compact('msg'));   
    }
}
