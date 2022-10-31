<?php

/**
 * 18/06/2022: Modificación de la lógica para presupuestos y pedidos.  Desaparece la tabla budget_order_shared_fields, y es la tabla orders la que asimila el papel de entidad, y presupuestos pasan a ser un tipo de pedido aunque con algunos valores propios.  ID de budget y order son desde ahora el mismo.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;
use PDF;

//Models:
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\BudgetPatterns;
use App\Models\BudgetSettings;
use App\Models\BusinessAreas;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CostCenters;
use App\Models\CustomerProviders;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderSettings;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectOrders;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\Workplace;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BudgetController extends Controller{
    /**
     * 1. Presupuestos por empresa.
     * 1.1. Listado de presupuestos para DataTable.
     * 2. Formulario nuevo presupuesto.
     * 3. Guardar nuevo presupuesto.
     * 3.1. Items por defecto en nuevo presupuesto.
     * 4. Editar presupuesto.
     * 5. Actualizar presupuesto.
     * 6. Eliminar presupuesto.
     * 7. Resetear numeración del presupuesto.
     * 8. Añadir responsable de cliente.
     * 9. Eliminar documento de orden de compra.
     * 10. Crear presupuesto desde pedido.
     * 11. Actualizar notas del presupuesto.
     * 12. Actualizar plantilla de presupuesto.
     * 13. Eliminar plantilla del presupuesto.
     * 14. Imprimir presupuesto PDF.
     * 15. Crear versión de presupuesto.
     * 16. Actualizar estado de presupuesto.
     * 17. Cambio de proveedor.
     * 18. Definir modalidad de presupuesto.
     * 19. Copiar presupuesto.
     * 20. Setear bloque para líneas de formulario.
     * 21. Presupuestos proveedores.
     * 22. Permitir acceso a cliente.
     * 23. Ver presupuesto.
     * 23.1. Línea de presupuesto para DataTable.
     * 24. Aceptar presupuesto por parte del cliente.
     * 25. Cancelar presupuesto por parte del cliente.
     * 26. Importar presupuesto de proveedor.
     */
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:budgets');
        $this->middleware('order_get', ['only' => ['edit', 'update', 'destroy', 'resetNum', 'removeOcDoc', 'createOrder', 'removeTemplate', 'print', 'modify', 'updateTemplate']]);
        $this->middleware('order_post', ['only' => ['addManager', 'updateComments', 'setProvider', 'setModality', 'copy', 'setBlock']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'form_id' => 'required_if:modality,==,'.config('constants.BUDGET_FORM_MODALITY_'),
                'modality' => 'required',
                'customer_id' => 'required|integer'
            ],[
                'modality.required' => trans('textos.presupuesto_modalidad_aviso'),
                'form_id.required_if' => trans('textos.formulario_presupuesto_obligatorio')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                //'customer_id' => 'required|integer'
            ]);

        //Validación añadir nuevo responsable:
        }elseif(isset($data['submit_add_manager'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'name' => 'required',
                'surname' => 'required',
                'email' => 'required|string|email|max:191|unique:users,email,NULL,id,deleted_at,NULL'
            ],[
                'email.unique' => trans('textos.email_existe')
            ]);

        //Validación template:
        }elseif(isset($data['submit_update_template'])){
            return Validator::make($data, [
                'template_id' => 'nullable|integer'
            ]); 
        
        //Validación cambio proveedor:
        }elseif(isset($data['submit-set-provider'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'company_id' => 'required|integer'
            ]);   

        //Validación actualizar modalidad: 
        }elseif(isset($data['submit_set_modality'])){
            return Validator::make($data, [   
                'order_id' => 'required|integer',
                'modality' => 'required'
            ]);

        //Validación copia presupuesto:
        }elseif(isset($data['submit_copy'])){
            return Validator::make($data, [   
                'order_id' => 'required|integer',
                'customer' => 'required|integer'
            ]);

        //Validación set bloque de líneas de formulario: 
        }elseif(isset($data['submit_set_block'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'block_id' => 'required|integer'
            ]); 

        //Validación importar presupuesto:
        }elseif(isset($data['submit_import'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'modality' => 'required_without:budget_id',
                'form_id' => 'required_if:modality,==,'.config('constants.BUDGET_FORM_MODALITY_')
            ],[
                'modality.required_without' => trans('textos.presupuesto_modalidad_aviso'),
                'form_id.required_if' => trans('textos.formulario_presupuesto_obligatorio')
            ]);   
        }
    }

    /**
     * 1. Presupuestos por empresa. 
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.presupuestos_mis',
            'module' => $this->module,
            'slug' => 'budgets'
        ]; 

        $budget_from = 'customers'; 

        return view('admin.budgets.index')->with(compact('header', 'budget_from'));     
    }

    /**
     * 1.1. Listado de presupuestos para DataTable.
     */
    public function getBudgetsDT($from = false){
        //Presupuestos para cliente:
        if($from == 'customers'){
            $query = Order::select('orders.id', 'orders.is_order', 'orders.discount', 'orders.customer_id', 'orders.provider_id', 'orders.budget_num', 'orders.budget_old_num', 'orders.name', 'orders.budget_status', 'orders.modality', 'orders.created_at', 'companies.name AS company', 'orders.id AS orderId', 'orders.order_num','orders.invoiced', 'users.name As Nombre', 'users.surname', 'u_.name AS CustomerManagerName', 'u_.surname AS CustomerManagerSurname')
            ->join('companies', 'orders.customer_id', '=', 'companies.id')
            ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
            ->leftJoin('users AS u_', 'orders.customer_manager_id', '=', 'u_.id')
            ->where('orders.is_budget', true)
            ->where('orders.provider_id', session('currentCompany'));

        //Presupuestos de proveedor:
        }elseif($from == 'providers'){
            $query = Order::select('orders.*', 'companies.name AS company', 'users.name As Nombre', 'users.surname')
            ->join('companies', 'orders.provider_id', '=', 'companies.id')
            ->leftJoin('users', 'orders.customer_manager_id', '=', 'users.id')
            ->where('orders.is_budget', true)
            ->where('orders.budget_shared', 1)
            ->where('orders.customer_id', session('currentCompany'));
        }
        $budgets = $query; 

        $estados = helpers::budgetStatus();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Abreviaturas modalidades de presupuesto:
        $abrev = [
            1 => trans('textos.basico'),
            10 => trans('textos.formulario'),
            20 => trans('textos.catalogo')
        ];

        return DataTables::of($budgets)
        ->addColumn('num', function($budget) use($from){
            return view('admin.budgets.partials.dt-num', compact('budget', 'from')); 
        })
        ->addColumn('order_num', function($budget){
            if($budget->is_order){
                $order_num = $budget->order_num? $budget->order_num:($budget->order_old_num? $budget->order_old_num:$budget->id);
                return '<a target="_blank" href="'.route('orders.edit', $budget->id).'">'.$order_num.'</a>';
            }else{
                return '';
            }
        })
        ->addColumn('amount', function($budget) use($currency){
            $amount = $budget->getAmount();    
            return $amount['amount_with_discount'].$currency->symbol;
        })
        ->addColumn('modalidad', function($budget) use($abrev){
            return ucfirst($abrev[$budget->modality]);
        })
        ->addColumn('manager', function($budget){
            return $budget->Nombre.' '.$budget->surname;
        })
        ->filterColumn('manager', function($query, $keyword){
            $sql = "CONCAT(users.name, ' ', users.surname) like ?";
            $query->whereRaw($sql, ["%$keyword%"]);
        })
        ->addColumn('alta', function($budget){
            return $budget->created_at->format($this->languages[session()->get('locale')][4]);    
        })
        ->addColumn('estado', function($budget) use($estados){
            return view('admin.budgets.partials.dt-status', compact('budget', 'estados'));    
        })
        ->filterColumn('estado', function($query, $keyword) use($estados){
            //01/09/22: no filtra. Corregir código.
            $idx = $keyword > 0? array_search('$keyword', $estados):false;
            if($idx){
                $query->where("orders.budget_status", $idx);
            }
        })
        ->addColumn('acciones', function($budget) use($estados){
            return view('admin.budgets.partials.dt-actions', compact('budget', 'estados'));    
        })
        ->rawColumns(['order_num'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo presupuesto.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($customer_id = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.presupuesto_nuevo',
            'module' => $this->module,
            'slug' => 'budgets-create'
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

        $forms = BudgetForms::select('id', 'name')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        //Módulos activos:
        $module_products = $this->settedModule('products');

        return view('admin.budgets.create')->with(compact('header', 'customer_id', 'customers', 'forms', 'module_products'));
    }

    /**
     * 3. Guardar nuevo presupuesto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/budgets/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Guardamos el form_id si el presupuesto es por formulario:
        $formId = NULL;
        if($request->input('modality') == config('constants.BUDGET_FORM_MODALITY_')){
            $formId = $request->input('form_id');
        }

        //Comprobamos si hay algún descuento por defecto del proveedor con el cliente para sus pedidos:
        $cu_pr = CustomerProviders::customerProvider($request->customer_id, session('currentCompany'));

        $budget = new Order();
        $budget->is_budget = true;
        $budget->modality = $request->modality;
        $budget->form_id = $formId;   
        $budget->customer_id = $request->customer_id;
        $budget->provider_id = session('currentCompany');
        $budget->provider_manager_id = Auth::user()->id;
        $budget->created_by = Auth::user()->id; 
        $budget->discount = $cu_pr->discount;
        $budget->save();

        //Items por defecto en los presupuestos:
        $this->setDefaultItems($budget);

        //Descuento por defecto:
        $budget->setDefaultDiscount();

        return redirect()->route('budgets.edit', $budget);
    }

    /**
     * 3.1. Items por defecto en nuevo presupuesto.
     */
    private function setDefaultItems($order){
        //Si el presupuesto es de tipo formulario y éste tiene items por defecto:
        if($order->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $items = BudgetFormItems::select('budget_form_items.*', 'products.name AS Product', 'products.price', 'products.iva')
            ->join('products', 'budget_form_items.product_id', '=', 'products.id')
            ->where('budget_form_items.form_id', $order->form_id)
            ->where('budget_form_items.fixed_in_budget', 1)
            ->get();

            //Items con importe porcentual:
            $byPercentItems = [];

            if($items->count()){
                foreach($items as $item){
                    $data = [
                        'product_id' => $item->product_id, 
                        'order_id' => $order->id, 
                        'budget_form_block_id' => $item->recid, 
                        'budget_form_item_id' => $item->id, 
                        'concept' => $item->Product, 
                        'qty' => $item->qty_min, 
                        'price' => $item->price, 
                        'item_no_price' => NULL, 
                        'iva' => $item->iva, 
                        'discount' => $item->discount, 
                        'calculated_by_percent' => $item->calculated_by_percent,
                        'deposit_used' => NULL, 
                        'active' => 1, 
                        'status' => NULL, 
                        'print' => 1, 
                        'user_id' => Auth::user()->id
                    ];
                    $item_registered = Item::insert($data);

                    //Guardamos los items cuyo precio se guarda porcentualmente:
                    if($item->calculated_by_percent){
                        array_push($byPercentItems, $item);
                    }
                }
            }

            //Importe de los items con cálculo porcentual:
            //17/10/22: revisar este punto. NO FUNCIONA.
            // if(count($byPercentItems) > 0){
            //     //Importe del presupuesto:
            //     $budget_amount = $order->itemsBudgetOrderAmount(false);

            //     foreach($byPercentItems as $row){
            //         $price = helpers::percent($budget_amount, $row->calculated_by_percent);

            //         $row->price = $price;
            //         $row->save();
            //     }
            // }            
        }
    }

    /**
     * 4. Editar presupuesto.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order, $tab = false){
        $suboption = $order->provider_id == session('currentCompany')? 'presupuesto_editar':'presupuesto_proveedor_editar';

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$suboption,
            'module' => $this->module,
            'slug' => 'budgets'
        ]; 
        //Importe facturado:
        $invoiced = $order->getInvoicedAmount();

        //Facturas:
        $invoices = Invoice::select('id', 'amount', 'amount_with_iva')
        ->where('order_id', $order->id)
        ->get();

        //Patrones de numeración del presupuesto:
        $patterns = false;
        if(!$order->pattern_id){
            $patterns = BudgetPatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Versiones:
        $recid = (!$order->recid)? $order->id:$order->recid;
        $versions = Order::select('id', 'name', 'budget_num')->where('recid', $recid)->get();

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        //Managers: sólo con permisos de edición de presupuestos.
        $provider_managers_ = UserCompanies::getCompanyUsers($order->provider_id, 1);   
        $provider_managers = [];
        foreach($provider_managers_ as $pm){
            if($pm->can('budgets.edit')){
                $provider_managers[$pm->id] = $pm->name.' '.$pm->surname;
            }
        }

        //Configuraciones proveedor:
        $provider_settings = session('companySettings');

        //Sedes del proveedor, para facturación:
        $provider_workplaces = Workplace::where('company_id', $order->provider_id)
        ->get();

        //Centros de coste del proveedor:
        $provider_cost_centers = CostCenters::where('company_id', $order->provider_id)
        ->get();

        //Áreas de negocio del proveedor:
        $provider_business_areas = BusinessAreas::where('company_id', $order->provider_id)
        ->get();

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($order->customer_id);

        //CLIENTE FINAL:
        $final_customer = $order->final_customer_id? Company::select('id', 'name', 'tradename')->find($order->final_customer_id):false;

        //Managers:
        $customer_managers_ = UserCompanies::getCompanyUsers($order->customer_id, 1); 
        $customer_managers = [];
        foreach($customer_managers_ as $cm){
            $customer_managers[$cm->id] = $cm->name.' '.$cm->surname;
        }

        //Sedes del cliente:
        $customer_workplaces = Workplace::where('company_id', $order->customer_id)
        ->get();

        //Centros de coste del cliente:
        $customer_cost_centers = CostCenters::where('company_id', $order->customer_id)
        ->get();

        //Áreas de negocio del cliente:
        $customer_business_areas = BusinessAreas::where('company_id', $order->customer_id)
        ->get();

        //Clientes finales. Sólo clientes del cliente primero:
        $final_customers = Company::select('companies.id', 'companies.name')
        ->join('customer_providers', 'companies.id', '=', 'customer_providers.customer_id')
        ->where('companies.status', 1)
        ->where('companies.id', '!=', $order->provider_id)
        ->where('companies.id', '!=', $order->customer_id)
        ->where('customer_providers.provider_id', $order->provider_id)
        ->orderBy('companies.name', 'ASC')
        ->get();

        //Clientes:
        $customers = Company::where('companies.status', 1)
        ->join('customer_providers', 'companies.id', '=', 'customer_providers.customer_id')
        ->where('companies.id', '!=', $order->provider_id)
        ->where('companies.id', '!=', $order->customer_id)
        ->where('customer_providers.provider_id', $order->provider_id)
        ->orderBy('companies.name', 'ASC')
        ->pluck('companies.name', 'companies.id')
        ->toArray();

        //Configuraciones de cliente:
        $customer_settings = CustomerProviders::where('customer_id', $order->customer_id)
        ->where('provider_id', $order->provider_id)
        ->first();

        //Acceso por defecto del cliente a presupuestos:
        if($customer_settings && $customer_settings->budget_access){
            $order->budget_shared = true;
            $order->save();
        }

        $forms = BudgetForms::select('id', 'name')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        //Importe:
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
            $order_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        //Productos. Activos y aptos para venta:
        $products = [];
        if($order->modality == config('constants.BUDGET_CATALOGUE_MODALITY_') || $order->modality == config('constants.BUDGET_BASIC_MODALITY_')){
            $products = Product::companyProducts($order->provider_id, $order->customer_id);
        }

        //Proyectos:
        $projects = Project::select('id', 'code', 'name')
        ->where('company_id', session('currentCompany'))
        ->get();

        //Proyecto vinculado:
        $project = ProjectOrders::select('projects.id', 'projects.code', 'projects.contest_code', 'projects.name')
        ->join('projects', 'project_orders.project_id', '=', 'projects.id')
        ->where('project_orders.order_id', $order->id)
        ->first();

        //Bloques para presupuesto tipo formulario:
        $blocks = false;
        if($order->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $blocks = BudgetFormItems::getBudgetBlocks($order->form_id, $order->id);
        }

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Templates:
        $templates = false;
        if($order->provider_id == session('currentCompany')){
            $templates = BudgetSettings::select('id', 'name', 'featured')
            ->where('company_id', $order->provider_id)
            ->where('status', 1)
            ->get();
        }

        if($templates && count($templates) == 1 && !$order->budget_template_id){
            $default_template = OrderSettings::select('conditions', 'payment_method', 'observations', 'footer_legend')
            ->find($templates[0]->id);

            $template = [
                'conditions' => $default_template? $default_template->conditions:'', 
                'payment_method' => $default_template? $default_template->payment_method:'',
                'observations' => $default_template? $default_template->observations:'',
                'footer_legend' => $default_template? $default_template->footer_legend:''
            ];

            $order->budget_template_id = $templates[0]->id;
            $order->budget_template = serialize($template);
            $order->save();
        }

        $budget_from = $order->provider_id == session('currentCompany')? 'customers':'providers';

        //Presupuestos de cliente para vincular a cliente final:
        $customer_budgets = false;
        if($order->final_customer_id && $order->customer_id == session('currentCompany')){
            //Presupuestos con el cliente final, obtenemos aquellos que estén todavía abiertos para poder vincular las líneas en la importación y no estén vinculados a otros presupuestos:
            $customer_budgets = Order::select(DB::raw("CONCAT(budget_num, ' - ', name) AS budget"), 'id')
            ->where('is_budget', true)
            ->where('provider_id', $order->customer_id)
            ->where('customer_id', $order->final_customer_id)
            ->where('budget_status', '<', config('constants.BUDGET_STATUS_CANCELLED_'))
            ->pluck('budget', 'id')
            ->toArray();
        }

        $languages = $this->languages;

        //Cambios bloqueados: establecer en función de parámetros por definir que opciones quedan bloqueadas.
        $permitted = true;

        //Módulos activos:
        $module_orders = $this->settedModule('orders');
        $module_products = $this->settedModule('products');
        $module_projects = $this->settedModule('projects');

        return view('admin.budgets.edit')->with(compact('header', 'order', 'tab', 'invoiced', 'invoices', 'patterns', 'versions', 'order_amount', 'order_amount_without_discount', 'provider', 'provider_managers', 'provider_settings', 'provider_workplaces', 'provider_cost_centers', 'provider_business_areas', 'customer', 'final_customer', 'customer_managers', 'customer_workplaces', 'customer_cost_centers', 'customer_business_areas', 'final_customers', 'customers', 'customer_settings', 'forms', 'products', 'projects', 'project', 'blocks', 'currency', 'templates', 'budget_from', 'customer_budgets', 'languages', 'permitted', 'module_orders', 'module_products', 'module_projects'));
    }

    /**
     * 5. Actualizar presupuesto.
     */
    public function update(Request $request, Order $order){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/budgets/'.$order->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Número de presupuesto:
        $patternNum = [];
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('budget', $request->pattern_id);
            
            //Si existen versiones, se actualiza también su budget_num:
            $versions = Order::select('id', 'budget_num')
            ->where('recid', $order->id)
            ->orderBy('id', 'ASC')
            ->get();

            if($versions->count()){
                $k = 2;
                foreach($versions as $vs){
                    $vs->budget_num = $patternNum['pattern_num'].'-v'.$k;
                    $vs->save();
                    $k++;
                }
            }
        }

        //Slug:
        $slug = Str::slug($request->name);

        //Documento de compra:
        $purchase_doc = false;
        if($request->hasFile('doc')){
            $file = $request->file('doc');
            $path = public_path().'/img/purchase-orders';
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

            if($moved){
                $purchase_doc = $fileName;
            }
        }

        //Actualizando datos:
        if($request->pattern_id){
            if(!empty($patternNum)){
                $order->budget_num = $patternNum['pattern_num'];  
                $order->budget_num_cardinal = $patternNum['num']; 
            }            
            $order->budget_pattern_id = $request->pattern_id;
        }
        $order->budget_file_name = $request->budget_file_name;
        $order->budget_old_num = $request->budget_old_num;
        $order->name = $request->name;
        $order->slug = $slug;
        $order->description = $request->description;
        $order->purchase_old_num = $request->purchase_old_num;
        if($purchase_doc){
            $order->purchase_doc = $purchase_doc;
        }        
        $order->customer_manager_id = $request->customer_manager_id;
        $order->customer_workplace_id = $request->customer_workplace_id;
        $order->customer_cost_center_id = $request->customer_cost_center_id;
        $order->customer_business_area_id = $request->customer_business_area_id;
        $order->customer_order = $request->customer_order;
        /*if($customer_order_doc){
            $order->customer_order_doc = $customer_order_doc;
        }*/
        if($request->provider_manager_id){
            $order->provider_manager_id = $request->provider_manager_id;
        }
        if($request->provider_workplace_id){
            $order->provider_workplace_id = $request->provider_workplace_id;
        }
        if($request->provider_cost_center_id){
            $order->provider_cost_center_id = $request->provider_cost_center_id;
        }
        if($request->provider_business_area_id){
            $order->provider_business_area_id = $request->provider_business_area_id;
        }
        $order->final_customer_id = $request->final_customer_id;
        if($request->status){
            $order->budget_status = $request->status;
        }
        if($order->modality == config('constants.BUDGET_BASIC_MODALITY_')){
            $order->bag_amount = $request->bag_amount;
        }else{
            $order->bag_amount = NULL;    
        }
        $order->save();

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', $order)->with(compact('msg'));
    }

    /**
     * 6. Eliminar presupuesto.
     *
     * @param  str $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order){
        if($order->invoiced){
            $alert = trans('textos.pedido_facturado_aviso');
            return redirect()->route('budgets.edit', $order)->with(compact('alert')); 

        }else{
            //Actualizamos registro compartido:
            $order->is_budget = false;
            $order->save();

            $msg = trans('textos.presupuesto_deleted');
            return redirect()->route('budgets.index')->with(compact('msg'));  
        }  
    }

    /**
     * 7. Resetear numeración del presupuesto.
     */
    public function resetNum(Order $order){
        //Eliminamos también en versiones:
        Order::where('recid', $order->id)
        ->update(['budget_num' => NULL]);

        $order->budget_num = NULL;
        $order->budget_num_cardinal = NULL;
        $order->budget_pattern_id = NULL;
        $order->save();

        $msg = trans('textos.numero_deleted');
        return redirect()->route('budgets.edit', $order)->with(compact('msg'));
    }

    /**
     * 8. Añadir responsable de cliente.
     */
    public function addManager(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-manager');

            return redirect('admin/budgets/'.$request->order_id.'/'.$request->return_view)
                ->withErrors($validator)
                ->withInput();
        }

        //Guardamos datos de usuario:
        $user = new User();
        $user->name = $request->input('name');
        $user->surname = $request->input('surname');
        $user->email = $request->input('email');
        $user->isAdmin = 1;
        $user->status = 1;
        $user->remember_token = $request->input('_token');
        $user->save();

        //Guardamos rol:
        $user->assignRole(config('constants.ROLE_ID_'));

        //Actualizamos responsable de cliente en el proyecto:
        $order = Order::select('id', 'customer_id', 'customer_manager_id')->find($request->order_id);
        $order->customer_manager_id = $user->id;
        $order->save(); 

        //Vinculamos usuario a empresa:
        $uc = new UserCompanies();
        $uc->user_id = $user->id;
        $uc->company_id = $order->customer_id;
        $uc->save();       

        $msg = trans('textos.responsable_cliente_agregado');
        return redirect()->route('budgets.'.$request->return_view, $order->id)->with(compact('msg'));
    }

    /**
     * 9. Eliminar documento de orden de compra.
     */
    public function removeOcDoc(Order $order){
        $prev_url = url()->previous();
        $segments = explode('/', $prev_url);
        $return = end($segments);

        File::delete(public_path().'/img/purchase-orders/'.$order->customer_order_doc);

        $order->customer_order_doc = NULL;
        $order->save();

        $msg = trans('textos.archivo_deleted');
        return redirect()->route('budgets.'.$return, $order->id)->with(compact('msg'));        
    }    

    /**
     * 10. Crear presupuesto desde pedido.
     *
     * @param  str $order
     * @return \Illuminate\Http\Response
     */
    public function budgetFromOrder(Order $order){
        $order->is_budget = true;
        $order->save();

        




        // //dd($order);


        // //El pedido toma el título del presupuesto para los casos de formulario o catálogo, no para bolsas:
        // $order_name = $budget->modality != config('constants.BUDGET_BASIC_MODALITY_')? $budget->name:NULL;

        // //Responsable de cliente:
        // $customer_team = NULL;
        // if($budget->manager_customer_id){
        //     $mc = $budget->manager_customer_id.'|';
        //     $customer_team = serialize([$mc]);
        // }

        // //Guardamos pedido:
        // $data = [
        //     'customer_id' => $budget->customer_id,
        //     'provider_id' => $budget->provider_id, 
        //     'final_customer_id' => $budget->final_customer_id,
        //     'user_id' => Auth::user()->id,
        //     'buyer' => $budget->manager_customer_id,
        //     'budget_id' => $budget->id,
        //     'name' => $order_name? $order_name:'',
        //     'customer_team' => $customer_team,
        //     'discount' => $budget->discount
        // ];
        // $order = Order::saveOrder($data);

        // //Actualizando el model proyectos si el presupuesto tiene asociado un proyecto:
        // Project::where('budget_id', $budget->id)
        // ->update(['order_id' => $order->id]);

        // //Pasar los productos de presupuesto tipo catálogo al pedido:
        // if($budget->modality == config('constants.BUDGET_CATALOGUE_MODALITY_')){
        //     $products = BudgetProducts::where('budget_id', $budget->id)->get();

        //     foreach($products as $row){
        //         $line = new Item;
        //         $line->product_id = $row->product_id;
        //         $line->order_id = $order->id;
        //         $line->qty = $row->qty;
        //         $line->price = $row->price;
        //         $line->observations = $row->observations;
        //         $line->user_id = Auth::user()->id;
        //         $line->save();
        //     }
        // }

        // //Pasar las líneas de presupuesto tipo formulario al pedido:
        // if($budget->modality == config('constants.BUDGET_FORM_MODALITY_')){
        //     //Le pasamos el order_id a los productos de dicho presupuesto:
        //     Item::where('budget_id', $budget->id)->update(['order_id' => $order->id]);
        // }

        // //Si es un presupuesto tipo Free y tiene generada una agrupación de pedidos, pasamos el nuevo pedido a la agrupación de pedidos:
        // if($budget->modality == config('constants.BUDGET_BASIC_MODALITY_')){
        //     $order_group = OrderGroups::select('id')->where('budget_id', $budget->id)->first();

        //     if($order_group){
        //         $ogo = new OrderGroupsOrders();
        //         $ogo->og_id = $order_group->id;
        //         $ogo->order_id = $order->id;
        //         $ogo->user_id = Auth::user()->id;
        //         $ogo->save();
        //     }     

        //     //Pasamos el order_id a las líneas de los presupuestos englobados, de haberlos:
        //     $included_budgets = Order::select('id')
        //     ->where('father', $budget->id)
        //     ->where('status', config('constants.BUDGET_STATUS_APPROVED_'))
        //     ->get();

        //     if($included_budgets->count() > 0){
        //         foreach($included_budgets as $ib){
        //             Item::where('budget_id', $ib->id)
        //             ->whereNull('order_id')
        //             ->update(['order_id' => $order->id]);    
        //         }
        //     }         
        // }

        $msg = trans('textos.presupuesto_creado');
        return redirect()->route('budgets.edit', $order)->with(compact('msg'));
    }

    /**
     * 11. Actualizar notas del presupuesto.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateComments(Request $request){
        $order = Order::find($request->order_id);
        $order->comments = $request->comments;
        $order->save();

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', [$order->id, 'comments'])->with(compact('msg'));
    }

    /**
     * 12. Actualizar plantilla de presupuesto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateTemplate(Request $request, Order $order){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/budgets/'.$order->id.'/edit/template')
                ->withErrors($validator)
                ->withInput();
        }

        //Si viene declarada la plantilla:
        if($request->template_id){
            //Plantilla original:
            if($order->budget_template_id == $request->template_id){
                $template = [
                    'conditions' => $request->conditions, 
                    'payment_method' => $request->payment_method,
                    'observations' => $request->observations,
                    'footer_legend' => $request->footer_legend
                ];
            }else{
                //Plantilla original:
                $orig_template = BudgetSettings::find($request->template_id);

                $template = [
                    'conditions' => $orig_template->conditions, 
                    'payment_method' => $orig_template->payment_method,
                    'observations' => $orig_template->observations,
                    'footer_legend' => $orig_template->footer_legend
                ];
            }

        //No viene declarada la plantilla:
        }else{
            $template = [
                'conditions' => $request->conditions, 
                'payment_method' => $request->payment_method,
                'observations' => $request->observations,
                'footer_legend' => $request->footer_legend
            ];
        }

        $template = serialize($template);

        $order->budget_template_id = $request->template_id;
        $order->budget_template = $template;
        $order->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('budgets.edit', [$order->id, 'template'])->with(compact('msg'));
    }

    /**
     * 13. Eliminar plantilla del presupuesto.
     */
    public function removeTemplate(Order $order){
        $order->budget_template_id = NULL;
        $order->budget_template = NULL;
        $order->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('budgets.edit', [$order->id, 'template'])->with(compact('msg'));
    }

    /**
     * 14. Imprimir presupuesto PDF.
     */
    public function print(Order $order){
        $provider = Company::find($order->provider_id);
        $customer = Company::find($order->customer_id);

        //Template condiciones:
        $template = $order->budget_template? unserialize($order->budget_template):false;

        $fechaLiteral = helpers::fechaLiteral($order->created_at, session()->get('locale'));

        //Textos para items sin precio.
        $noPriceLinesTexts = helpers::statusBudgetLinesWithoutPrice();

        $blocks = false;
        $itemsSelected = false;

        //Para presupuestos modalidad Formulario:
        if($order->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $blocks = BudgetFormItems::getBudgetBlocks($order->form_id, $order->id);

            $itemsSelected = Item::select('items.*', 'budget_form_items.recid')
            ->leftJoin('budget_form_items', 'items.budget_form_item_id', '=', 'budget_form_items.id')
            ->where('items.order_id', $order->id)
            ->get();

            //Array del id recurrente:
            $arrRecid = [];
            foreach($itemsSelected as $row){
                array_push($arrRecid, $row->recid);
            }
            //Obtenemos valores únicos del array:
            $arrRecid = array_unique($arrRecid);
        }

        //Items para presupuestos de modalidad Formulario:
        /*if($order->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $blocks = BudgetFormItems::where('recid', '0')
            ->where('form_id', $order->form_id)
            ->get();

            $itemsSelected = BudgetItems::where('budget_id', $order->id)->get();
        
        }else{
            $blocks = false;
            $itemsSelected = false;
        }*/

        //Para presupuestos modalidad Catálogo:

        //Calculando importe sin líneas de importe porcentual:
        if($itemsSelected){
            $amountWithoutPercentItems = $order->itemsBudgetOrderAmount(false);
        }

        $fecha = false;

        $company_settings = session('companySettings');

        //Sede y teléfonos proveedor:
        $provider_workplace = false;
        $provider_phones = false;
        if($order->provider_workplace_id){
            $provider_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $order->provider_workplace_id)
            ->first();  

            $provider_phones = Phone::select('phone_number')
            ->where('workplace_id', $provider_workplace->id)
            ->get();

            $web = parse_url($provider_workplace->website);
        }

        //Sede cliente:
        $customer_workplace = false;
        if($order->customer_workplace_id){
            $customer_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $order->customer_workplace_id)
            ->first();  
        }

        //Provider manager:
        $provider_manager = false;
        if($order->provider_manager_id){
            $provider_manager = User::select('name', 'surname')->find($order->provider_manager_id);
        }   

        //Customer manager:
        $customer_manager = false;
        if($order->customer_manager_id){
            $customer_manager = User::select('name', 'surname')->find($order->customer_manager_id);
        }    

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Color corporativo:
        $bg_color = $company_settings->primary_color? $company_settings->primary_color:'#FFF';

        //Título .pdf:
        $pdf_title = $order->budget_file_name? helpers::slugFiles($order->budget_file_name, '_', false):$order->budget_num;

        //Locale:
        $locale = session()->get('locale')? session()->get('locale'):App::getLocale();

        //Según la modalidad de presupuesto utiliza una plantilla u otra:
        if($order->modality == config('constants.BUDGET_BASIC_MODALITY_')){
            $pdf = PDF::loadView('pdf/budget-free', compact('order', 'company', 'customer', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'customer_workplace', 'provider_phones', 'provider_manager', 'customer_manager', 'blocks', 'itemsSelected', 'currency', 'bg_color', 'locale'));
            return $pdf->download($pdf_title.'.pdf');

        }elseif($order->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $pdf = PDF::loadView('pdf/budget-by-form', compact('order', 'provider', 'customer', 'template', 'fechaLiteral', 'blocks', 'itemsSelected', 'arrRecid', 'amountWithoutPercentItems', 'fecha', 'company_settings', 'provider_workplace', 'customer_workplace', 'provider_phones', 'provider_manager', 'customer_manager', 'blocks', 'itemsSelected', 'currency', 'bg_color', 'noPriceLinesTexts', 'locale'));
            return $pdf->download($pdf_title.'.pdf');  

        }elseif($order->modality == config('constants.BUDGET_CATALOGUE_MODALITY_')){
            $pdf = PDF::loadView('pdf/budget-catalogue', compact('order', 'company', 'customer', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'customer_workplace', 'provider_phones', 'provider_manager', 'customer_manager', 'blocks', 'itemsSelected', 'currency', 'bg_color', 'locale'));
            return $pdf->download($pdf_title.'.pdf');
        }
    }

    /**
     * 15. Crear versión de presupuesto.
     *
     * La versión se realiza del presupuesto copiado, pero mantiene como referente ($recid) el presupuesto original.
     */
    public function modify(Order $order){
        //Id del original:
        $originalId = !$order->recid? $order->id:$order->recid;

        //Obtenemos nº de versiones:
        $versions = Order::where('recid', $originalId)
        ->orWhere('id', $originalId)
        ->count();

        //La primera versión debe llevar la extensión v2, y el original v1:
        if($versions == 0){
            $versions = 1;
        }

        $ext = 'v'.($versions+1);
        
        //Nombre y budget_num deben modificarse desde el presupuesto original:
        $origBudget = Order::find($originalId);
        $name = $origBudget? $origBudget->name.' '.$ext:trans('textos.presupuesto');
        $budget_num = $origBudget? $origBudget->budget_num.'-'.$ext:'';

        $version = new Order();
        $version->is_budget = true;
        $version->recid = $originalId;
        $version->budget_num = $budget_num;
        $version->name = $name;
        $version->slug = Str::slug($name);
        $version->modality = $order->modality;
        $version->description = $order->description;
        $version->comments = $order->comments;
        $version->form_id = $order->form_id;
        $version->delivery_date = $order->delivery_date;
        $version->budget_template_id = $order->budget_template_id;
        $version->budget_template = $order->budget_template;
        $version->customer_id = $order->customer_id;
        $version->customer_manager_id = $order->customer_manager_id;
        $version->customer_workplace_id = $order->customer_workplace_id;
        $version->customer_cost_center_id = $order->customer_cost_center_id;
        $version->customer_business_area_id = $order->customer_business_area_id;
        $version->customer_order = $order->customer_order;
        $version->customer_order_doc = $order->customer_order_doc;
        $version->customer_team = $order->customer_team;
        $version->customer_products = $order->customer_products;
        $version->provider_id = $order->provider_id;
        $version->provider_manager_id = $order->provider_manager_id;
        $version->provider_workplace_id = $order->provider_workplace_id;
        $version->provider_cost_center_id = $order->provider_cost_center_id;
        $version->provider_business_area_id = $order->provider_business_area_id;
        $version->provider_team = $order->provider_team;
        $version->final_customer_id = $order->final_customer_id;
        $version->final_customer_manager_id = $order->final_customer_manager_id;
        $version->created_by = Auth::user()->id;
        $version->discount = $order->discount;
        $version->discount_amount = $order->discount_amount;
        $version->save();

        //Copiamos bloques libres e items:
        if($order->modality == config('constants.BUDGET_FORM_MODALITY_')){
            //Copiamos bloques que se hayan creado libremente:
            $blocks = BudgetFormItems::where('recid', '0')
            ->where('order_id', $order->id)
            ->get();
            $arrBlockId = [];
            
            foreach($blocks as $block){
                $bk = new BudgetFormItems();
                $bk->recid = '0';
                $bk->order_id = $version->id;
                $bk->name = $block->name;
                $bk->translations = $block->translations;
                $bk->observations = $block->observations;
                $bk->save();

                //Los nuevos bloques deben corresponder a los bloques de la versión anterior para poder vincularles sus respectivos items:
                $arrBlockId[$block->id] = $bk->id;
            }
        }

        //Copiamos products:
        $items = Item::where('order_id', $order->id)->get();
        foreach($items as $row){
            $blockId = isset($arrBlockId[$row->budget_form_block_id])? $arrBlockId[$row->budget_form_block_id]:$row->budget_form_block_id;

            $vit = new Item();
            $vit->product_id = $row->product_id? $row->product_id:NULL;
            $vit->order_id = $version->id;
            $vit->budget_form_block_id = $blockId;
            $vit->budget_form_item_id = $row->budget_form_item_id;
            $vit->concept = $row->concept;
            $vit->observations = $row->observations;
            $vit->qty = $row->qty;
            $vit->price = $row->price;
            $vit->item_no_price = $row->item_no_price;
            $vit->iva = $row->iva;
            $vit->discount = $row->discount;
            $vit->calculated_by_percent = $row->calculated_by_percent;
            $vit->days = $row->days;
            $vit->days_continue = $row->days_continue;
            $vit->days_continue_discount = $row->days_continue_discount;
            $vit->active = $row->active;
            $vit->status = $row->status;
            $vit->print = $row->print;
            $vit->user_id = Auth::user()->id;
            $vit->save();
        }

        $msg = trans('textos.presupuesto_versionado');
        return redirect()->route('budgets.edit', $version->id)->with(compact('msg')); 
    }
    
    /**
     * 16. Actualizar estado de presupuesto.
     */
    public function status(Request $request, Order $order, $status){
        if($request->ajax()){    
            $order->budget_status = $status;
            $order->save();

            return response()->json(true);
        }
    }

    /**
     * 17. Cambio de proveedor.
     */
    public function setProvider(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }
        
        $order = Order::select('id', 'is_order', 'provider_id', 'provider_manager_id', 'provider_workplace_id', 'provider_cost_center_id', 'provider_business_area_id', 'provider_project_id', 'provider_team', )->find($request->order_id);

        //Comprobamos que no exista pedido:
        if($order->is_order){
            $alert = trans('textos.proveedor_cambio_error');
            return redirect()->route('budgets.edit', $request->order_id)->with(compact('alert'));    
        }

        $order->provider_id = $request->company_id;
        $order->provider_manager_id = NULL;
        $order->provider_workplace_id = NULL;
        $order->provider_cost_center_id = NULL;
        $order->provider_business_area_id = NULL;
        $order->provider_project_id = NULL;
        $order->provider_team = NULL;
        $order->save();

        //Cambiamos la sesión de la empresa:
        session(['currentCompany' => $request->company_id]);

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', $request->order_id)->with(compact('msg'));
    }

    /**
     * 18. Definir modalidad de presupuesto.
     *
     * Cuando se crea presupuesto a partir de un pedido o proyecto.
     */
    public function setModality(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $order = Order::select('id', 'modality', 'form_id')->find($request->order_id);
        $order->modality = $request->modality;
        $order->form_id = $request->form_id;
        $order->save();

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', $order)->with(compact('msg'));
    }

    /**
     * 19. Copiar presupuesto.
     */
    public function copy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Datos presupuesto original:
        $order = Order::find($request->order_id);
        $items = Item::where('order_id', $order->id)->get();

        //Nuevo presupuesto:
        $new_budget = new Order();
        $new_budget->is_budget = true;
        $new_budget->name = $order->name.' - '.trans('textos.copia');
        $new_budget->slug = $order->slug.'-'.trans('textos.copia');
        $new_budget->modality = $order->modality;
        $new_budget->description = $order->description;
        $new_budget->comments = $order->comments;
        $new_budget->form_id = $order->form_id;    
        $new_budget->created_by = Auth::user()->id; 
        $new_budget->budget_template_id = $order->template_id;
        $new_budget->budget_template = $order->template;
        $new_budget->customer_id = $request->customer;
        $new_budget->provider_id = session('currentCompany');
        $new_budget->provider_manager_id = Auth::user()->id;
        $new_budget->provider_workplace_id = $order->provider_workplace_id;
        $new_budget->provider_cost_center_id = $order->provider_cost_center_id;
        $new_budget->provider_business_area_id = $order->provider_business_area_id;
        $new_budget->provider_team = $order->provider_team;
        //$new_budget->discount = $order->discount;
        //$new_budget->discount_amount = $order->discount_amount;
        //$new_budget->updated_by = Auth::user()->id;
        $new_budget->save();

        foreach($items as $i){
            $it = new Item();
            $it->product_id = $i->product_id;
            $it->order_id = $new_budget->id;
            $it->budget_form_block_id = $i->budget_form_block_id;
            $it->budget_form_item_id = $i->budget_form_item_id;
            $it->budget_form_block_id = $i->budget_form_block_id;
            $it->budget_order = $i->budget_order;
            $it->concept = $i->concept;
            $it->observations = $i->observations;
            $it->qty = $i->qty;
            $it->price = $i->price;
            $it->item_no_price = $i->item_no_price;
            $it->iva = $i->iva;
            $it->discount = $i->discount;
            $it->calculated_by_percent = $i->calculated_by_percent;
            $it->invoiced = $i->invoiced;
            $it->deposit_used = $i->deposit_used;
            $it->days = $i->days;
            $it->days_continue = $i->days_continue;
            $it->days_continue_discount = $i->days_continue_discount;
            $it->active = $i->active;
            $it->status = $i->status;
            $it->print = $i->print;
            $it->user_id = Auth::user()->id;
            $it->save();
        }

        $msg = trans('textos.presupuesto_copiado');
        return redirect()->route('budgets.edit', $new_budget)->with(compact('msg'));
    }

    /**
     * 20. Setear bloque para líneas de formulario.
     */
    public function setBlock(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-set-block');

            return redirect('/admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $order = Order::find($request->order_id);

        if($order){
            Item::where('order_id', $order->id)
            ->whereNull('budget_form_block_id')
            ->update(['budget_form_block_id' => $request->block_id]);
        }

        $msg = trans('textos.presupuesto_lineas_vinculadas');
        return redirect()->route('budgets.edit', $request->order_id)->with(compact('msg'));
    }

    /**
     * 21. Presupuestos proveedores.
     */
    public function fromProviders(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.presupuestos_proveedores',
            'module' => $this->module,
            'slug' => 'budgets'
        ]; 

        $budget_from = 'providers'; 

        return view('admin.budgets.index')->with(compact('header', 'budget_from'));    
    }

    /**
     * 22. Permitir acceso a cliente.
     */
    public function customerAccess(Request $request, $order_id, $status){
        if($request->ajax()){
            Order::where('id', $order_id)
            ->update(['budget_shared' => $status]);
        }    
    }

    /**
     * 23. Ver presupuesto.
     */
    public function show(Order $order){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.presupuesto_ver',
            'module' => $this->module,
            'slug' => 'budgets'
        ];  

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($order->customer_id);

        //CLIENTE FINAL:
        $final_customer = $order->final_customer_id? Company::select('id', 'name', 'tradename')->find($order->final_customer_id):false;

        //Importe:
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
            $order_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        //Moneda:
        $currency = $this->co_currency;

        return view('admin.budgets.show')->with(compact('header', 'order', 'provider', 'customer', 'final_customer', 'order_amount', 'currency'));   
    }

    /**
     * 23.1. Línea de presupuesto para DataTable.
     */
    public function getItemsDT(Order $order){
        $items = Item::where('order_id', $order->id);

        //Moneda:
        $currency = $this->co_currency;

        //Importe del pedido sin contar los items con precio porcentual:
        $order_amount = $order->getAmount(true, false, false);

        return DataTables::of($items)
        ->addColumn('price', function($item) use($currency){
            if($item->price > 0){
                return $item->price.$currency->symbol;
            }elseif(!$item->price && $item->calculated_by_percent > 0){
                return trans('textos.por_porcentaje').' ('.$item->calculated_by_percent.'%)';
            }
        })
        ->addColumn('discount', function($item){
            if($item->discount > 0){
                return $item->discount.'%';
            }else{
                return '';
            }
        })
        ->addColumn('amount', function($item) use($currency, $order_amount){
            $amount = '';
            if($item->price != 0){
                if($item->calculated_by_percent > 0){
                    $amount = helpers::itemAmountPercentage($item->price, $order_amount['amount_with_discount']);
                    return $amount.$currency->symbol.'<br><small>('.trans('textos.precio_porcentual').')</small>';
                }else{
                    $amount = helpers::itemAmount($item); 
                    return $amount.$currency->symbol;
                }            
            }else{
                return trans('textos.precio_sin_definir');
            }              
        })
        ->addColumn('observations', function($item){
            return $item->observations;
        })
        ->addColumn('acciones', function($item) use($order){
            return '<a href="#" class="btn btn-xs btn-info btnSeeItem" rel="tooltip" title="'.ucfirst(trans('textos.ver')).'"><i class="fa fa-eye"></i></a>';    
        })
        ->rawColumns(['amount', 'observations', 'acciones'])
        ->make(true);
    }

    /**
     * 24. Aceptar presupuesto por parte del cliente.
     */
    public function accept(Order $order){
        $order->accepted_date = date('Y-m-d');
        $order->accepted_by = Auth::user()->id;
        $order->budget_status = config('constants.BUDGET_STATUS_APPROVED_')[0];
        $order->save();

        $msg = trans('textos.presupuesto_aceptado');
        return redirect()->route('budgets.edit', [$order->id])->with(compact('msg'));
    }

    /**
     * 25. Cancelar presupuesto por parte del cliente.
     */
    public function cancel(Order $order){
        $order->accepted_date = NULL;
        $order->accepted_by = Auth::user()->id;
        $order->budget_status = config('constants.BUDGET_STATUS_CANCELLED_');
        $order->save();

        $msg = trans('textos.presupuesto_cancelado');
        return redirect()->route('budgets.edit', [$order->id])->with(compact('msg'));    
    }

    /**
     * 26. Importar presupuesto de proveedor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-import-budget');

            return redirect('/admin/budgets/'.$request->id.'/show')
                ->withErrors($validator)
                ->withInput();
        }

        $original_budget = Order::find($request->id);

        //Si en el presupuesto original, el cliente final disfruta de un descuento por defecto por parte del proveedor, tal descuento se arrastra como descuento del cliente hacia el cliente final.
        $discount = NULL;
        if($original_budget->final_customer_id){
            $original_discount = CustomerProviders::select('discount')
            ->where('customer_id', $original_budget->final_customer_id)    
            ->where('provider_id', $original_budget->provider_id)
            ->first();

            if($original_discount) $discount = $original_discount->discount;
        }

        //Si no recibimos $budget_id, creamos nuevo presupuesto.
        if($request->budget_id){
            $budget = Order::find($request->budget_id);

        }else{
            $budget = new Order();
            $budget->is_budget = true;
            $budget->modality = $original_budget->modality;
            $budget->provider_id = session('currentCompany');
            $budget->customer_id = $original_budget->final_customer_id;
            $budget->provider_manager_id = $original_budget->customer_manager_id;   //El manager de cliente del presupuesto original se convierte ahora en el manager del proveedor con respecto al cliente final.
            if($request->form_id){
                $budget->form_id = $request->form_id;
            }
            $budget->discount = $discount;
            $budget->save();
        }

        //Añadimos el budget_id al que se vincula el nuevo presupuesto
        $budget->first_provider_order = $request->id;
        $budget->save();

        //Exportamos los items del presupuesto original al nuevo presupuesto:
        $items = Item::where('order_id', $request->id)
        ->get();

        if($items->count()){
            $i = 1;

            foreach($items as $item){
                Item::create([
                    'order_id' => $budget->id,
                    'budget_order' => $i,
                    'concept' => $item->concept,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'discount' => $item->discount,
                    'calculated_by_percent' => $item->calculated_by_percent,
                    //'batch_id' => $item->batch_id,
                    'observations' => $item->observations,
                    'days' => $item->days,
                    'days_continue' => $item->days_continue,
                    'days_continue_discount' => $item->days_continue_discount,
                    'user_id' => Auth::user()->id
                ]);
                $i++;
            }
        }

        $msg = trans('textos.presupuesto_importado');
        return redirect()->route('budgets.edit', $budget->id)->with(compact('msg'));
    }

}
