<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use File;
use PDF;

//Models:
use App\Models\Budget;
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\BudgetOrderSharedFields;
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
use App\Models\Phone;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectShared;
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
     */
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:budgets');
        $this->middleware('budget_get', ['only' => ['edit', 'update', 'destroy', 'resetNum', 'removeOcDoc', 'createOrder', 'removeTemplate', 'print', 'modify']]);
        $this->middleware('budget_post', ['only' => ['updateComments', 'updateTemplate', 'setProvider']]);
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
                'id' => 'required|integer',
                'name' => 'required',
                'surname' => 'required',
                'email' => 'required|string|email|max:191|unique:users,email,NULL,id,deleted_at,NULL'
            ],[
                'email.unique' => trans('textos.email_existe')
            ]);

        //Validación template:
        }elseif(isset($data['submit_update_template'])){
            return Validator::make($data, [
                'budget_id' => 'required|integer',
                'template_id' => 'nullable|integer'
            ]); 
        
        //Validación cambio proveedor:
        }elseif(isset($data['submit-set-provider'])){
            return Validator::make($data, [
                'budget_id' => 'required|integer',
                'company_id' => 'required|integer'
            ]);   

        //Validación actualizar modalidad: 
        }elseif(isset($data['submit_set_modality'])){
            return Validator::make($data, [   
                'budget_id' => 'required|integer',
                'modality' => 'required'
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
    public function getBudgetsDT($from){
        //Presupuestos para cliente:
        if($from == 'customers'){
            $query = BudgetOrderSharedFields::select( 'budget_order_shared_fields.id', 'budget_order_shared_fields.discount', 'budgets.id AS budgetId', 'budgets.budget_num', 'budgets.budget_old_num', 'budgets.name', 'budgets.status', 'budgets.modality', 'budgets.created_at', 'companies.name AS Company', 'orders.id AS orderId', 'orders.order_num', 'users.name As Nombre', 'users.surname', 'u_.name AS CustomerManagerName', 'u_.surname AS CustomerManagerSurname')
            ->join('budgets', 'budgets.id', '=', 'budget_order_shared_fields.budget_id')
            ->join('companies', 'budget_order_shared_fields.customer_id', '=', 'companies.id')
            ->leftJoin('orders', 'budget_order_shared_fields.order_id', '=', 'orders.id')
            ->leftJoin('users', 'budget_order_shared_fields.provider_manager_id', '=', 'users.id')
            ->leftJoin('users AS u_', 'budget_order_shared_fields.customer_manager_id', '=', 'u_.id')
            ->where('budget_order_shared_fields.provider_id', session('currentCompany'));
             
            
            // $qA = DB::table('budgets')
            //        ->select('id AS budgetId, budget_num, budget_old_num, name, status, modality, created_at');

        
            // $query = BudgetOrderSharedFields::select( 'budget_order_shared_fields.id')
            // ->joinSub($qA, 'budgets_', function($join){
            //     $join->on('budget_order_shared_fields.budget_id', '=', 'budgets_.id');
            // })
            
            //$query = BudgetOrderSharedFields::select('id', 'budget_id', 'discount')
            //->with('budget')
            // ->with(['budget' => function($q){
            //     $q->select('budget_num');
            // }])
            // ->with('customer', function($q){
            //     $q->select('name');
            // })
            //->with('customer')
            //->get();
            //->where('provider_id', session('currentCompany'))->get();

            //dd($query);


        //Presupuestos de proveedor:
        }else{
            $query = BudgetOrderSharedFields::select('budgets.*', 'companies.name AS Company', 'users.name As Nombre', 'users.surname')
            ->join('budgets', 'budgets.id', '=', 'budget_order_shared_fields.budget_id')
            ->join('companies', 'budget_order_shared_fields.provider_id', '=', 'companies.id')
            ->leftJoin('users', 'budget_order_shared_fields.customer_manager_id', '=', 'users.id')
            ->where('budget_order_shared_fields.customer_id', session('currentCompany'));
        }
        $budgets = $query->get(); 

        //Permisos del usuario:
        $editar = Auth::user()->can('budgets.edit');
        $eliminar = Auth::user()->can('budgets.destroy');

        $arrEstados = Helpers::budgetStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Abreviaturas modalidades de presupuesto:
        $abrev = [
            1 => trans('textos.basico'),
            10 => trans('textos.formulario'),
            20 => trans('textos.catalogo')
        ];

        //Ampliación de información de los objetos:
        if($budgets){
            foreach($budgets as $row){
                //Enviamos las fechas ya formateadas:
                $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

                //Importe:
                $arr_amounts = $row->getAmount();    
                $row->amount = $arr_amounts['amount_with_discount'].$currency->symbol;

                //Estado:
                $row->estado = $row->status? $arrEstados[$row->status]:'';
                $row->estados = $row->status.';'.$strEstados;

                //Modalidad:
                $row->modalidad = $row->modality? ucfirst($abrev[$row->modality]):''; 

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar;

                //Entorno del presupuesto: cliente o proveedor:
                $row->from = $from;

                //Estado del presupuesto:
                $row->budget_status = $row->status? ucfirst(Helpers::budgetStatus($row->status)):'';
            }
        }

        return datatables($budgets)->toJson();
    }

    /**
     * 2. Formulario nuevo presupuesto.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
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

        return view('admin.budgets.create')->with(compact('header', 'customers', 'forms', 'module_products'));
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

        $budget = new Budget();
        $budget->modality = $request->modality;
        $budget->form_id = $formId;    
        $budget->created_by = Auth::user()->id; 
        $budget->save();

        //Comprobamos si hay algún descuento por defecto del proveedor con el cliente para sus pedidos:
        $cu_pr = CustomerProviders::customerProvider($request->customer_id, session('currentCompany'));

        //Guardamos campos compartido con order:
        $bosf = new BudgetOrderSharedFields();
        $bosf->budget_id = $budget->id;
        $bosf->customer_id = $request->customer_id;
        $bosf->provider_id = session('currentCompany');
        $bosf->provider_manager_id = Auth::user()->id;
        $bosf->discount = $cu_pr->discount;
        $bosf->save();

        //Items por defecto en los presupuestos:
        $this->setDefaultItems($budget, $bosf);

        return redirect()->route('budgets.edit', $budget);
    }

    /**
     * 3.1. Items por defecto en nuevo presupuesto.
     */
    private function setDefaultItems($budget, $bosf){
        //Si el presupuesto es de tipo formulario y éste tiene items por defecto:
        if($budget->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $items = BudgetFormItems::select('budget_form_items.*', 'products.name AS Product', 'products.price', 'products.iva')
            ->join('products', 'budget_form_items.product_id', '=', 'products.id')
            ->where('budget_form_items.form_id', $budget->form_id)
            ->where('budget_form_items.fixed_in_budget', 1)
            ->get();

            //Items con importe porcentual:
            $byPercentItems = [];

            if($items->count()){
                foreach($items as $item){
                    $data = [
                        'product_id' => $item->product_id, 
                        'budget_order_id' => $bosf->id, 
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
            if(count($byPercentItems) > 0){
                //Importe del presupuesto:
                $shared = BudgetOrderSharedFields::select('id')->where('budget_id', $budget->id)->first();
                $budget_amount = $shared->itemsBudgetOrderAmount(false);

                foreach($byPercentItems as $row){
                    $price = Helpers::percent($budget_amount, $row->calculated_by_percent);

                    $row->price = $price;
                    $row->save();
                }
            }            
        }
    }

    /**
     * 4. Editar presupuesto.
     *
     * @param  \App\Budget  $budget
     * @return \Illuminate\Http\Response
     */
    public function edit(Budget $budget, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.presupuesto_editar',
            'module' => $this->module,
            'slug' => 'budgets'
        ]; 
        
        $shared = BudgetOrderSharedFields::where('budget_id', $budget->id)->first();

        //Importe facturado:
        $invoiced = $shared->getInvoicedAmount();

        //Facturas:
        $invoices = Invoice::select('id', 'amount', 'amount_with_iva')
        ->where('shared_id', $shared->id)
        ->get();

        //Patrones de numeración del presupuesto:
        $patterns = false;
        if(!$budget->pattern_id){
            $patterns = BudgetPatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Versiones:
        $recid = (!$budget->recid)? $budget->id:$budget->recid;
        $versions = Budget::select('id', 'name', 'budget_num')->where('recid', $recid)->get();

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($shared->provider_id);

        //Managers: sólo con permisos de edición de presupuestos.
        $provider_managers_ = UserCompanies::getCompanyUsers($shared->provider_id, 1);   
        $provider_managers = [];
        foreach($provider_managers_ as $pm){
            if($pm->can('budgets.edit')){
                $provider_managers[$pm->id] = $pm->name.' '.$pm->surname;
            }
        }

        //Configuraciones proveedor:
        $provider_settings = session('companySettings');

        //Sedes del proveedor, para facturación:
        $provider_workplaces = Workplace::where('company_id', $shared->provider_id)
        ->get();

        //Centros de coste del proveedor:
        $provider_cost_centers = CostCenters::where('company_id', $shared->provider_id)
        ->get();

        //Áreas de negocio del proveedor:
        $provider_business_areas = BusinessAreas::where('company_id', $shared->provider_id)
        ->get();

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($shared->customer_id);

        //Managers:
        $customer_managers_ = UserCompanies::getCompanyUsers($shared->customer_id, 1); 
        $customer_managers = [];
        foreach($customer_managers_ as $cm){
            $customer_managers[$cm->id] = $cm->name.' '.$cm->surname;
        }

        //Sedes del cliente:
        $customer_workplaces = Workplace::where('company_id', $shared->customer_id)
        ->get();

        //Centros de coste del cliente:
        $customer_cost_centers = CostCenters::where('company_id', $shared->customer_id)
        ->get();

        //Áreas de negocio del cliente:
        $customer_business_areas = BusinessAreas::where('company_id', $shared->customer_id)
        ->get();

        //Clientes finales. Sólo clientes del cliente primero:
        $final_customers = Company::select('companies.id', 'companies.name')
        ->join('customer_providers', 'companies.id', '=', 'customer_providers.customer_id')
        ->where('companies.status', 1)
        ->where('companies.id', '!=', $shared->provider_id)
        ->where('companies.id', '!=', $shared->customer_id)
        ->where('customer_providers.provider_id', $shared->provider_id)
        ->orderBy('companies.name', 'ASC')
        ->get();

        //Clientes:
        $customers = Company::where('status', 1)
        ->where('id', '!=', $shared->provider_id)
        ->where('id', '!=', $shared->customer_id)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Configuraciones de cliente:
        $customer_settings = CustomerProviders::where('customer_id', $shared->customer_id)
        ->where('provider_id', $shared->provider_id)
        ->first();

        $forms = BudgetForms::select('id', 'name')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();

        //Pedido:
        $order = $shared->order_id? Order::find($shared->order_id):false;

        //Importe:
        if($shared->free_amount){
            $budget_amount = $shared->free_amount;
        }else{
            $items = $shared->getBudgetOrderItems();
            $arr_amounts = $shared->getAmount();    

            $budget_amount = $arr_amounts['amount_with_discount'];
            $budget_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        //Productos. Activos y aptos para venta:
        $products = [];
        if($budget->modality == config('constants.BUDGET_CATALOGUE_MODALITY_') || $budget->modality == config('constants.BUDGET_BASIC_MODALITY_')){
            $products = Product::companyProducts($shared->provider_id);
        }

        //Proyectos:
        $projects = Project::select('id', 'code', 'name')
        ->where('company_id', session('currentCompany'))
        ->get();

        //Proyecto vinculado:
        $project = ProjectShared::select('projects.id', 'projects.code', 'projects.contest_code', 'projects.name')
        ->join('projects', 'project_shareds.project_id', '=', 'projects.id')
        ->where('project_shareds.shared_id', $shared->id)
        ->first();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Templates:
        $templates = false;
        if($shared->provider_id == session('currentCompany')){
            $templates = BudgetSettings::select('id', 'name', 'featured')
            ->where('company_id', $shared->provider_id)
            ->where('status', 1)
            ->get();
        }

        $budget_from = 'customers';

        $languages = $this->languages;

        //Módulos activos:
        $module_orders = $this->settedModule('orders');
        $module_products = $this->settedModule('products');
        $module_projects = $this->settedModule('projects');

        return view('admin.budgets.edit')->with(compact('header', 'budget', 'tab', 'shared', 'invoiced', 'invoices', 'patterns', 'versions', 'budget_amount', 'budget_amount_without_discount', 'provider', 'provider_managers', 'provider_settings', 'provider_workplaces', 'provider_cost_centers', 'provider_business_areas', 'customer', 'customer_managers', 'customer_workplaces', 'customer_cost_centers', 'customer_business_areas', 'final_customers', 'customers', 'customer_settings', 'forms', 'order', 'products', 'projects', 'project', 'currency', 'templates', 'budget_from', 'languages', 'module_orders', 'module_products', 'module_projects'));
    }

    /**
     * 5. Actualizar presupuesto.
     */
    public function update(Request $request, Budget $budget){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/budgets/'.$budget->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Número de presupuesto:
        $patternNum = [];
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('budget', $request->pattern_id);
            
            //Si existen versiones, se actualiza también su budget_num:
            $versions = Budget::select('id', 'budget_num')
            ->where('recid', $budget->id)
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

        //Actualizando datos:
        if($request->pattern_id){
            if(!empty($patternNum)){
                $budget->budget_num = $patternNum['pattern_num'];  
                $budget->num_cardinal = $patternNum['num']; 
            }            
            $budget->pattern_id = $request->pattern_id;
        }
        $budget->file_name = $request->file_name;
        $budget->budget_old_num = $request->budget_old_num;
        $budget->name = $request->name;
        $budget->slug = $slug;
        $budget->description = $request->description;
        $budget->status = $request->status;
        $budget->save();

        $customer_order_doc = false;
        if($request->hasFile('customer_order_doc')){
            $file = $request->file('customer_order_doc');
            $path = public_path().'/img/purchase-orders';
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

            if($moved){
                $customer_order_doc = $fileName;
            }
        }

        //Actualizando en budget_order_shared_fields:
        $shared = BudgetOrderSharedFields::where('budget_id', $budget->id)->first(); 
        $shared->customer_manager_id = $request->customer_manager_id;
        $shared->customer_workplace_id = $request->customer_workplace_id;
        $shared->customer_business_area_id = $request->customer_business_area_id;



        $shared->customer_order = $request->customer_order;
        if($customer_order_doc){
            $shared->customer_order_doc = $customer_order_doc;
        }

        $shared->provider_manager_id = $request->provider_manager_id;
        $shared->provider_workplace_id = $request->provider_workplace_id;
        $shared->provider_business_area_id = $request->provider_business_area_id;


        $shared->final_customer_id = $request->final_customer_id;



        if($budget->modality == config('constants.BUDGET_BASIC_MODALITY_')){
            $shared->free_amount = $request->free_amount;
        }else{
            $shared->free_amount = NULL;    
        }
        $shared->save();

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', $budget)->with(compact('msg'));
    }

    /**
     * 6. Eliminar presupuesto.
     *
     * @param  str $budget
     * @return \Illuminate\Http\Response
     */
    public function destroy(Budget $budget){
        BudgetOrderSharedFields::where('budget_id', $budget->id)->delete();

        $budget->delete();

        $msg = trans('textos.presupuesto_deleted');
        return redirect()->route('budgets.index')->with(compact('msg'));    
    }

    /**
     * 7. Resetear numeración del presupuesto.
     */
    public function resetNum(Budget $budget){
        //Eliminamos también en versiones:
        Budget::where('recid', $budget->id)
        ->update(['budget_num' => NULL]);

        $budget->budget_num = NULL;
        $budget->num_cardinal = NULL;
        $budget->pattern_id = NULL;
        $budget->save();

        $msg = trans('textos.numero_deleted');
        return redirect()->route('budgets.edit', $budget)->with(compact('msg'));
    }

    /**
     * 8. Añadir responsable de cliente.
     */
    public function addManager(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-manager');

            return redirect('admin/budgets/'.$request->input('id').'/'.$request->return_view)
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
        $shared = BudgetOrderSharedFields::where('budget_id', $request->input('id'))->first();
        $shared->customer_manager_id = $user->id;
        $shared->save(); 

        //Vinculamos usuario a empresa:
        $uc = new UserCompanies();
        $uc->user_id = $user->id;
        $uc->company_id = $shared->customer_id;
        $uc->save();       

        $msg = trans('textos.responsable_cliente_agregado');
        return redirect()->route('budgets.'.$request->return_view, $shared->budget_id)->with(compact('msg'));
    }

    /**
     * 9. Eliminar documento de orden de compra.
     */
    public function removeOcDoc(Budget $budget){
        $prev_url = url()->previous();
        $segments = explode('/', $prev_url);
        $return = end($segments);

        $shared = BudgetOrderSharedFields::select('id', 'customer_order_doc')
        ->where('budget_id', $budget->id)
        ->first();

        File::delete(public_path().'/img/purchase-orders/'.$shared->customer_order_doc);

        $shared->customer_order_doc = NULL;
        $shared->save();

        $msg = trans('textos.archivo_deleted');
        return redirect()->route('budgets.'.$return, $budget->id)->with(compact('msg'));        
    }    

    /**
     * 10. Crear presupuesto desde pedido.
     *
     * @param  str $order
     * @return \Illuminate\Http\Response
     */
    public function budgetFromOrder(Order $order){
        $budget = new Budget();
        $budget->created_by = Auth::user()->id;
        $budget->save();

        //Shared:
        $shared = BudgetOrderSharedFields::where('order_id', $order->id)->first();
        $shared->budget_id = $budget->id;
        $shared->save();




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
        //     $included_budgets = Budget::select('id')
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
        return redirect()->route('budgets.edit', $budget)->with(compact('msg'));
    }

    /**
     * 11. Actualizar notas del presupuesto.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateComments(Request $request){
        $budget = Budget::find($request->input('budget_id'));
        $budget->comments = $request->input('comments');
        $budget->save();

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', [$request->input('budget_id'), 'comments'])->with(compact('msg'));
    }

    /**
     * 12. Actualizar plantilla de presupuesto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateTemplate(Request $request, Budget $budget){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/budgets/'.$budget->id.'/edit/template')
                ->withErrors($validator)
                ->withInput();
        }

        //Si viene declarada la plantilla:
        if($request->template_id){
            //Plantilla original:
            if($budget->template_id == $request->template_id){
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

        $budget->template_id = $request->template_id;
        $budget->template = $template;
        $budget->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('budgets.edit', [$budget->id, 'template'])->with(compact('msg'));
    }

    /**
     * 13. Eliminar plantilla del presupuesto.
     */
    public function removeTemplate(Budget $budget){
        $budget->template_id = NULL;
        $budget->template = NULL;
        $budget->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('budgets.edit', [$budget->id, 'template'])->with(compact('msg'));
    }

    /**
     * 14. Imprimir presupuesto PDF.
     */
    public function print(Budget $budget){
        $shared = BudgetOrderSharedFields::where('budget_id', $budget->id)->first();

        $provider = Company::find($shared->provider_id);
        $customer = Company::find($shared->customer_id);

        //Template condiciones:
        $template = unserialize($budget->template);

        $fechaLiteral = Helpers::fechaLiteral($budget->created_at, session()->get('locale'));

        //Textos para items sin precio.
        $noPriceLinesTexts = Helpers::statusBudgetLinesWithoutPrice();

        $blocks = false;
        $itemsSelected = false;

        //Para presupuestos modalidad Formulario:
        if($budget->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $blocks = BudgetFormItems::getBudgetBlocks($budget->form_id, $budget->id);

            $itemsSelected = Item::select('items.*', 'budget_form_items.recid')
            ->leftJoin('budget_form_items', 'items.budget_form_item_id', '=', 'budget_form_items.id')
            ->where('items.budget_order_id', $budget->id)
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
        /*if($budget->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $blocks = BudgetFormItems::where('recid', '0')
            ->where('form_id', $budget->form_id)
            ->get();

            $itemsSelected = BudgetItems::where('budget_id', $budget->id)->get();
        
        }else{
            $blocks = false;
            $itemsSelected = false;
        }*/

        //Para presupuestos modalidad Catálogo:

        //Calculando importe sin líneas de importe porcentual:
        if($itemsSelected){
            $amountWithoutPercentItems = $shared->itemsBudgetOrderAmount(false);
        }


        //dd($arrRecid, $amountWithoutPercentItems);

        $fecha = false;

        $company_settings = session('companySettings');

        //Sede y teléfonos proveedor:
        $provider_workplace = false;
        $provider_phones = false;
        if($shared->provider_workplace_id){
            $provider_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $shared->provider_workplace_id)
            ->first();  

            $provider_phones = Phone::select('phone_number')
            ->where('workplace_id', $provider_workplace->id)
            ->get();

            $web = parse_url($provider_workplace->website);
        }

        //Sede cliente:
        $customer_workplace = false;
        if($shared->customer_workplace_id){
            $customer_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->leftJoin('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $shared->customer_workplace_id)
            ->first();  
        }

        //Provider manager:
        $provider_manager = false;
        if($shared->provider_manager_id){
            $provider_manager = User::select('name', 'surname')->find($shared->provider_manager_id);
        }   

        //Customer manager:
        $customer_manager = false;
        if($budget->customer_manager_id){
            $customer_manager = User::select('name', 'surname')->find($shared->customer_manager_id);
        }    

        //Moneda:
        $currency = CompanySettings::co_currency(0);



        //Color corporativo:
        $bg_color = $company_settings->primary_color? $company_settings->primary_color:'#FFF';

        //Título .pdf:
        $pdf_title = $budget->file_name? Helpers::slugFiles($budget->file_name, '_', false):$budget->budget_num;

        //Locale:
        $locale = session()->get('locale')? session()->get('locale'):App::getLocale();

        //Según la modalidad de presupuesto utiliza una plantilla u otra:
        if($budget->modality == config('constants.BUDGET_BASIC_MODALITY_')){
            $pdf = PDF::loadView('pdf/budget-free', compact('budget', 'company', 'customer', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'customer_workplace', 'provider_phones', 'manager_provider', 'manager_customer', 'blocks', 'itemsSelected', 'currency', 'bg_color', 'locale'));
            return $pdf->download($pdf_title.'.pdf');

        }elseif($budget->modality == config('constants.BUDGET_FORM_MODALITY_')){
            $pdf = PDF::loadView('pdf/budget-by-form', compact('budget', 'provider', 'customer', 'template', 'fechaLiteral', 'blocks', 'itemsSelected', 'arrRecid', 'amountWithoutPercentItems', 'fecha', 'company_settings', 'provider_workplace', 'customer_workplace', 'provider_phones', 'provider_manager', 'customer_manager', 'blocks', 'itemsSelected', 'currency', 'bg_color', 'noPriceLinesTexts', 'locale'));
            return $pdf->download($pdf_title.'.pdf');  

        }elseif($budget->modality == config('constants.BUDGET_CATALOGUE_MODALITY_')){
            $pdf = PDF::loadView('pdf/budget-catalogue', compact('budget', 'company', 'customer', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'customer_workplace', 'provider_phones', 'manager_provider', 'manager_customer', 'blocks', 'itemsSelected', 'currency', 'bg_color', 'locale'));
            return $pdf->download($pdf_title.'.pdf');
        }
    }

    /**
     * 15. Crear versión de presupuesto.
     *
     * La versión se realiza del presupuesto copiado, pero mantiene como referente ($recid) el presupuesto original.
     */
    public function modify(Budget $budget){
        //Id del original:
        $originalId = !$budget->recid? $budget->id:$budget->recid;

        //Registro compartido:
        $shared = BudgetOrderSharedFields::where('budget_id', $budget->id)->first();

        //Obtenemos nº de versiones:
        $versions = Budget::where('recid', $originalId)
        ->orWhere('id', $originalId)
        ->count();

        //La primera versión debe llevar la extensión v2, y el original v1:
        if($versions == 0){
            $versions = 1;
        }

        $ext = 'v'.($versions+1);
        
        //Nombre y budget_num deben modificarse desde el presupuesto original:
        $origBudget = Budget::find($originalId);
        $name = $origBudget? $origBudget->name.' '.$ext:trans('textos.presupuesto');
        $budget_num = $origBudget? $origBudget->budget_num.'-'.$ext:'';

        $version = new Budget();
        $version->recid = $originalId;
        $version->budget_num = $budget_num;
        $version->name = $name;
        $version->slug = Str::slug($name);
        $version->modality = $budget->modality;
        $version->description = $budget->description;
        $version->comments = $budget->comments;
        $version->form_id = $budget->form_id;
        $version->delivery_date = $budget->delivery_date;
        $version->template_id = $budget->template_id;
        $version->template = $budget->template;
        $version->created_by = Auth::user()->id;
        $version->save();

        //Creamos registro compartido:
        $shared_v = new BudgetOrderSharedFields();
        $shared_v->budget_id = $version->id;
        $shared_v->customer_id = $shared->customer_id;
        $shared_v->customer_workplace_id = $shared->customer_workplace_id;
        $shared_v->customer_cost_center_id = $shared->customer_cost_center_id;
        $shared_v->customer_business_area_id = $shared->customer_business_area_id;
        $shared_v->customer_order = $shared->customer_order;
        $shared_v->customer_order_doc = $shared->customer_order_doc;
        $shared_v->customer_team = $shared->customer_team;
        $shared_v->customer_products = $shared->customer_products;
        $shared_v->provider_id = $shared->provider_id;
        $shared_v->provider_manager_id = $shared->provider_manager_id;
        $shared_v->provider_workplace_id = $shared->provider_workplace_id;
        $shared_v->provider_cost_center_id = $shared->provider_cost_center_id;
        $shared_v->provider_business_area_id = $shared->provider_business_area_id;
        $shared_v->provider_team = $shared->provider_team;
        $shared_v->final_customer_id = $shared->final_customer_id;
        $shared_v->final_customer_manager_id = $shared->final_customer_manager_id;
        $shared_v->third_part_budget = $shared->third_part_budget;
        $shared_v->free_amount = $shared->free_amount;
        $shared_v->discount = $shared->discount;
        $shared_v->discount_amount = $shared->discount_amount;
        $shared_v->save();

        //Copiamos bloques libres e items:
        if($budget->modality == config('constants.BUDGET_FORM_MODALITY_')){
            //Copiamos bloques que se hayan creado libremente:
            $blocks = BudgetFormItems::where('recid', '0')
            ->where('budget_id', $budget->id)
            ->get();
            $arrBlockId = [];
            
            foreach($blocks as $block){
                $bk = new BudgetFormItems();
                $bk->recid = '0';
                $bk->budget_id = $version->id;
                $bk->name = $block->name;
                $bk->translations = $block->translations;
                $bk->observations = $block->observations;
                $bk->save();

                //Los nuevos bloques deben corresponder a los bloques de la versión anterior para poder vincularles sus respectivos items:
                $arrBlockId[$block->id] = $bk->id;
            }
        }

        //Copiamos products:
        $items = Item::where('budget_order_id', $shared->id)->get();
        foreach($items as $row){
            $blockId = isset($arrBlockId[$row->budget_form_block_id])? $arrBlockId[$row->budget_form_block_id]:$row->budget_form_block_id;

            $vit = new Item();
            $vit->product_id = $row->product_id? $row->product_id:NULL;
            $vit->budget_order_id = $shared_v->id;
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


        // if($budget->modality == config('constants.BUDGET_CATALOGUE_MODALITY_')){
        //     $products = BudgetProducts::where('budget_id', $originalId)->get();
        //     foreach($products as $row){
        //         $vp = new BudgetProducts();
        //         $vp->budget_id = $version->id;
        //         $vp->product_id = $row->product_id;
        //         $vp->price = $row->price;
        //         $vp->qty = $row->qty;
        //         $vp->observations = $row->observations;
        //         $vp->save();
        //     }
        // }

        $msg = trans('textos.presupuesto_versionado');
        return redirect()->route('budgets.edit', $version->id)->with(compact('msg')); 
    }
    
    /**
     * 16. Actualizar estado de presupuesto.
     */
    public function status(Request $request, Budget $budget, $status){
        if($request->ajax()){    
            $budget->status = $status;
            $budget->save();

            return response()->json(true);
        }
    }

    /**
     * 17. Cambio de proveedor.
     */
    public function setProvider(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/budgets/'.$request->budget_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }
        
        $shared = BudgetOrderSharedFields::select('id', 'order_id', 'provider_id', 'provider_manager_id', 'provider_workplace_id', 'provider_cost_center_id', 'provider_business_area_id', 'provider_project_id', 'provider_team', )->where('budget_id', $request->budget_id)->first();

        //Comprobamos que no exista pedido:
        if($shared->order_id){
            $alert = trans('textos.proveedor_cambio_error');
            return redirect()->route('budgets.edit', $request->budget_id)->with(compact('alert'));    
        }

        $shared->provider_id = $request->company_id;
        $shared->provider_manager_id = NULL;
        $shared->provider_workplace_id = NULL;
        $shared->provider_cost_center_id = NULL;
        $shared->provider_business_area_id = NULL;
        $shared->provider_project_id = NULL;
        $shared->provider_team = NULL;
        $shared->save();

        //Cambiamos la sesión de la empresa:
        session(['currentCompany' => $request->company_id]);

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', $request->budget_id)->with(compact('msg'));
    }

    /**
     * 18. Definir modalidad de presupuesto.
     *
     * Cuando se crea presupuesto a partir de un pedido o proyecto.
     */
    public function setModality(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/budgets/'.$request->budget_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $budget = Budget::find($request->budget_id);
        $budget->modality = $request->modality;
        $budget->form_id = $request->form_id;
        $budget->save();

        $msg = trans('textos.presupuesto_updated');
        return redirect()->route('budgets.edit', $budget)->with(compact('msg'));
    }

}
