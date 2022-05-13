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
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskMessages;
use App\Models\TaskStatus;
use App\Models\User;

class TaskController extends Controller{
    /**
     * 1. Tareas por proyecto.
     * 2. Guardar tarea.
     * 3. Editar tarea.
     * 4. Actualizar tarea.
     * 5. Actualizar estado de la tarea.
     * 6. Ordenar columna de tareas.
     * 7. Guardar tiempo de trabajo en tarea.
     * 8. Obtener tiempos de trabajo de la tarea.
     * 9. Eliminar tiempo de trabajo de la tarea.
     * 10. Tareas por miembro de equipo.
     * 11. Eliminar tarea.
     * 12. Generar .json de tareas.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';
    private $languages;
    
    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:projects');
        $this->middleware('project_post', ['only' => ['store']]);
        $this->middleware('task_get', ['only' => ['edit', 'update', 'destroy']]);
        $this->middleware('task_post', ['only' => ['storeWorkingTime']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'status' => 'required',
                'assigned_to' => 'nullable|integer',
                'estimated_hours' => 'nullable|numeric'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'status' => 'required',
                'assigned_to' => 'nullable|integer',
                'estimated_hours' => 'nullable|numeric'
            ]);

        //Validación working time:
        }elseif(isset($data['submit_working-time'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'time_' => 'required',
                'date_' => 'required'
            ]);    
        }
    }

    /**
     * 1. Tareas por proyecto.
     */
    
    /**
     * 2. Guardar tarea.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-save-task');

            return redirect('/admin/projects/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Si no viene declarado estado de la tarea, creamos un estado inicial:
        if($request->status == '*'){
            $ps = new TaskStatus();
            $ps->name = 'inicial';
            $ps->slug = 'inicial';
            $ps->company_id = session('currentCompany');
            $ps->order = 1;
            $ps->save();

            $status = $ps->id;

        }else{
            $status = $request->status;
        }

        //Slug:
        $slug = Str::slug($request->name);

        //Formateando fechas:
        $delivery_date = $request->delivery_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->delivery_date):$request->delivery_date:NULL;

        //Convertir horas a minutos:
        $estimated_hours = $request->estimated_hours? $request->estimated_hours * 60:NULL;

        $task = new Task();
        $task->name = $request->name;
        $task->slug = $slug;
        $task->description = $request->description;
        $task->project_id = $request->id;
        $task->user_id = Auth::user()->id;
        $task->assigned_to = $request->assigned_to;
        $task->delivery_date = $delivery_date;
        $task->estimated_hours = $estimated_hours;
        $task->status = $status;
        $task->save();

        //Generar .json de tareas.
        $this->jsonTasks();

        $msg = trans('textos.tarea_creado_ok');
        return redirect()->route('projects.edit', [$task->project_id, 'tasks'])->with(compact('msg'));
    }

    /**
     * 3. Editar tarea.
     */
    public function edit(Task $task){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.tarea_editar',
            'module' => $this->module,
            'slug' => 'projects'
        ];  

        $project = Project::find($task->project_id);

        $team = [];
        if($project->team){
            $team = explode(',', $project->team);
        }
        array_push($team, $project->user_id);
        if($project->user_id != $project->owner){
            array_push($team, $project->owner);    
        }
        $team = array_filter($team);

        //Info de los miembros del equipo:
        $team_members = false;
        if(count($team) > 0){
            $team_members = User::select('id', 'name', 'surname')
            ->whereIn('id', $team)
            ->get();    
        }

        //Estados de proyecto:
        $task_status = TaskStatus::where('company_id', session('currentCompany'))
        ->orderBy('order', 'ASC')
        ->get();

        //Mensajes:
        $messages = TaskMessages::select('task_messages.*', 'users.name', 'users.surname')
        ->join('users', 'task_messages.from', '=', 'users.id')
        ->where('task_messages.task_id', $task->id)
        ->orderBy('task_messages.created_at', 'DESC')
        ->get();

        $languages = $this->languages;

        return view('admin.tasks.edit', compact('header', 'task', 'project', 'team', 'team_members', 'task_status', 'messages', 'languages'));   
    }

    /**
     * 4. Actualizar tarea.
     */
    public function update(Request $request, Task $task){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/tasks/'.$task->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        //Formateando fechas:
        $delivery_date = $request->delivery_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->delivery_date):$request->delivery_date:NULL;

        //Convertir horas a minutos:
        $estimated_hours = $request->estimated_hours? $request->estimated_hours * 60:NULL;

        $task->name = $request->name;
        $task->slug = $slug;
        $task->description = $request->description;
        $task->assigned_to = $request->assigned_to;
        $task->delivery_date = $delivery_date;
        $task->estimated_hours = $estimated_hours;
        $task->status = $request->status;
        $task->save();

        //Generar .json de tareas.
        $this->jsonTasks();

        $msg = trans('textos.tarea_updated');
        return redirect()->route('tasks.edit', $task->id)->with(compact('msg'));
    }

    /**
     * 5. Actualizar estado de la tarea.
     *
     * @param \App\task $task $status
     */
    public function status(Request $request, Task $task, $status){
        if($request->ajax()){    
            $task->status = $status;
            $task->save();
            
        }else{
            return redirect('/');
        }
    }

    /**
     * 6. Ordenar columna de tareas.
     */
    public function orderColumn(Request $request){
        if($request->ajax()){
            $i = 1;
            foreach($request->data as $row){
                if($row){
                    $t = Task::find($row);
                    $t->column_order = $i;
                    $t->save(); 

                    $i++;
                }   
            }
        }   
    }

    /**
     * 7. Guardar tiempo de trabajo en tarea.
     */
    public function storeWorkingTime(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-working-time');

            return redirect('/admin/tasks/'.$request->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $task = Task::find($request->id);

        //Convertimos las horas a minutos:
        $minutes = $request->time_ > 0? $request->time_ * 60:'0';

        //Info en array del tiempo trabajado:
        $arr['time_'] = $minutes;
        $arr['date_'] = $request->date_;
        $arr['user_'] = Auth::user()->id;

        $partial_hours = $task->partial_hours? unserialize($task->partial_hours):[];
        array_push($partial_hours, $arr);

        //Actualizamos info de la tarea:
        $task->partial_hours = serialize($partial_hours);
        $task->hours = $task->hours + $minutes;
        $task->save();

        //Actualizamos tiempo trabajado del proyecto y porcentaje completado:
        $project = Project::select('id', 'hours_forecast', 'hours_worked', 'completed')->find($task->project_id);
        $project->updateHourWorked();

        //Generar .json de tareas.
        $this->jsonTasks();
        
        $msg = trans('textos.tiempo_trabajo_creado');
        if($request->return == 'task'){
            return redirect()->route('tasks.edit', $task->id)->with(compact('msg'));    
        }else{
            return redirect()->route('projects.edit', [$task->project_id, 'tasks'])->with(compact('msg'));
        }
    }

    /**
     * 8. Obtener tiempos de trabajo de la tarea.
     */
    public function workingTime(Request $request, Task $task){
        if($request->ajax()){
            $working_time = $task->partial_hours? unserialize($task->partial_hours):false;
            return response()->json($working_time);
        }else{
            return false;
        }    
    }

    /**
     * 9. Eliminar tiempo de trabajo de la tarea.
     */
    public function deleteWorkingTime(Task $task, $index){
        $partials = $task->partial_hours? unserialize($task->partial_hours):false;

        if($partials){
            $partials_hours = [];
            $minutes = 0;
            foreach($partials as $idx => $p){
                if($idx != $index){
                    array_push($partials_hours, $p);
                    $minutes = $minutes + $p['time_'];
                }
            }

            $task->partial_hours = serialize($partials_hours);
            $task->hours = $minutes;
            $task->save();
        }

        //Generar .json de tareas.
        $this->jsonTasks();

        $msg = trans('textos.tiempo_trabajo_eliminado');
        return redirect()->route('tasks.edit', $task->id)->with(compact('msg'));
    }

    /**
     * 10. Tareas por miembro de equipo.
     */
    public function memberTasks(Request $request, User $user, $project){
        if($request->ajax()){    
            $tasks = Task::select('id', 'name', 'delivery_date', 'hours', 'status', 'created_at')
            ->where('assigned_to', $user->id)
            ->where('project_id', $project)
            ->get();  

            $data['user'] = $user;

            if($tasks){
                foreach($tasks as $row){
                    $row->fechaEntrega = $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';

                    $row->estado = TaskStatus::select('name')
                    ->where('id', $row->status)
                    ->first();
                }
            }

            $data['tasks'] = $tasks;

            return response()->json($data);            
        }else{
            return redirect('/');
        }    
    }

    /**
     * 11. Eliminar tarea.
     */
    public function destroy(Task $task){
        $project_id = $task->project_id;

        $task->delete();

        //Generar .json de tareas.
        $this->jsonTasks();

        $msg = trans('textos.tarea_deleted');
        return redirect()->route('projects.edit', [$project_id, 'tasks'])->with(compact('msg'));
    }

    /**
     * 12. Generar .json de tareas.
     */
    public function jsonTasks(){
        $tasks = Task::all(); 
        $jsondata = json_encode($tasks);
        file_put_contents(storage_path()."/json/tasks.json", $jsondata);    
    }
}
