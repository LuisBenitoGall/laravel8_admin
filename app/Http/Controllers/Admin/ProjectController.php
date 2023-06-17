<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;
use PDF;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Project;
use App\Models\ProjectDocs;
use App\Models\ProjectFavourite;
use App\Models\ProjectGroup;
use App\Models\ProjectGroupItems;
use App\Models\ProjectOrders;
use App\Models\ProjectPatterns;
use App\Models\ProjectPurchases;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;

class ProjectController extends Controller{
    /**
     * 1. Proyectos por empresa.
     * 1.1. Listado de proyectos por filtro.
     * 1.2. Listado de proyectos por empresa para DataTable.
     * 1.3. Proyectos por empresa para selector.
     * 2. Formulario nuevo proyecto.
     * 3. Guardar nuevo proyecto.
     * 3.1. Generar proyecto desde presupuesto-pedido.
     * 3.2. Vincular proyecto a presupuesto-pedido.
     * 3.3. Validación de proyecto - shared no existente.
     * 4. Editar proyecto.
     * 4.1. Gastos por proyecto para DT.
     * 5. Actualizar proyecto.
     * 6. Actualizar estado de proyecto.
     * 7. Eliminar proyecto.
     * 8. Actualizar propietario.
     * 9. Actualizar miembros del equipo.
     * 10. Eliminar miembro de equipo.
     * 11. Generar .json de proyectos.
     * 12. Resetear numeración del proyecto.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:projects');
        $this->middleware('project_get', ['only' => ['edit', 'update', 'destroy', 'resetNum']]);
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
                'order_id' => 'required|integer',
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

        //Estados de proyecto:
        $status = helpers::projectStatus();

        $view = 'list';

        //Importación de proyectos desde .json. Se ejecuta 1 vez.
        Project::import();

        //Proyectos favoritos del usuario:
        $favourites = ProjectFavourite::select('projects.id', 'projects.project_num', 'projects.name', 'projects.hours_forecast', 'projects.hours_worked')
        ->join('projects', 'project_favourites.project_id', '=', 'projects.id')
        ->where('project_favourites.user_id', Auth::user()->id)
        ->orderBy('projects.name', 'ASC')
        ->get();

        return view('admin.projects.index')->with(compact('header', 'status', 'view', 'favourites'));
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

        $estados = helpers::projectStatus();

        //Proyectos favoritos del usuario:
        $favourites = ProjectFavourite::where('user_id', Auth::user()->id)
        ->pluck('project_id')
        ->toArray();

        $data = Project::select('projects.id', 'projects.name', 'projects.estimated_delivery_date', 'projects.delivery_date', 'projects.hours_forecast', 'projects.hours_worked', 'projects.completed', 'projects.status', 'projects.created_at', 'companies.name AS customer'
        )
        ->leftJoin('companies', 'projects.customer_id', '=', 'companies.id')
        ->where('projects.company_id', session('currentCompany'))
        //Filtro status:
        ->when($arrStatus, function($q) use($arrStatus){
            return $q->whereIn('projects.status', $arrStatus);
        })
        ->orderBy('projects.created_at', 'DESC');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return '<a href="/admin/projects/'.$row->id.'/edit">'.$row->name.'</a>'; 
        })
        ->addColumn('customer', function($row){
            return $row->customer;
        })
        ->addColumn('alta', function($row){
            return $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('entrega_prevista', function($row){
            return $row->prevista = $row->estimated_delivery_date? Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('entrega', function($row){
            return $row->entrega = $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('progress', function($row){
            $progress = '';
            if(!$row->completed){
                $completed = $row->completed > 100? 100:$row->completed;
                if($completed > 0){
                    $progress .= '<div class="tooltip-fixed"><span class="tooltiptext" style="left: calc('.$completed.'% - 26px)">'.$completed.'%</span></div>';
                }
                $progress .= '<div class="progress progress-mini mx-1"><div style="width: '.$completed.'%;" class="progress-bar"></div></div>';
            }

            return $progress;
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.projects.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function($row) use($estados, $favourites){
            $manage_status = true;
            return view('admin.projects.partials.dt-actions', compact('row', 'estados', 'favourites', 'manage_status'));    
        })
        ->rawColumns(['name', 'progress'])
        ->make(true);
    }

    /**
     * 1.3. Proyectos por empresa para selector.
     */
    public function companyProjects(Request $request, $company_id){
        if($request->ajax()){ 
            return Project::select('id', 'name', DB::raw("CONCAT(name, ' - ', project_num) AS project"))
            ->where('company_id', $company_id)
            ->orderBy('name', 'ASC')
            ->get();
        }
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

        $customers = false;
        //La empresa ha de tener habilitada la gestión de clientes:
        if(session('companySettings') && session('companySettings')['customers_management']){
            $customers = CustomerProviders::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'customer_providers.customer_id', '=', 'companies.id')
            ->where('customer_providers.provider_id', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->get();
        }

        return view('admin.projects.create')->with(compact('header', 'customers'));
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
        $project->customer_id = $request->customer_id;
        $project->save();

        //Generar .json de proyectos:
        $this->jsonProjects();

        $msg = trans('textos.proyecto_creado_ok');
        return redirect()->route('projects.edit', $project)->with(compact('msg'));
    }

    /**
     * 3.1. Generar proyecto desde presupuesto-pedido.
     */
    public function createFromBudgetOrder(Order $order){
        //Validamos que el presupuesto - pedido no esté vinculado a ningún otro proyecto:
        $other_project = $this->validateProjectOrder($order->id);

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
            $project->customer_id = $order->customer_id;
            $project->save();

            //Vinculamos proyecto a pedido:
            $project_order = new ProjectOrders();
            $project_order->project_id = $project->id;
            $project_order->order_id = $order->id;
            $project_order->save();

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
        $other_project = $this->validateProjectOrder($request->order_id);

        if($other_project){
            $alert = trans('textos.proyecto_vinculacion_existe_aviso');
            return redirect()->route('projects.index')->with(compact('alert'));
           exit;

        }else{
            //Vinculamos proyecto a presupuesto-pedido:
            $project_shared = new ProjectOrders();
            $project_shared->project_id = $request->project_id;
            $project_shared->order_id = $request->order_id;
            $project_shared->save();

            $msg = trans('textos.proyecto_vinculado');
            return redirect()->route('projects.edit', $request->project_id)->with(compact('msg'));
        }
    }

    /**
     * 3.3. Validación de proyecto - shared no existente.
     *
     * Un proyecto puede vincularse a un número indeterminado de pedidos. Cada pedido sólo puede vincularse a un proyecto. Por tanto, la relación en la tabla project_orders es única.
     */
    public function validateProjectOrder($order_id){
        $data = ProjectOrders::select('id')
        ->where('order_id', $order_id)
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

        $team = [];
        if($project->team){
            $team = explode(',', $project->team);
        }
        array_push($team, $project->user_id);
        if($project->user_id != $project->owner){
            array_push($team, $project->owner);    
        }
        $team = array_filter($team);

        //Si hay acceso restringido, el usuario debe pertenecer al equipo:
        if($project->restricted_access && !in_array(Auth::user()->id, $team)){
            $alert = trans('textos.proyecto_acceso_restringido');
            return redirect()->route('projects.index')->with(compact('alert'));
            exit;
        }

        //Patrones de numeración del proyecto:
        $patterns = false;
        if(!$project->pattern_id){
            $patterns = ProjectPatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        $author = User::select('name', 'surname')
        ->where('id', $project->user_id)
        ->first();

        $owner = User::select('name', 'surname')
        ->where('id', $project->owner)
        ->first();

        $customer = Company::select('id', 'name')->find($project->customer_id);

        $customers = false;
        //La empresa ha de tener habilitada la gestión de clientes y no debe estar declardo el cliente:
        if(session('companySettings') && session('companySettings')['customers_management'] && !$customer){
            $customers = CustomerProviders::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'customer_providers.customer_id', '=', 'companies.id')
            ->where('customer_providers.provider_id', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->get();
        }

        $users = Employee::companyEmployees(session('currentCompany'), 1);

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
        $project_orders = Order::select('orders.id', 'orders.is_order', 'orders.is_budget', 'orders.customer_id', 'orders.budget_num', 'orders.budget_old_num', 'orders.name', 'orders.modality', 'orders.budget_status AS budget_status', 'orders.order_num', 'orders.order_old_num', 'orders.estimated_delivery_date', 'orders.delivery_date', 'orders.order_status', 'orders.created_at', 'users.name AS nombre', 'users.surname')
        ->join('project_orders', 'orders.id', '=', 'project_orders.order_id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->where('project_orders.project_id', $project->id)
        ->get();

        //Pasamos las ventas vinculadas a un array:
        $arr_project_orders = [];
        foreach($project_orders as $po){
            array_push($arr_project_orders, $po->id);    
        }

        //Ventas para el proyecto:
        $orders = Order::select('orders.id', 'orders.order_num', 'orders.name', 'companies.name AS customer')
        ->join('companies', 'orders.provider_id', '=', 'companies.id')
        ->where('orders.is_order', true)
        ->where('orders.provider_id', session('currentCompany'))
        ->whereNotIn('orders.id', $arr_project_orders)
        ->orderBy('orders.order_num', 'ASC')
        ->get();

        //Abreviaturas modalidades de presupuesto:
        $abrev = [
            1 => trans('textos.basico'),
            10 => trans('textos.formulario'),
            20 => trans('textos.catalogo')
        ];

        //Obtenemos importe de cada presupuesto-pedido:
        foreach($project_orders as $row){
            //Enviamos las fechas ya formateadas:
            $row->fechaEntregaPrevista = $row->estimated_delivery_date? Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]):'';
            $row->fechaEntrega = $row->delivery_date? Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]):'';
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            //Importe:
            $arr_amounts = $row->getAmount();    
            $row->amount = $arr_amounts['total_amount'];

            //Modalidad:
            $row->modalidad = $row->modality? ucfirst($abrev[$row->modality]):'';

            //Estado del presupuesto:
            $row->budget_status = $row->budget_status? ucfirst(helpers::budgetStatus($row->budget_status)):'';
            //Estado del pedido:
            $row->order_status = $row->order_status? ucfirst(helpers::orderStatus($row->order_status)):'';
        }

        //Compras:
        // $project_purchases = Order::select('orders.id', 'orders.order_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.name', 'orders.delivery_date', 'orders.order_status', 'companies.name AS provider', 'project_purchases.id AS ppId', 'invoices.fiscal_date')
        // ->join('project_purchases', 'orders.id', '=', 'project_purchases.order_id')
        // ->join('companies', 'orders.provider_id', '=', 'companies.id')
        // ->leftJoin('invoices', 'orders.id', '=', 'invoices.order_id')
        // ->where('project_purchases.project_id', $project->id)
        // ->orderBy('orders.order_num', 'ASC')
        // ->groupBy('orders.id')
        // ->get();

        $project_purchases = Invoice::select('orders.id', 'orders.order_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.name', 'orders.delivery_date', 'orders.order_status', 'companies.name AS provider', 'project_purchases.id AS ppId', 'invoices.fiscal_date', 'invoices.id AS invoiceId', 'invoices.invoice_num', 'invoices.invoice_old_num', 'invoices.amount', 'invoices.amount_with_iva', 'invoices.irpf', 'c.id AS customer_id', 'c.name AS customer')
        ->leftJoin('orders', 'invoices.order_id', '=', 'orders.id')
        ->leftJoin('project_purchases', 'orders.id', '=', 'project_purchases.order_id')
        ->leftJoin('companies', 'orders.provider_id', '=', 'companies.id')
        ->leftJoin('companies AS c', 'orders.customer_id', '=', 'c.id')
        ->where('project_purchases.project_id', $project->id)
        ->orderBy('orders.order_num', 'ASC')
        ->groupBy('invoiceId')
        ->get();

        //Pasamos las compras vinculadas a un array:
        $arr_project_purchases = [];
        foreach($project_purchases as $pp){
            array_push($arr_project_purchases, $pp->id);
        }

        //Compras para el proyecto:
        $purchases = Order::select('orders.id', 'orders.order_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.name', 'companies.name AS provider')
        ->join('companies', 'orders.provider_id', '=', 'companies.id')
        ->where('orders.is_order', true)
        ->where('orders.customer_id', session('currentCompany'))
        ->whereNotIn('orders.id', $arr_project_purchases)
        ->orderBy('orders.purchase_num', 'ASC')
        ->get();

        //Gastos del proyecto:
        $project_expenses = Expense::select('expenses.*', 'companies.name')
        ->leftJoin('companies', 'expenses.provider_id', '=', 'companies.id')
        ->where('expenses.project_id', $project->id)
        ->get();

        //Tipos de gasto:
        $expense_types = helpers::expenseTypes();

        //Gastos sin proyecto:
        $expenses = Expense::select('expenses.*', 'companies.name')
        ->leftJoin('companies', 'expenses.provider_id', '=', 'companies.id')
        ->where('company_id', session('currentCompany'))
        ->whereNull('expenses.project_id')
        ->get();

        //Agrupaciones a las que pertenece el proyecto presente:
        $groups_in = ProjectGroupItems::where('project_id', $project->id)
        ->pluck('group_id')
        ->toArray();

        //Agrupaciones de proyectos:
        $arr_companies = [];
        foreach(session('companies') as $c){
            array_push($arr_companies, $c->id);
        }
        $project_groups = ProjectGroup::whereIn('company_id', $arr_companies)
        ->orderBy('title', 'ASC')
        ->pluck('title', 'id')
        ->toArray();

        $languages = $this->languages;

        //Moneda:
        $currency = $this->co_currency;

        return view('admin.projects.edit', compact('header', 'tab', 'project', 'patterns', 'author', 'owner', 'customer', 'customers', 'users', 'team', 'team_members', 'tasks', 'task_status', 'groups', 'project_orders', 'orders', 'project_purchases', 'purchases', 'project_expenses', 'expense_types', 'expenses', 'project_groups', 'groups_in', 'languages', 'currency'));
    }

    /**
     * 4.1. Gastos por proyecto para DT.
     */
    public function getProjectExpensesDT($project_id){
        $data = Expense::select('expenses.*', 'companies.name AS company')
        ->leftJoin('companies', 'expenses.provider_id', '=', 'companies.id')
        ->where('expenses.project_id', $project_id);

        //Moneda:
        $currency = $this->co_currency;  

        //Tipos de pago:
        $types = helpers::expenseTypes(); 
        
        return DataTables::of($data)
        ->addColumn('concept', function($row){
            return $row->concept;
        })
        ->addColumn('expensed_in', function($row){
            return $row->expensed_in? Carbon::parse($row->expensed_in)->format($this->languages[session()->get('locale')][4]):'';
        })
        ->addColumn('amount', function($row) use($currency){
            if($row->amount_with_iva){
                return $row->amount_with_iva.$currency->symbol;
            }else{
                return '';
            }
        }) 
        ->addColumn('type', function($row) use($types){
            if($row->type){
                return $types[$row->type];
            }else{
                return '';
            }
        })
        ->addColumn('provider', function($row){
            return $row->company;
        }) 
        ->addColumn('doc', function($row){
            if($row->doc){
                return '<a target="_blank" href="/docs/expenses/'.$row->doc.'"><i class="fa fa-file"></i></a>';
            }else{
                return '';
            }
        })  
        ->addColumn('acciones', function($row){
            $return = 'projects';
            return view('admin.expenses.partials.dt-actions', compact('row', 'return')); 
        }) 
        ->rawColumns(['doc'])
        ->make(true);
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

        //Número de proyecto:
        $project_num = NULL;
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('project', $request->pattern_id);
        }

        //Formateando fechas:
        $estimated_delivery_date = $request->estimated_delivery_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->estimated_delivery_date):$request->estimated_delivery_date:NULL;
        $delivery_date = $request->delivery_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->delivery_date):$request->delivery_date:NULL;

        //Grupos del proyecto:
        $groups = $request->groups? implode(',', $request->groups):NULL;

        //Actualizando info:
        if($request->pattern_id){
            if(!empty($patternNum)){
                $project->project_num = $patternNum['pattern_num'];  
                $project->num_cardinal = $patternNum['num']; 
            }            
            $project->pattern_id = $request->pattern_id;
        }
        $project->project_old_num = $request->project_old_num;
        $project->name = $request->name;
        $project->slug = $slug;
        $project->chart = $request->chart;
        $project->short_description = $request->short_description;
        $project->description = $request->description;
        $project->briefing = $request->briefing;
        $project->restricted_access = $request->restricted_access? true:false;
        if($request->customer_id){
            $project->customer_id = $request->customer_id;
        }
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

    /**
     * 12. Resetear numeración del proyecto.
     */
    public function resetNum(Project $project){
        $project->project_num = NULL;
        $project->num_cardinal = NULL;
        $project->pattern_id = NULL;
        $project->save();

        $msg = trans('textos.numero_deleted');
        return redirect()->route('projects.edit', $project)->with(compact('msg'));
    }
}
