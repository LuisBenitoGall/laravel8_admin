<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use File;

//Models:
use App\Models\Task;
use App\Models\TaskMessages;

class TaskMessagesController extends Controller{
    /**
     * 1. Guardar mensaje.
     * 2. Eliminar mensaje.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';

    public function __construct(){
        $this->middleware('module_setted:projects');
        $this->middleware('task_get', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'message' => 'required|string',
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required'
            ]);
        }
    }

    /**
     * 1. Guardar mensaje.
     *
     * @param $request, $task
     */
    public function store(Request $request, Task $task){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-message');

            return redirect('/admin/tasks/'.$task->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        //Destinatarios:
        $destinatarios = $request->to? serialize($request->to):NULL;

        $m = new TaskMessages();
        $m->message = $request->message;
        $m->task_id = $task->id;
        $m->from = auth()->user()->id;
        $m->to = $destinatarios;
        $m->save();

        $msg = trans('textos.mensaje_enviado');
        return redirect()->route('tasks.edit', $task->id)->with(compact('msg'));
    }

    /**
     * 2. Eliminar mensaje.
     *
     * @param  obj $doc
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskMessages $message){
        if($message != ''){
            $message->delete();

            $msg = trans('textos.mensaje_deleted');
            return redirect()->route('tasks.edit', $message->task_id)->with(compact('msg')); 
        
        }else{
            return redirect('/');
        } 
    }    
}
