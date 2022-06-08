<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Budget;
use App\Models\BudgetOrderSharedFields;
use App\Models\BusinessAreas;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CostCenters;
use App\Models\CustomerProviders;
use App\Models\Delivery;
use App\Models\DeliveryOrders;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderDocs;
use App\Models\OrderGroupOrders;
use App\Models\OrderGroups;
use App\Models\OrderPatterns;
use App\Models\OrderPrevisions;
use App\Models\OrderSettings;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectPurchases;
use App\Models\ProjectShared;
use App\Models\PurchasePatterns;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\UserWorkplaces;
use App\Models\Workplace;

class OrderController extends Controller{
    /**
     * 1. Pedidos por cliente o proveedor.
     * 1.1. Lista de pedidos por cliente o proveedor.
     * 1.2. Listado de pedidos por cliente y proveedor para DataTable.
     * 2. Formulario nuevo pedido.
     * 3. Guardar nuevo pedido.
     * 3.1. Generar pedido desde presupuesto.
     * 4. Editar pedido.
     * 5. Actualizar pedido.
     * 6. Eliminar pedido.
     * 7. Resetear numeración del pedido.
     * 8. Obtener equipo de pedido.
     * 9. Obtener líneas de pedido.
     * 10. Actualizar estado de pedido.
     * 11. Panel control de ventas.
     * 12. Panel control de compras.
     * 13. Vincular compra a proyecto.
     * 14. Desvincular compra a proyecto.
     * 15. Activar entrega de pedido.
     * 16. Actualizar plantilla de pedido.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:orders');
        $this->middleware('budget_get', ['only' => ['orderFromBudget']]);
        $this->middleware('order_get', ['only' => ['edit', 'update', 'destroy', 'print', 'setOrderDelivery']]);
        $this->middleware('project_purchases', ['only' => ['addPurchaseToProject']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación index:
        if(isset($data['submit_filter'])){
            //Si no viene declarado order_id
            if(!$data['order_id']){
                return Validator::make($data, [
                    'year' => 'required|integer',
                    'customer_id' => 'required_without:provider_id|required_if:provider_id,==,0',
                    'provider_id' => 'required_without:customer_id|required_if:customer_id,==,0'
                ],[
                    'customer_id.required_without' => trans('textos.required_cliente_proveedor'),
                    'customer_id.required_if' => trans('textos.required_cliente_proveedor'),
                    'provider_id.required_without' => trans('textos.required_cliente_proveedor'),
                    'provider_id.required_if' => trans('textos.required_cliente_proveedor'),    
                ]);
            }else{
                return Validator::make($data, [
                    'order_id' => 'required'
                ]); 
            }

        //Validación create:
        }elseif(isset($data['submit_create'])){
            return Validator::make($data, [
                'customer_id' => 'required_without:provider_id',
                'provider_id' => 'required_without:customer_id'
            ],[
                'customer_id.required_without' => trans('textos.required_cliente_proveedor'),
                'provider_id.required_without' => trans('textos.required_cliente_proveedor')    
            ]);

        //Validación store:
        }elseif(isset($data['submit_save'])){
            return Validator::make($data, [
                'company_id' => 'required|integer',
                'type' => 'required|in:sale,purchase'
            ],[
                'type.in' => trans('textos.pedido_tipo_error')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|numeric',
                'year' => 'nullable|array',
                'month' => 'nullable|array',
                'percent' => 'nullable|array'
            ]); 

        //Validación update owner:
        }elseif(isset($data['submit_update_owner'])){
            return Validator::make($data, [
                'order_id' => 'required|numeric',
                'owner' => 'required|numeric'
            ]);

        //Validación template:
        }elseif(isset($data['submit_update_template'])){
            return Validator::make($data, [
                'order_id' => 'required|numeric',
                'template_id' => 'required|numeric'
            ]); 

        //Validación add discount:
        }elseif(isset($data['submit_order_discount'])){
            return Validator::make($data, [
                'discount' => 'required_without:discount_amount|numeric|max:100',
                'discount_amount' => 'nullable|numeric'
            ],[
                'discount.required_without' => trans('textos.obligatorio_sin_valor_neto'),
                'discount.numeric' => trans('textos.valor_numerico')
            ]);

        //Validación set report:
        }elseif(isset($data['submit_set_report'])){
            return Validator::make($data, [ 
                'customer_id' => 'required|integer',
                'provider_id' => 'required|integer',
                'from' => 'required',
                'to' => 'required'
            ],[
                'from.date' => 'Introduce una fecha válida',
                'to.date' => 'Introduce una fecha válida'
            ]);

        //Vincular compra a proyecto:
        }elseif(isset($data['submit_add_purchase'])){
            return Validator::make($data, [ 
                'order_id' => 'required|integer',
                'project_id' => 'required|integer',      
            ]);
        }
    }
    
    /**
     * 1. Pedidos por cliente o proveedor.
     */
    public function index($year = false, $customer_id = false, $provider_id = false, $owner = false, $status = false){
        //Si se cambia la empresa en sesión se toman los pedidos relativos a ésta:
        if($customer_id 
            && $customer_id != session('currentCompany') 
            && $customer_id != 'all'
            && $provider_id 
            && $provider_id != session('currentCompany')
            && $provider_id != 'all'
        ){
            return redirect()->route('orders.index');
        }

        if($customer_id && ($provider_id == session('currentCompany') || $customer_id == 'all') ){
            $ordersType = 'ventas';
        }elseif($provider_id && ($customer_id == session('currentCompany') || $provider_id == 'all') ){
            $ordersType = 'compras';
        }else{
            $ordersType = 'listado';
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$ordersType,
            'module' => $this->module,
            'slug' => 'orders'
        ]; 

        //Cliente:
        $customer = false;
        if($customer_id && is_numeric($customer_id)){
            $customer = Company::find($customer_id);
        }

        //Proveedor:
        $provider = false;
        if($provider_id && is_numeric($provider_id)){
            $provider = Company::find($provider_id);
        }

        //Comprobamos si la empresa hace gestión de clientes:
        $customers = false;
        if(session('companySettings') && session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }
        
        //Comprobamos si la empresa hace gestión de proveedores:
        $providers = false;
        if(session('companySettings') && session('companySettings')['providers_management'] == 1){
            $providers = CustomerProviders::getCompanyProviders(session('currentCompany'));
        }

        //Propietarios de pedidos (sólo usuarios activos):
        $owners = UserCompanies::getCompanyUsers(session('currentCompany'), 1);

        //Estados de pedido:
        $arrStatus = Helpers::orderStatus();

        //Pasamos el índice del status de pedido:
        if($status && $status != ''){
            foreach($arrStatus as $i => $os){
                if($status == $os){
                    $status = $i;
                    break;
                }
            }
        }

        //Status de pedidos:
        $order_status = '';
        foreach($arrStatus as $i => $os){
            $order_status .= '<options value="'.$i.'">'.$os.'</option>';
        }

        //Módulos activos:
        $module_accounting = $this->settedModule('accounting');

        return view('admin.orders.index')->with(compact('header', 'owner', 'status', 'customer_id', 'provider_id', 'customer', 'provider', 'customers', 'providers', 'owners', 'order_status', 'module_accounting'));
    }

    /**
     * 1.1. Lista de pedidos por cliente o proveedor.
     */
    public function getOrdersBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/orders/list')
                ->withErrors($validator)
                ->withInput();
        }

        //Si viene declarado el número de pedido redirigimos:
        if($request->order_id){
            //Comprobamos si el valor recibido corresponde a id, order_num o old_order:
            $order = Order::select('id')
            ->where('id', $request->order_id)
            ->orWhere('order_num', $request->order_id)
            ->orWhere('order_old_num', $request->order_id)
            ->orWhere('purchase_num', $request->order_id)
            ->first();

            if(!$order){
                $alert = trans('textos.pedido_no_existe');
                return redirect()->route('orders.index')->with(compact('alert'));   
                exit;
            }else{
                return redirect()->route('orders.edit', $order->id);    
                exit;   
            }
        }

        //Si no viene declarado ni cliente ni proveedor redirigimos al inicio:
        if(!$request->customer_id && !$request->provider_id){
            $alert = trans('textos.cliente_proveedor_obligatorios');
            return redirect()->route('orders.index')->with(compact('alert'));   
            exit;
        }

        //Sólo podemos tomar un criterio, nunca ambos:
        if($request->customer_id){
            $customer_id = $request->customer_id;
            $provider_id = session('currentCompany');   
            session(['orderCompanySelected' => ['customer', $request->customer_id]]);
        }
        if($request->provider_id && !$request->customer_id){
            $customer_id = session('currentCompany');
            $provider_id =  $request->provider_id;
            if(!$request->customer_id){
                session(['orderCompanySelected' => ['provider', $request->provider_id]]);   
            }
        }

        return redirect()->route('orders.index', [$request->year, $customer_id, $provider_id, $request->owner]);
    }

    /**
     * 1.2. Listado de pedidos por cliente y proveedor para DataTable.
     */
    public function getOrdersDT($customer = false, $provider = false, $owner = false, $status = false){
        //La empresa en sesión debe ser cliente o proveedor del pedido.
        if($customer && $customer != session('currentCompany') && $provider && $provider != session('currentCompany')){
            return false;
            exit;
        }

        $environment = $customer != session('currentCompany')? 'provider':'customer';

        $orders = BudgetOrderSharedFields::select('budget_order_shared_fields.id', 'budget_order_shared_fields.discount', 'orders.id AS orderId', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'orders.estimated_delivery_date', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.delivery_date', 'orders.status', 'orders.invoiced', 'orders.created_at', 'business_areas.name AS businessArea', 'cost_centers.name AS costCenter', 'budgets.id as budgetId', 'budgets.budget_num', 'order_group_orders.og_id')
        ->join('orders', 'budget_order_shared_fields.order_id', '=', 'orders.id')
        ->leftJoin('business_areas', 'budget_order_shared_fields.customer_business_area_id', '=', 'business_areas.id')
        ->leftJoin('cost_centers', 'budget_order_shared_fields.customer_cost_center_id', '=', 'cost_centers.id')
        //->leftJoin('workplaces', 'orders.workplace_id', '=', 'workplaces.id')
        ->leftJoin('budgets', 'budget_order_shared_fields.budget_id', '=', 'budgets.id')
        ->leftJoin('order_group_orders', 'orders.id', '=', 'order_group_orders.order_id')
        ->when($owner, function($q) use($owner){
            return $q->where('orders.owner', $owner);
        })
        ->when($customer && is_numeric($customer), function($q) use($customer){
            return $q->where('budget_order_shared_fields.customer_id', $customer)
            ->orWhere('budget_order_shared_fields.final_customer_id', $customer);
        })
        ->when($provider && is_numeric($provider), function($q) use($provider){
            return $q->where('budget_order_shared_fields.provider_id', $provider);
        })
        ->orderBy('orders.created_at', 'DESC')
        ->get();
        //Filtro status:
        /*$query->when($status && $status != '', function($q) use($status){
            return $q->where('orders.status', $status);
        });*/
        

        $arrEstados = Helpers::orderStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        //Permisos del usuario:
        $editar = Auth::user()->can('orders.edit');
        $eliminar = Auth::user()->can('orders.destroy');

        //Ampliación de información de los objetos:
        if($orders){
            //Comprobamos si tiene activo el módulo de contabilidad para poder facturar el pedido:
            $invoice = $this->settedModule('contabilidad');

            //Moneda:
            $currency = CompanySettings::co_currency(0);

            foreach($orders as $row){
                //Enviamos las fechas ya formateadas:
                $row->fechaEntregaPrevista = Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]);
                $row->fechaEntrega = Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]);
                $row->alta = $row->created_at? $row->created_at->format($this->languages[session()->get('locale')][4]):'';

                //Estado del pedido:
                $row->estado = $row->status? ucfirst($arrEstados[$row->status]):'';
                $row->estados = $row->status.';'.$strEstados;

                //Importe:
                $arr_amounts = $row->getAmount();    
                $row->amount = $arr_amounts['amount_with_discount'].$currency->symbol;

                //Orden de compra:
                $row->purchase_number = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');

                //Facturación de pedido. El pedido debe estar en algún status que permita su facturación:
                $row->invoiceable = in_array($row->status, config('constants.ORDER_STATUS_INVOICED_'))? $invoice:false;

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar;

                //Estado de la facturación:
                if($row->invoiceable){
                    $invoices_amount = Invoice::where('order_id', $row->orderId)
                    ->sum('total_amount');  

                    if($invoices_amount == 0){
                        $row->invoices = 0;
                    }elseif($invoices_amount > 0 && $invoices_amount < $amount[2]){
                        $row->invoices = 1;
                    }else{
                        $row->invoices = 2;
                    }
                }else{
                    $row->invoices = false;
                }

                $row->currency = $currency;

                //Presupuesto:
                if($row->budgetId){
                    $row->budget = $row->budget_num? $row->budget_num:$row->budgetId;
                }else{
                    $row->budget = '';
                }

                //Entorno:
                $row->environment = $environment;
            }
        }

        return datatables($orders)->toJson();
    }

    /**
     * 2. Formulario nuevo pedido.
     */
    public function create($type){
        if($type == 'sale'){
            $suboption = 'venta_nueva';
            $slug = 'sales-create';
        }else{
            $suboption = 'compra_nueva';
            $slug = 'purchases-create';
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$suboption,
            'module' => $this->module,
            'slug' => $slug
        ]; 

        $companies = false;
        //La empresa ha de tener habilitada la gestión de clientes:
        if($type == 'sale' && session('companySettings') && session('companySettings')['customers_management']){
            $companies = CustomerProviders::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'customer_providers.customer_id', '=', 'companies.id')
            ->where('customer_providers.provider_id', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->get();
        }

        //La empresa ha de tener habilitada la gestión de proveedores:
        if($type == 'purchase' && session('companySettings') && session('companySettings')['providers_management']){
            $companies = CustomerProviders::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'customer_providers.provider_id', '=', 'companies.id')
            ->where('customer_providers.customer_id', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->get();
        }

        //Módulos activos:
        $module_products = $this->settedModule('products');

        return view('admin.orders.create')->with(compact('header', 'type', 'companies', 'module_products'));    
    }

    /**
     * 3. Guardar nuevo pedido.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/orders/'.$request->type.'/create')
                ->withErrors($validator)
                ->withInput();
        }

        //Creamos pedido:
        $order = new Order();
        $order->created_by = Auth::user()->id;
        $order->save();

        $customer_id = $request->type == 'sale'? $request->company_id:session('currentCompany');
        $provider_id = $request->type == 'purchase'? $request->company_id:session('currentCompany');

        //Creamos registro compartido:
        $shared = new BudgetOrderSharedFields();
        $shared->order_id = $order->id;
        $shared->customer_id = $customer_id;
        if($request->type == 'purchase'){
            $shared->customer_manager_id = Auth::user()->id;
        }
        $shared->provider_id = $provider_id;
        if($request->type == 'sale'){
            $shared->provider_manager_id = Auth::user()->id;
        }
        $shared->save();

        //Guardamos previsión de facturación por defecto:
        OrderPrevisions::store($order->id);

        //Si se trata de un pedido de compra, se envía notificación al proveedor:
        if($shared->customer_id == session('currentCompany')){
            $buyer = '';
            foreach(session('companies') as $co){
                if($co->id == session('currentCompany')){
                    $buyer = $co->name;
                    break;
                }
            }

            //Destinatarios de la notificación:
            foreach($request->receivers as $receiver){
                $user = User::select('id', 'name', 'surname', 'email')->find($receiver);

                $user->title = trans('textos.nuevo_pedido_venta');
                $user->sender = Auth::user()->name.' '.Auth::user()->surname;
                $user->user_id = Auth::user()->id;
                $user->content = trans('textos.nuevo_pedido_venta_aviso', ['company' => $buyer]);
                $user->order_id = $order->id;
                $user->notify(new PurchaseMessage($user));  
            }  
        }

        $msg = trans('textos.pedido_creado');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 3.1. Generar pedido desde presupuesto.
     */
    public function orderFromBudget(Budget $budget){
        //Creamos pedido:
        $order = new Order();
        $order->created_by = Auth::user()->id;
        $order->save();

        //Actualizamos registro compartido.
        $shared = BudgetOrderSharedFields::where('budget_id', $budget->id)->first();
        $shared->order_id = $order->id;
        $shared->save();

        //Guardamos previsión de facturación por defecto:
        OrderPrevisions::store($order->id);

        $msg = trans('textos.pedido_creado');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 4. Editar pedido.
     */
    public function edit(Order $order, $tab = false){
        $shared = BudgetOrderSharedFields::where('order_id', $order->id)->first();

        $suboption = $shared->customer_id == session('currentCompany')? 'compra_editar':'venta_editar';

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$suboption,
            'module' => $this->module,
            'slug' => 'orders'
        ]; 

        //Cambios bloqueados: establece en función de si el pedido está facturado o no, las opciones que quedan bloqueadas.
        $permitted = true;

        //Patrones de numeración del pedido:
        $patterns = false;
        if(!$order->pattern_id){
            $patterns = OrderPatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Patrones de órdenes de compra:
        $purchase_patterns = false;
        if(!$order->purchase_num){
            $purchase_patterns = PurchasePatterns::where('company_id', $shared->customer_id)
            ->pluck('name', 'id')
            ->toArray();
        }

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($shared->provider_id);

        $provider_managers = Employee::select(
            DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
        ->join('users', 'employees.user_id', '=', 'users.id')
        ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        ->where('users.status', 1)
        ->where('users.isAdmin', 1)
        ->where('employees.status', 1)
        ->where('contracts.company_id', $shared->provider_id)
        ->orderBy('users.name')
        ->pluck('user', 'users.id')
        ->toArray();

        $provider_business_areas = BusinessAreas::where('company_id', $shared->provider_id)
        ->pluck('name', 'id')
        ->toArray();

        $provider_cost_centers = CostCenters::where('company_id', $shared->provider_id)
        ->pluck('name', 'id')
        ->toArray();

        $provider_workplaces = Workplace::where('company_id', $shared->provider_id)
        ->pluck('name', 'id')
        ->toArray();

        //En caso de no existir $budget->provider_workplace, buscamos sede vinculada al usuario en sesión:
        $default_workplace = false;
        if(!$order->provider_workplace){
            $defaultWorkplace = UserWorkplaces::select('workplace_id')
            ->where('user_id', Auth::user()->id)
            ->get();

            if($defaultWorkplace->count() == 1){
                $default_workplace = $defaultWorkplace[0]->workplace_id;   
            }
        }

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($shared->customer_id);

        //31/05/22: duda sobre si obtener sólo empleados o usuarios del cliente. Por el momento modifico la query para obtener usuarios.
        // $customer_managers = Employee::select(
        //     DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
        // ->join('users', 'employees.user_id', '=', 'users.id')
        // ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        // ->where('users.status', 1)
        // ->where('users.isAdmin', 1)
        // ->where('employees.status', 1)
        // ->where('contracts.company_id', $shared->customer_id)
        // ->orderBy('users.name')
        // ->pluck('user', 'users.id')
        // ->toArray();
        
        $customer_managers_ = UserCompanies::getCompanyUsers($shared->customer_id, 1); 
        $customer_managers = [];
        foreach($customer_managers_ as $cm){
            $customer_managers[$cm->id] = $cm->name.' '.$cm->surname;
        }

        $customer_business_areas = BusinessAreas::where('company_id', $shared->customer_id)
        ->pluck('name', 'id')
        ->toArray();

        $customer_cost_centers = CostCenters::where('company_id', $shared->customer_id)
        ->pluck('name', 'id')
        ->toArray();

        $customer_workplaces = Workplace::where('company_id', $shared->customer_id)
        ->pluck('name', 'id')
        ->toArray();

        //Configuraciones de cliente:
        $customer_settings = CustomerProviders::where('customer_id', $shared->customer_id)
        ->where('provider_id', $shared->provider_id)
        ->first();

        //Confirmación pedido:
        $confirmed_by = false;
        if($order->customer_confirmation){
            $confirmed_by = User::select('name', 'surname')->find($order->confirmed_by);    
        }

        //Clientes:
        $customers = Company::where('status', 1)
        ->where('id', '!=', $shared->provider_id)
        ->where('id', '!=', $shared->customer_id)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Presupuesto:
        $budget = $shared->budget_id? Budget::find($shared->budget_id):false;

        //Importe del pedido:
        if($shared->free_amount){
            $order_amount = $shared->free_amount;
            $order_amount_without_discount = false;
        }else{
            $items = $shared->getBudgetOrderItems();
            $arr_amounts = $shared->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
            $order_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        //Agrupaciones de pedidos:
        $order_groups = OrderGroups::where('provider_id', session('currentCompany'))
        ->where('customer_id', $shared->customer_id)
        ->where('status', config('constants.ORDER_GROUPS_OPENED_'))
        ->get();

        //Pertenencia a agrupación de pedidos:
        $in_group = OrderGroupOrders::select('order_groups.id', 'order_groups.name', 'order_groups.status')
        ->join('order_groups', 'order_group_orders.og_id', '=', 'order_groups.id')
        ->where('order_group_orders.order_id', $order->id)
        ->first();

        //Importe facturado:
        $invoiced = $shared->getInvoicedAmount();

        //Facturas:
        $invoices = Invoice::select('id', 'amount', 'amount_with_iva')
        ->where('shared_id', $shared->id)
        ->get();
        if($invoices->count()) $permitted = false;

        //Previsiones de facturación del pedido:      
        $previsions = OrderPrevisions::where('order_id', $order->id)
        ->orderBy('year', 'ASC')
        ->orderBy('month', 'ASC')
        ->get();

        //Productos. Activos y aptos para venta:
        $products = Product::companyProducts($shared->provider_id);

        //Proyectos:
        $projects = Project::select('id', 'code', 'name')
        ->where('company_id', session('currentCompany'))
        ->get();

        //Proyecto vinculado:
        $project_linked = ProjectShared::select('project_id')
        ->where('shared_id', $shared->id)
        ->first();

        //Proyectos vinculados a compras:
        $project_purchases = [];
        if($shared->customer_id == session('currentCompany')){
            $project_purchases = ProjectPurchases::select('project_purchases.id', 'projects.id AS projectId', 'projects.code', 'projects.name')
            ->join('projects', 'project_purchases.project_id', '=', 'projects.id')
            ->where('project_purchases.order_id', $order->id)
            ->get();    
        } 

        //Entregas del pedido:
        $deliveries = DeliveryOrders::select('delivery_orders.id', 'deliveries.id AS deliveryId', 'deliveries.delivery_num', 'deliveries.delivery_old_num')
        ->join('deliveries', 'delivery_orders.delivery_id', '=', 'deliveries.id')
        ->where('delivery_orders.order_id', $order->id)
        ->get();
      
        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Items:
        $items = Item::where('budget_order_id', $shared->id)
        ->orderBy('iva', 'ASC')
        ->orderBy('concept', 'ASC')
        ->get();

        //Autor:
        $author = User::select('name', 'surname')->find($order->created_by);

        //Propietario:
        $owner = User::select('name', 'surname')->find($shared->provider_manager_id);

        //IVA:
        $iva = CompanySettings::co_iva();

        $order_type = $shared->customer_id == session('currentCompany')? 'purchase':'sale';

        //Templates:
        $templates = false;
        if($shared->provider_id == session('currentCompany')){
            $templates = OrderSettings::select('id', 'name', 'featured')
            ->where('company_id', $shared->provider_id)
            ->where('status', 1)
            ->get();
        }

        $languages = $this->languages;

        //Módulos activos:
        $module_accounting = $this->settedModule('accounting');
        $module_budgets = $this->settedModule('budgets');
        $module_logistics = $this->settedModule('logistics');
        $module_products = $this->settedModule('products');
        $module_projects = $this->settedModule('projects');
        $module_rrhh = $this->settedModule('rrhh');
        
        return view('admin.orders.edit')->with(compact('header', 'order', 'tab', 'shared', 'patterns', 'purchase_patterns', 'provider', 'provider_managers', 'provider_business_areas', 'provider_cost_centers', 'provider_workplaces', 'default_workplace', 'customer', 'customer_managers', 'customer_business_areas', 'customer_cost_centers', 'customer_workplaces', 'customer_settings', 'confirmed_by', 'customers', 'budget', 'order_amount', 'order_amount_without_discount', 'order_groups', 'in_group', 'invoiced', 'invoices', 'previsions', 'products', 'projects', 'project_linked', 'project_purchases', 'deliveries', 'currency', 'items', 'author', 'owner', 'iva', 'order_type', 'templates', 'languages', 'permitted', 'module_accounting', 'module_budgets', 'module_products', 'module_projects', 'module_rrhh', 'module_logistics'));
    }

    /**
     * 5. Actualizar pedido.
     */
    public function update(Request $request, Order $order){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/orders/'.$order->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        $shared = BudgetOrderSharedFields::where('order_id', $order->id)->first();
        
        //Número de pedido:
        $order_num = NULL;
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('order', $request->pattern_id);
        }

        //Número de orden de compra:
        $purchase_num = false;
        if($request->purchase_pattern_id){
            $purchasePatternNum = $this->setPatternNumber('purchase', $request->purchase_pattern_id);
        }

        //Slug:
        $slug = Str::slug($request->name);

        //Formateando fechas:
        $estimated_delivery_date = $request->estimated_delivery_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->estimated_delivery_date):$request->estimated_delivery_date:NULL;
        $delivery_date = $request->delivery_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->delivery_date):$request->delivery_date:NULL;

        //Actualizando datos:
        if($request->pattern_id && $shared->provider_id == session('currentCompany')){
            if(!empty($patternNum)){
                $order->order_num = $patternNum['pattern_num'];  
                $order->num_cardinal = $patternNum['num']; 
            }            
            $order->pattern_id = $request->pattern_id;
        }
        if($shared->provider_id == session('currentCompany')){
            $order->order_old_num = $request->order_old_num;
        }
        if($request->purchase_pattern_id && $shared->customer_id == session('currentCompany')){
            if(!empty($purchasePatternNum)){
                $order->purchase_num = $purchasePatternNum['pattern_num'];   
                $order->purchase_num_cardinal = $purchasePatternNum['num'];
            }
            $order->purchase_pattern_id = $request->purchase_pattern_id;
        }
        $order->purchase_old_num = $request->purchase_old_num;
        if($shared->provider_id == session('currentCompany')){
            $order->name = $request->name;
            $order->slug = $slug;
        }
        $order->briefing = $request->briefing;
        $order->observations = $request->observations;
        if($estimated_delivery_date){
            $order->estimated_delivery_date = $estimated_delivery_date;
        }
        if($request->customer_confirmation){
            $order->customer_confirmation = NOW();
            $order->confirmed_by = Auth::user()->id;
        }
        if($delivery_date){
            $order->delivery_date = $delivery_date;
        }
        $order->status = $request->status;

        $order->save();

        //Actualizando registro compartido:
        $shared->customer_manager_id = $request->customer_manager_id;
        $shared->customer_workplace_id = $request->customer_workplace_id;
        $shared->customer_cost_center_id = $request->customer_cost_center_id;
        $shared->customer_business_area_id = $request->customer_business_area_id;
        if($shared->provider_id == session('currentCompany')){
            $shared->provider_manager_id = $request->provider_manager_id;
            $shared->provider_workplace_id = $request->provider_workplace_id;
            $shared->provider_cost_center_id = $request->provider_cost_center_id;
            $shared->provider_business_area_id = $request->provider_business_area_id;
        }
        $shared->save();

        //Guardamos previsiones:
        if($request->year){
            for($i=0; $i<count($request->year); $i++){
                //Validamos datos:
                if(is_numeric($request->year[$i])){
                    //Actualizar o...
                    if(isset($request->op_id[$i])){
                        $op = OrderPrevisions::find($request->op_id[$i]);
                        //Actualizamos si no se encuentra facturado:
                        if(!$op->invoice_id){
                            $op->year = $request->year[$i];
                            $op->month = $request->month[$i]? $request->month[$i]:NULL;
                            $op->percent = $request->percent[$i];
                            $op->save();
                        }

                    //...insertar
                    }else{
                        $op = new OrderPrevisions();
                        $op->order_id = $order->id;
                        $op->year = $request->year[$i];
                        $op->month = $request->month[$i]? $request->month[$i]:NULL;
                        $op->percent = $request->percent[$i];
                        $op->save();
                    }
                }
            }
        }

        $msg = trans('textos.pedido_updated');
        return redirect()->route('orders.edit', [$order->id, false])->with(compact('msg')); 
    }

    /**
     * 6. Eliminar pedido.
     *
     * @param  str $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order){
        //Actualizamos registro compartido:
        BudgetOrderSharedFields::where('order_id', $order->id)
        ->update(['order_id' => NULL]);

        $order->delete();

        $msg = trans('textos.pedido_deleted');
        return redirect()->route('orders.index')->with(compact('msg'));    
    }

    /**
     * 7. Resetear numeración del pedido.
     */
    public function resetNum(Order $order){
        $order->order_num = NULL;
        $order->num_cardinal = NULL;
        $order->pattern_id = NULL;
        $order->save();

        $msg = trans('textos.numero_deleted');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 8. Obtener equipo de pedido.
     */
    public function getTeamDT($shared = false, $environment = false){
        if($environment == 'customer'){
            $data = BudgetOrderSharedFields::select('customer_id AS company_id', 'customer_manager_id as manager_id', 'customer_team as team')->find($shared);    
        }else{
            $data = BudgetOrderSharedFields::select('provider_id AS company_id', 'provider_manager_id as manager_id', 'provider_team as team')->find($shared);     
        }
        
        $arr = [];
        //Añadir Project Manager:
        if($data->manager_id) array_push($arr, $data->manager_id);

        //Añadir resto de equipo:
        if($data->team){
            $members = unserialize($data->team);
        }

        //Fusionando arrays:
        if(!empty($members)){
            $arr = array_merge($arr, $members);
        }

        $users = User::select('id', 'name', 'surname', DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image'))
        ->whereIn('id', $arr)
        ->orderBy('name', 'ASC')
        ->get();

        //Permisos:
        $customers_on = Auth::user()->can('orders.edit') && $data->company_id == session('currentCompany')? true:false;
        $providers_on = Auth::user()->can('orders.edit') && $data->company_id == session('currentCompany')? true:false;
        $users_show = Auth::user()->can('users.show');

        //Ampliación de los objetos:
        foreach($users as $user){
            //Manager:
            $user->rol = $data->manager_id && $user->id == $data->manager_id? 'Project Manager':'';  

            //Permisos para las acciones:
            $user->customers_on = $customers_on;
            $user->providers_on = $providers_on;
            $user->users_show = $users_show;  
        }

        return datatables($users)->toJson();
    }

    /**
     * 9. Obtener líneas de pedido.
     */
    public function getItemsDT(BudgetOrderSharedFields $shared){
        $items = Item::where('budget_order_id', $shared->id)->get();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Permisos del usuario:
        $editar = Auth::user()->can('orders.edit');

        //Estados de línea:
        $item_status = Helpers::orderLineStatus();

        //Ampliación de los objetos:
        foreach($items as $row){
            //Moneda:
            $row->currency = $currency->symbol;

            //Estado:
            $row->estado = $row->status? $item_status[$row->status]:'';

            //Permisos para las acciones:
            $row->editar = $editar;

            //Importe:
            $row->amount = Helpers::itemAmount($row, true);
        }

        return datatables($items)->toJson();
    }

    /**
     * 10. Actualizar estado de pedido.
     */
    public function status(Request $request, Order $order, $status){
        if($request->ajax()){    
            $order->status = $status;
            $order->save();

            return response()->json(true);
        }
    }

    /**
     * 11. Panel control de ventas.
     */
    public function controlPanelSales($year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.panel_control_ventas',
            'module' => $this->module,
            'slug' => 'control-panel'
        ]; 

        $year = !$year? Date('Y'):$year;

        $min_year = config('constants.MIN_YEAR_');
        $max_year = date('Y') + 1;

        $order_type = 'ventas';

        return view('admin.orders.control-panel')->with(compact('header', 'year', 'min_year', 'max_year', 'order_type'));
    }

    /**
     * 11.1. Filtrar año para panel de control de ventas.
     */
    public function controlPanelSalesPost(Request $request){
        return redirect()->route('orders.control-panel-sales', $request->year);
    }

    /**
     * 12. Panel control de compras.
     */
    public function controlPanelPurchases($year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.panel_control_compras',
            'module' => $this->module,
            'slug' => 'control-panel'
        ]; 

        $year = !$year? Date('Y'):$year;

        $min_year = config('constants.MIN_YEAR_');
        $max_year = date('Y') + 1;

        $order_type = 'compras';

        return view('admin.orders.control-panel')->with(compact('header', 'year', 'min_year', 'max_year', 'order_type'));
    }

    /**
     * 13. Vincular compra a proyecto.
     */
    public function addPurchaseToProject(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-purchase-to-project');

            return redirect('admin/orders/'.$request->order_id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //Comprobamos que no esté vinculado previamente:
        $exist = ProjectPurchases::select('id')
        ->where('project_id', $request->project_id)
        ->where('order_id', $request->order_id)
        ->first();

        if($exist){
            $alert = trans('textos.compra_ya_vinculada_proyecto', [$request->project_id]);
            return redirect()->route('orders.edit', $request->order_id)->with(compact('alert'));
            exit;
        
        }else{
            $pp = new ProjectPurchases();
            $pp->project_id = $request->project_id;
            $pp->order_id = $request->order_id;
            $pp->save();

            $msg = trans('textos.compra_vinculada');
            return redirect()->route('orders.edit', [$request->order_id, 'projects'])->with(compact('msg'));    
        }
    }

    /**
     * 14. Desvincular compra a proyecto.
     */
    public function destroyPurchaseToProject(Request $request, ProjectPurchases $purchase){
        //Comprobamos que compra y proyecto pertenezcan a la empresa en sesión:
        $project = Project::select('company_id')
        ->where('id', $purchase->project_id)
        ->where('company_id', session('currentCompany'))
        ->first();

        $purchase_ = BudgetOrderSharedFields::select('id')
        ->where('order_id', $purchase->order_id)
        ->where('customer_id', session('currentCompany'))
        ->first();

        if(!$project || !$purchase_){
            $alert = trans('textos.compra_proyecto_no_valida');
            return redirect()->route('orders.index')->with(compact('alert'));    
        }else{
            $order_id = $purchase->order_id;
            $project_id = $purchase->project_id;

            $purchase->delete();

            $msg = trans('textos.compra_desvinculada');
            if($request->return == 'order'){
                return redirect()->route('orders.edit', [$order_id, 'projects'])->with(compact('msg'));
            }else{
                return redirect()->route('projects.edit', [$project_id, 'purchases'])->with(compact('msg'));
            }
        }
    }

    /**
     * 15. Activar entrega de pedido.
     */
    public function setOrderDelivery(Order $order){
        $order->prepare_delivery = $order->prepare_delivery == '0'? 1:'0';
        $order->save();

        //09/04/22: aquí se debería incluir la inicialización de picking para pedidos lo requieran y si el módulo respectivo se encuentra activo.

        $msg = trans('textos.pedido_entrega_activada');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 16. Actualizar plantilla de pedido.
     */
    public function updateTemplate(Request $request, Order $order){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/orders/'.$order->id.'/edit/template')
                ->withErrors($validator)
                ->withInput();
        }

        //Si viene declarada la plantilla:
        if($request->template_id){
            //Plantilla original:
            if($order->template_id == $request->template_id){
                $template = [
                    'conditions' => $request->conditions, 
                    'payment_method' => $request->payment_method,
                    'observations' => $request->observations,
                    'footer_legend' => $request->footer_legend
                ];
            }else{
                //Plantilla original:
                $orig_template = OrderSettings::find($request->template_id);

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

        $order->template_id = $request->template_id;
        $order->template = $template;
        $order->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('orders.edit', [$order->id, 'template'])->with(compact('msg'));
    } 

    /**
     * 17. Eliminar plantilla del pedido.
     */
    public function removeTemplate(Order $order){
        $order->template_id = NULL;
        $order->template = NULL;
        $order->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('orders.edit', [$order->id, 'template'])->with(compact('msg'));
    }
}
