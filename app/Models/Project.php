<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Support\Facades\DB;

class Project extends Model{
    /**
     * 1. Tareas del proyecto.
     * 2. Documentos del proyecto.
     * 3. Proyectos por empresa.
     * 4. Actualizar horas de trabajo.
     * 5. Importación proyectos desde .json.
     * 6. Generar proyecto vinculado a pedido.
     * 7. Categorías de un proyecto.
     */
    
    use HasFactory;
    use SoftDeletes; //Implementamos borrado lógico

    protected $dates = ['deleted_at']; //Columna de borrado lógico  

    protected $table = 'projects';

    /**
     * 1. Tareas del proyecto.
     */
    public function tasks(){
        return $this->hasMany(Task::class);
    }

    /**
     * 2. Documentos del proyecto.
     */
    public function docs(){
        return $this->hasMany(ProjectDocs::class);
    }

    /**
     * 3. Proyectos por empresa.
     *
     * $company_id  int
     * $limit       int
     * $filter      array
     */
    public static function companyProjects($company_id, $limit = false, $filter = false){
        $q = Project::select('projects.*',
            DB::raw('(SELECT doc FROM project_docs WHERE project_id = projects.id AND thumbnail = 1) AS Image')
        )
        ->where('projects.company_id', $company_id);
        if($filter){
             $q->whereIn('status', $filter);
        }
        $q->orderBy('projects.created_at', 'DESC');
        if($limit){
            $q->limit($limit);
        }
        $data = $q->get();

        return $data;    
    }

    /**
     * 4. Actualizar horas de trabajo.
     */
    public function updateHourWorked(){
        $hours_by_task = Task::where('project_id', $this->id)->sum('hours');

        //Convertimos a horas:
        $hours_by_task = round($hours_by_task / 60, 2);

        //Porcentaje completado:
        $completed = $this->hours_forecast > 0 && $hours_by_task > 0? round( ($hours_by_task * 100) / $this->hours_forecast, 2):0;

        $this->hours_worked = $hours_by_task;
        $this->completed = $completed;
        $this->save();
    }

    /**
     * 5. Importación proyectos desde .json.
     */
    public function import(){
        $exist = Project::select('id')->first();  
        
        if(!$exist){
            $path = storage_path()."/json/projects.json";
            $projects = json_decode(file_get_contents($path), true);

            $path_docs = storage_path()."/json/project-docs.json";
            $docs = json_decode(file_get_contents($path_docs), true);

            $path_task_status = storage_path()."/json/task-status.json";
            $task_status = json_decode(file_get_contents($path_task_status), true);

            $path_tasks = storage_path()."/json/tasks.json";
            $tasks = json_decode(file_get_contents($path_tasks), true);

            //Truncate de las tablas:
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('project_docs')->truncate();
            DB::table('task_status')->truncate();
            DB::table('tasks')->truncate();
            DB::table('projects')->truncate();

            foreach($projects as $p){
                $project = new Project();
                $project->id = $p['id'];
                $project->code = $p['code'];
                $project->contest_code = $p['contest_code'];
                $project->name = $p['name'];
                $project->slug = $p['slug'];
                $project->description = $p['description'];
                $project->briefing = $p['briefing'];
                $project->company_id = $p['company_id'];
                $project->user_id = $p['user_id'];
                $project->owner = $p['owner'];
                $project->team = $p['team'];
                $project->estimated_delivery_date = $p['estimated_delivery_date'];
                $project->delivery_date = $p['delivery_date'];
                $project->hours_forecast = $p['hours_forecast'];
                $project->hours_worked = $p['hours_worked'];
                $project->task_hours = $p['task_hours'];
                $project->completed = $p['completed'];
                $project->groups = $p['groups'];
                $project->status = $p['status'];
                $project->save();
            }

            if($docs){
                foreach($docs as $d){
                    $doc = new ProjectDocs();
                    $doc->id = $d['id'];
                    $doc->doc = $d['doc'];
                    $doc->project_id = $d['project_id'];
                    $doc->type = $d['type'];
                    $doc->featured = $d['featured'];
                    $doc->thumbnail = $d['thumbnail'];
                    $doc->save();
                }
            }

            if($tasks){
                foreach($tasks as $t){
                    $task = new Task();
                    $task->id = $t['id'];
                    $task->name = $t['name'];
                    $task->slug = $t['slug'];
                    $task->description = $t['description'];
                    $task->project_id = $t['project_id'];
                    $task->user_id = $t['user_id'];
                    $task->assigned_to = $t['assigned_to'];
                    $task->delivery_date = $t['delivery_date'];
                    $task->estimated_hours = $t['estimated_hours'];
                    $task->partial_hours = $t['partial_hours'];
                    $task->hours = $t['hours'];
                    $task->status = $t['status'];
                    $task->column_order = $t['column_order'];
                    $task->save();
                }
            }

            if($task_status){
                foreach($task_status as $ts){
                    $status = new TaskStatus();
                    $status->id = $ts['id'];
                    $status->name = $ts['name'];
                    $status->slug = $ts['slug'];
                    $status->company_id = $ts['company_id'];
                    $status->order = $ts['order'];
                    $status->save();
                }
            }

            //Realizamos toda la importación sin considerar las Foreign Key para evitar errores.
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }  
    }

    /**
     * 6. Generar proyecto vinculado a pedido.
     */
    public static function setProject($order){
        $p = new Project();
        $p->name = trans('textos.pedido').'-'.$order->id;
        $p->slug = trans('textos.pedido').'-'.$order->id;
        $p->company_id = $order->provider_id;
        $p->user_id = $order->provider_manager_id;
        $p->owner = $order->provider_manager_id;
        $p->customer_id = $order->customer_id;
        $p->status = 1;
        $p->save();

        //Vinculando pedido a proyecto:
        $po = new ProjectOrders();
        $po->project_id = $p->id;
        $po->order_id = $order->id;
        $po->save();

        return $p;
    }

    /**
     * 7. Categorías de un proyecto.
     */
    public function categories(){
        return $this->belongsToMany(Category::class, 'project_categories');
    }

}
