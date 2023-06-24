<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\ProjectGroup;
use App\Models\ProjectGroupItems;

class ProjectGroupController extends Controller{
    /**
     * 1. Guardar nueva agrupación.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:projects');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'project_id' => 'required|integer',
                'title' => 'required_without:group|max:255'
            ]);
        }
    }

    /**
     * 1. Guardar nueva agrupación.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-set-group');

            return redirect('/admin/projects/'.$request->project_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->group){
            $g = ProjectGroup::find($request->group);
        }else{
            $g = new ProjectGroup();
            $g->company_id = session('currentCompany');
            $g->title = $request->title;
            $g->created_by = Auth::user()->id;
            $g->save();
        }

        //Integrando el proyecto en el grupo:
        $gi = ProjectGroupItems::firstOrCreate(
            ['group_id' => $g->id, 'project_id' => $request->project_id],
            ['group_id' => $g->id, 'project_id' => $request->project_id, 'created_by' => Auth::user()->id]
        );

        $msg = trans('textos.proyecto_vinculado');
        return redirect()->route('projects.edit', [$request->project_id, 'groups'])->with(compact('msg'));
    }
}
