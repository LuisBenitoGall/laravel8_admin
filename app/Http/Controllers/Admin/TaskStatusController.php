<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

//Models:
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;

class TaskStatusController extends Controller{
    /**
     * 1. Guardar status de proyecto.
     * 2. Eliminar status de proyecto.
     * 3. Ordenar columnas.
     */
    
    public function __construct(){
        $this->middleware('module_setted:projects');
    }
    
    /**
     * 1. Guardar status de proyecto.
     */
    public function store(Request $request){
        if($request->ajax()){
            if($request->name){
                //Slug:
                $slug = Str::slug($request->name);

                //El valor debe ser único:
                $exist = TaskStatus::select('id')
                ->where('slug', $slug)
                ->where('company_id', session('currentCompany'))
                ->get();

                if($exist->count() > 0){
                    return response()->json('duplicado');
                }else{
                    $last_order = TaskStatus::select('order')
                    ->where('company_id', session('currentCompany'))
                    ->orderBy('order', 'DESC')
                    ->first();

                    $ps = new TaskStatus();
                    $ps->name = $request->name;
                    $ps->slug = $slug;
                    $ps->company_id = session('currentCompany');
                    $ps->order = $last_order? $last_order->order + 1:1;
                    $ps->save();

                    return response()->json($ps);
                } 
            }else{
                return false;
            }
        }   
    }

    /**
     * 2. Eliminar status de proyecto.
     */
    public function delete(Project $project, TaskStatus $status){
        if(!$project || !$status){
            return redirect('/');
            exit;
        }

        //Eliminamos status:
        $status->delete();

        //Reasignamos las tareas que se encontraban en el estado eliminado al primer estado posible:
        $new_status = TaskStatus::where('company_id', session('currentCompany'))
        ->orderBy('order', 'ASC')->first();

        if($new_status){
            //Update:
            Task::join('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.status', $status->slug)
            ->where('projects.company_id', session('currentCompany'))
            ->update(['tasks.status' => $new_status->slug]);
        }

        $msg = trans('textos.columna_deleted');
        return redirect()->route('projects.edit', [$project, 'tasks'])->with(compact('msg'));   
    }

    /**
     * 3. Ordenar columnas.
     */
    public function orderColumns(Request $request){
        if($request->ajax()){

            //dd($request->items);

            $p = explode('&', $request->items);
            $n = count($p);

            for($i=0; $i<$n; $i++){
                $p1 = explode('=', $p[$i]);
                $ps = TaskStatus::find($p1[1]);
                $ps->order = ($i+1);
                $ps->save();
            }
        }   
    }
}
