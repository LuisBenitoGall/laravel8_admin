<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\Subtask;

class SubtaskController extends Controller{
    /**
     * 1. Guardar nueva subtarea.
     * 2. Actualizar estado de la subtarea.
     * 3. Eliminar subtarea.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';
    private $languages;
    
    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:projects');
        $this->middleware('task_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'id' => 'required|required',
                'description' => 'required'
            ]);
        }
    }

    /**
     * 1. Guardar nueva subtarea.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-subtask');

            return redirect('/admin/tasks/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $row = new Subtask();
        $row->task_id = $request->id;
        $row->description = $request->description;
        $row->user_id = Auth::user()->id;
        $row->save();

        $msg = trans('textos.subtarea_creada');
        return redirect()->route('tasks.edit', [$request->id, 'subtasks'])->with(compact('msg')); 
    }

    /**
     * 2. Actualizar estado de la subtarea.
     *
     * @param \App\subtask $subtask $status
     */
    public function status(Request $request, Subtask $subtask, $status){
        if($request->ajax()){    
            $subtask->status = $status;
            $subtask->save();

            $resp = ($subtask)? true:false;
            return response()->json($resp);
            
        }else{
            return redirect('/');
        }
    }

    /**
     * 3. Eliminar subtarea.
     */
    public function destroy(Subtask $subtask){
        $subtask->delete();    

        $msg = trans('textos.subtarea_deleted');
        return redirect()->route('tasks.edit', [$subtask->task_id, 'subtasks'])->with(compact('msg'));
    }
}
