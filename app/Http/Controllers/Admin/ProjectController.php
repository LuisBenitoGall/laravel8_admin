<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use File;
use PDF;

//Models:
use App\Models\BudgetOrderSharedFields;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Employee;
use App\Models\Group;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProjectDocs;
use App\Models\ProjectPurchases;
use App\Models\ProjectShared;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;

class ProjectController extends Controller{
    /**
     * 1. Proyectos por empresa.
     * 1.1. Listado de proyectos por filtro.
     * 1.2. Listado de proyectos por empresa para DataTable.
     * 2. Formulario nuevo proyecto.
     * 3. Guardar nuevo proyecto.
     * 3.1. Generar proyecto desde presupuesto-pedido.
     * 3.2. Vincular proyecto a presupuesto-pedido.
     * 3.3. Validación de proyecto - shared no existente.
     * 4. Editar proyecto.
     * 5. Actualizar proyecto.
     * 6. Actualizar estado de proyecto.
     * 7. Eliminar proyecto.
     * 8. Actualizar propietario.
     * 9. Actualizar miembros del equipo.
     * 10. Eliminar miembro de equipo.
     * 11. Generar .json de proyectos.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:projects');
        $this->middleware('project_get', ['only' => ['edit', 'update', 'destroy']]);
        $this->middleware('project_post', ['only' => ['updateOwner', 'linkProjectToBudgetOrder']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
            ]); 

        //Validación filter:
        }elseif(isset($data['submit_filter'])){
            return Validator::make($data, [
                'status' => 'required',
            ]);

        //Validar vincular proyecto:
        }elseif(isset($data['submit_add_project'])){
            return Validator::make($data, [
                'shared_id' => 'required|integer',
                'project_id' => 'required|integer'
            ]);
        }
    }

    /**
     * 1. Listado de proyectos por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.proyectos_mis',
            'module' => $this->module,
            'slug' => 'projects'
        ]; 

        //Filtros:
        // $arrStatus = session('status_')? session('status_'):false;

        // $query = Project::select('id', 'name', 'estimated_delivery_date', 'delivery_date', 'hours_forecast', 'hours_worked', 'completed', 'status', 'created_at')
        // ->where('company_id', session('currentCompany'));        
        // if($arrStatus){
        //     $query = $query->whereIn('status', $arrStatus);
        // }
        // $projects = $query->orderBy('created_at', 'DESC')->get();

        //dd($projects);

        //Estados de proyecto:
        $status = Helpers::projectStatus();

        $view = 'list';

        //Importación de proyectos desde .json. Se ejecuta 1 vez.
        Project::import();

        return view('admin.projects.index')->with(compact('header', 'status', 'view'));
    }

    /**
     * 1.1. Listado de proyectos por filtro.
     */
    public function getProjectsBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/projects')
                ->withErrors($validator)
                ->withInput();
        }

        $status_ = $request->status;
        return redirect()->route('projects.index')->with(compact('status_'));
    }

    /**
     * 1.2. Listado de proyectos por empresa para DataTable.
     */
    public function getProjectsDT($status = false){
        $arrStatus = $status? explode(',', $status):false;

        $query = Project::select('id', 'name', 'estimated_delivery_date', 'delivery_date', 'hours_forecast', 'hours_worked', 'completed', 'status', 'created_at')
        ->where('company_id', session('currentCompany'));
        //Filtro status:
        if($arrStatus){
            $query = $query->whereIn('status', $arrStatus);
        }
        $projects = $query->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('projects.edit');
        $eliminar = Auth::user()->can('projects.destroy');

        $arrEstados = Helpers::projectStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        //Ampliación de información de los objetos:
        if($projects){
            foreach($projects as $row){
                $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

                //Fecha entrega prevista:
                $row->prevista = $row->estimated_delivery_date? Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]):'';

                //Fecha entrega:
                $row->entrega = $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';

                //Grupos:
                //11/03/22: se elimina esta opción por una barra de progreso del proyecto.
                // $arrGroups = $row->groups? explode(',', $row->groups):false;
                // if($arrGroups){
                //     $row->grupos = '';
                //     foreach($arrGroups as $grp){
                //         $group = Group::select('name')->find($grp);
                //         if($row->grupos != ''){
                //             $row->grupos .= ', ';
                //         } 
                //         $row->grupos .= $group->name;
                //     }
                // }else{
                //     $row->grupos = '';
                // }

                //Estado:
                $row->estado = $row->status? ucfirst($arrEstados[$row->status]):'';
                $row->estados = $row->status.';'.$strEstados;

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar;
            }
        }

        return datatables($projects)->toJson();
    }

    /**
     * 2. Formulario nuevo proyecto.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.proyecto_nuevo',
            'module' => $this->module,
            'slug' => 'projects-create'
        ]; 

        return view('admin.projects.create')->with(compact('header'));
    }

    /**
     * 3. Guardar nuevo proyecto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/projects/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        $project = new Project();
        $project->name = $request->name;
        $project->slug = $slug;
        $project->company_id = session('currentCompany');
        $project->user_id = Auth::user()->id;
        $project->owner = Auth::user()->id;
        $project->save();

        //Generar .json de proyectos:
        $this->jsonProjects();

        $msg = trans('textos.proyecto_creado_ok');
        return redirect()->route('projects.edit', $project)->with(compact('msg'));
    }

    /**
     * 3.1. Generar proyecto desde presupuesto-pedido.
     */
    public function createFromBudgetOrder(BudgetOrderSharedFields $shared){
        //Validamos que el presupuesto - pedido no esté vinculado a ningún otro proyecto:
        $other_project = $this->validateProjectShared($shared->id);

        if($other_project){
            $alert = trans('textos.proyecto_vinculacion_existe_aviso');
            return redirect()->route('projects.index')->with(compact('alert'));
           exit;
        
        }else{
            $project = new Project();
            $project->save();

            $project->name = ucfirst(trans('textos.proyecto')).' '.$project->id;
            $project->slug = trans('textos.proyecto').'-'.$project->id;
            $project->company_id = session('currentCompany');
            $project->user_id = Auth::user()->id;
            $project->owner = Auth::user()->id;
            $project->customer_id = $shared->customer_id;
            $project->save();

            //Vinculamos proyecto a presupuesto-pedido:
            $project_shared = new ProjectShared();
            $project_shared->project_id = $project->id;
            $project_shared->shared_id = $shared->id;
            $project_shared->save();

            //Generar .json de proyectos:
            $this->jsonProjects();

            $msg = trans('textos.proyecto_creado_ok');
            return redirect()->route('projects.edit', $project)->with(compact('msg'));
        }
    }

    /**
     * 3.2. Vincular proyecto a presupuesto-pedido.
     */
    public function linkProjectToBudgetOrder(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-project');

            return redirect('/admin/projects/'.$request->project_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Validamos que el presupuesto - pedido no esté vinculado a ningún otro proyecto:
        $other_project = $this->validateProjectShared($request->shared_id);

        if($other_project){
            $alert = trans('textos.proyecto_vinculacion_existe_aviso');
            return redirect()->route('projects.index')->with(compact('alert'));
           exit;

        }else{
            //Vinculamos proyecto a presupuesto-pedido:
            $project_shared = new ProjectShared();
            $project_shared->project_id = $request->project_id;
            $project_shared->shared_id = $request->shared_id;
            $project_shared->save();

            $msg = trans('textos.proyecto_vinculado');
            return redirect()->route('projects.edit', $request->project_id)->with(compact('msg'));
        }
    }

    /**
     * 3.3. Validación de proyecto - shared no existente.
     *
     * Un proyecto puede vincularse a un número indeterminado de presupuestos - pedidos (shared). Cada shared sólo puede vincularse a un proyecto. Por tanto, la relación en la tabla project_shared es única.
     */
    public function validateProjectShared($shared_id){
        $data = ProjectShared::select('id')
        ->where('shared_id', $shared_id)
        ->first();

        return $data;
    }

    /**
     * 4. Editar proyecto.
     *
     * @param  \App\project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.proyecto_editar',
            'module' => $this->module,
            'slug' => 'projects'
        ]; 

        $author = User::select('name', 'surname')
        ->where('id', $project->user_id)
        ->first();

        $owner = User::select('name', 'surname')
        ->where('id', $project->owner)
        ->first();

        $customer = Company::select('id', 'name')->find($project->customer_id);

        $users = Employee::companyEmployees(session('currentCompany'), 1);
    
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

        //Tareas:
        $tasks = Task::select('tasks.*', 'users.name AS Nombre', 'users.surname', DB::raw('(SELECT SUM(estimated_hours) FROM tasks WHERE project_id = '.$project->id.') AS taskHours'))
        ->leftJoin('users', 'tasks.assigned_to', '=', 'users.id')
        ->where('tasks.project_id', $project->id)
        ->orderBy('tasks.column_order', 'ASC')
        ->orderBy('tasks.id', 'DESC')
        ->get();

        //Actualizamos tiempo trabajado del proyecto y porcentaje completado:
        $project->updateHourWorked();

        //Horas previstas por la suma de las tareas:
        if($tasks->count()){
            $project->task_hours = $tasks[0]->taskHours / 60;
            $project->save();
        }

        //Módulos asociados:
        // $module_budgets = $this->associatedModule('presupuestos');
        // $module_orders = $this->associatedModule('pedidos');

        //Estados de tareas:
        $task_status = TaskStatus::where('company_id', session('currentCompany'))
        ->orderBy('order', 'ASC')
        ->get();

        //Grupos:
        $groups = Group::where('company_id', session('currentCompany'))
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Presupuestos y pedidos de venta:
        // $budgets_orders = ProjectShared::select('budgets.id AS budgetId', 'budgets.budget_num', 'orders.id AS orderId', 'orders.order_num', 'project_shareds.created_at')
        // ->join('budget_order_shared_fields', 'project_shareds.shared_id', '=', 'budget_order_shared_fields.id')
        // ->leftJoin('budgets', 'budget_order_shared_fields.budget_id', '=', 'budgets.id')
        // ->leftJoin('orders', 'budget_order_shared_fields.order_id', '=', 'orders.id')
        // ->where('project_shareds.project_id', $project->id)
        // ->get();


        $budgets_orders = BudgetOrderSharedFields::select('budget_order_shared_fields.id', 'budget_order_shared_fields.customer_id', 'budgets.id AS budgetId', 'budgets.budget_num', 'budgets.budget_old_num', 'budgets.name', 'budgets.modality', 'budgets.status AS budget_status', 'orders.id AS orderId', 'orders.order_num', 'orders.order_old_num', 'orders.name AS order_name', 'orders.estimated_delivery_date', 'orders.delivery_date', 'orders.status AS order_status', 'project_shareds.created_at', 'users.name AS nombre', 'users.surname')
        ->join('project_shareds', 'budget_order_shared_fields.id', '=', 'project_shareds.shared_id')
        ->leftJoin('budgets', 'budget_order_shared_fields.budget_id', '=', 'budgets.id')
        ->leftJoin('orders', 'budget_order_shared_fields.order_id', '=', 'orders.id')
        ->leftJoin('users', 'budget_order_shared_fields.provider_manager_id', '=', 'users.id')
        ->where('project_shareds.project_id', $project->id)
        ->get();

        //Cliente: debe ser único. Lo obtenemos de los distintos presupuestos y deben ser el mismo. En caso de más de un cliente, redirigimos para que el usuario seleccione un único cliente:
        $arr_customers = [];

        //Abreviaturas modalidades de presupuesto:
        $abrev = [
            1 => trans('textos.basico'),
            10 => trans('textos.formulario'),
            20 => trans('textos.catalogo')
        ];

        //Obtenemos importe de cada presupuesto-pedido:
        foreach($budgets_orders as $row){
            //Enviamos las fechas ya formateadas:
            $row->fechaEntregaPrevista = Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]);
            $row->fechaEntrega = Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]);
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            //Importe:
            $arr_amounts = $row->getAmount();    
            $row->amount = $arr_amounts['amount_with_discount'];

            //Modalidad:
            $row->modalidad = $row->modality? ucfirst($abrev[$row->modality]):'';

            //Estado del presupuesto:
            $row->budget_status = $row->budget_status? ucfirst(helpers::budgetStatus($row->budget_status)):'';
            //Estado del pedido:
            $row->order_status = $row->order_status? ucfirst(helpers::orderStatus($row->order_status)):'';
        }

        //Eliminamos valores duplicados del array de clientes:
        $arr_customers = array_unique($arr_customers);
        if(count($arr_customers) > 1){
            $msg = trans('textos.proyecto_updated');
            return redirect()->route('projects.edit', $project)->with(compact('msg'));
        }

        //Compras del proyecto:
        $project_purchases = BudgetOrderSharedFields::select('budget_order_shared_fields.id', 'project_purchases.id AS ppId', 'orders.id AS orderId', 'orders.order_num', 'orders.purchase_num', 'purchase_old_num', 'orders.name', 'orders.order_num', 'orders.delivery_date', 'orders.status')
        ->join('orders', 'budget_order_shared_fields.order_id', '=', 'orders.id')
        ->join('project_purchases', 'orders.id', '=', 'project_purchases.order_id')
        ->where('project_purchases.project_id', $project->id)
        ->get();

        //Compras del proyecto. Array:
        $purchases_linked = [];
        if($project_purchases->count()){
            foreach($project_purchases as $pp){
                array_push($purchases_linked, $pp->orderId);
            }
        }

        //Compras:
        $purchases = Order::select('orders.id', 'orders.order_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.name')
        ->join('budget_order_shared_fields', 'orders.id', '=', 'budget_order_shared_fields.order_id')
        ->join('companies', 'budget_order_shared_fields.provider_id', '=', 'companies.id')
        ->where('budget_order_shared_fields.customer_id', session('currentCompany'))
        ->whereNotIn('orders.id', $purchases_linked)
        ->orderBy('orders.purchase_num', 'ASC')
        ->orderBy('orders.order_num', 'ASC')
        ->get();

        //Pedidos de proveedores, descontando los pedidos que existan ya entre las compras
        // $purchases_in = [];
        // foreach($purchases as $pr){
        //     if($pr->orderId){
        //         array_push($purchases_in, $pr->orderId);
        //     }
        // }

        // $provider_orders = Order::where('customer_id', session('currentCompany'))
        // ->whereNotIn('id', $purchases_in)
        // ->get();
         
        $languages = $this->languages;

        //Moneda:
        $currency = $this->co_currency;

        return view('admin.projects.edit', compact('header', 'tab', 'project', 'author', 'owner', 'customer', 'users', 'team', 'team_members', 'tasks', 'task_status', 'groups', 'budgets_orders', 'project_purchases', 'purchases', 'languages', 'currency'));
    }

    /**
     * 5. Actualizar proyecto.
     */
    public function update(Request $request, Project $project){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/projects/'.$project->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);

        //Formateando fechas:
        $estimated_delivery_date = $request->estimated_delivery_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->estimated_delivery_date):$request->estimated_delivery_date:NULL;
        $delivery_date = $request->delivery_date != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->delivery_date):$request->delivery_date:NULL;

        //Grupos del proyecto:
        $groups = $request->groups? implode(',', $request->groups):NULL;

        //Actualizando info:
        $project->name = $request->name;
        $project->slug = $slug;
        $project->description = $request->description;
        $project->briefing = $request->briefing;
        if($estimated_delivery_date){
            $project->estimated_delivery_date = $estimated_delivery_date;
        }
        if($delivery_date){
            $project->delivery_date = $delivery_date;
        }
        $project->hours_forecast = $request->hours_forecast;
        $project->groups = $groups;
        $project->status = $request->status? $request->status:1;
        $project->save();

        //Generar .json de proyectos:
        $this->jsonProjects();

        $msg = trans('textos.proyecto_updated');
        return redirect()->route('projects.edit', $project)->with(compact('msg'));
    }

    /**
     * 6. Actualizar estado de proyecto.
     */
    public function status(Request $request, Project $project, $status){
        if($request->ajax()){    
            $project->status = $status;
            $project->save();

            return response()->json(true);
        }
    }

    /**
     * 7. Eliminar proyecto.
     */
    public function destroy(Project $project){
        $project->delete();

        //Generar .json de proyectos:
        $this->jsonProjects();

        $msg = trans('textos.proyecto_deleted');
        return redirect()->route('projects.index')->with(compact('msg'));    
    } 

    /**
     * 8. Actualizar propietario.
     */
    public function updateOwner(Request $request){
        $project = Project::find($request->id);
        $project->owner = $request->owner;
        $project->save();

        //Generar .json de proyectos:
        $this->jsonProjects();

        $msg = trans('textos.proyecto_updated');
        return redirect()->route('projects.edit', $project)->with(compact('msg'));
    }

    /**
     * 9. Actualizar miembros del equipo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateTeam(Request $request){
        $project = Project::find($request->id);
        $team = $request->member? $request->member.',':'';
        $alert = false;

        //Añadiendo los miembros del pedido:
        if($request->order_members){
            $order = Order::select('customer_team', 'provider_team')
            ->where('id', $project->order_id)
            ->first();

            if($order){
                $cteam = $order->customer_team? unserialize($order->customer_team):[];
                $pteam = $order->provider_team? unserialize($order->provider_team):[];

                //Fusionamos arrays y eliminamos duplicados:
                $order_team = array_merge($cteam, $pteam);
                $order_team = array_unique($order_team);

                if(count($order_team) > 0){
                    foreach($order_team as $ot){
                        $team .= $ot.',';
                    }
                }else{
                    $alert = trans('textos.pedido_sin_miembros');
                }
            }
        }    

        //Eliminamos valores duplicados:
        $clean_team = $project->team.$team;
        $clean_team = explode(',', $clean_team);
        $clean_team = array_unique($clean_team);
        $clean_team = implode(',', $clean_team);  
        
        $project->team = $clean_team;
        $project->save();

        //Generar .json de proyectos:
        $this->jsonProjects();

        if($alert){
            return redirect()->route('projects.edit', [$project->id, 'team'])->with(compact('alert'));
        }else{
            $msg = trans('textos.proyecto_updated');
            return redirect()->route('projects.edit', [$project->id, 'team'])->with(compact('msg'));    
        }
    }

    /**
     * 10. Eliminar miembro de equipo.
     *
     * @param  str $project
     * @return \Illuminate\Http\Response
     */
    public function deleteMember(Project $project, $user_id){
        if(!$project || !$user_id){
            return redirect('/');
            exit;
        }

        $team = explode(',', $project->team);
        $arrTeam = [];
        foreach($team as $row){
            if($row && $row != $user_id){
                $arrTeam[] = $row;
            }
        }
        $newTeam = implode(',', $arrTeam).',';  //Importante añadir la coma (,) al final por si se se añaden nuevos miembros.

        $project->team = $newTeam;
        $project->save();

        //Generar .json de proyectos:
        $this->jsonProjects();

        $msg = trans('textos.proyecto_updated');
        return redirect()->route('projects.edit', [$project->id, 'team'])->with(compact('msg'));
    }

    /**
     * 11. Generar .json de proyectos.
     *
     * @return .json
     */
    public function jsonProjects(){
        $projects = Project::all();
        $jsondata = json_encode($projects);
        file_put_contents(storage_path()."/json/projects.json", $jsondata); 

        $docs = ProjectDocs::all();
        $jsondata = json_encode($docs);
        file_put_contents(storage_path()."/json/project-docs.json", $jsondata); 

        $tasks = Task::all(); 
        $jsondata = json_encode($tasks);
        file_put_contents(storage_path()."/json/tasks.json", $jsondata); 

        $status = TaskStatus::all();
        $jsondata = json_encode($status);
        file_put_contents(storage_path()."/json/task-status.json", $jsondata);
    }
}
