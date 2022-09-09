<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use App\Notifications\PurchaseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use File;
use PDF;
use Carbon\Carbon;

//Models:
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
use App\Models\OrderPatterns;
use App\Models\OrderPrevisions;
use App\Models\OrderSettings;
use App\Models\Phone;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectOrders;
use App\Models\ProjectPurchases;
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
     * 11.1. Filtrar año para panel de control de ventas.
     * 11.2. Listado de pedidos del panel de control de ventas para DataTable.
     * 12. Panel control de compras.
     * 12.1. Filtrar año para panel de control de compras.
     * 12.2. Listado de pedidos del panel de control de compras para DataTable.
     * 13. Vincular compra a proyecto.
     * 14. Desvincular compra a proyecto.
     * 15. Activar entrega de pedido.
     * 16. Actualizar plantilla de pedido.
     * 17. Eliminar plantilla del pedido.
     * 18. Añadir descuento a pedido.
     * 18.1. Eliminar descuento de pedido.
     * 19. Modificar cliente de pedido.
     * 20. Eliminar documento de orden de compra.
     * 21. Imprimir pedido.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:orders');
        $this->middleware('order_get', ['only' => ['orderFromBudget', 'edit', 'update', 'destroy', 'print', 'setOrderDelivery', 'resetNum', 'removePurchaseDoc']]);
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
                'discount_percent' => 'required_without:discount_amount|numeric|max:100',
                'discount_amount' => 'nullable|numeric'
            ],[
                'discount_percent.required_without' => trans('textos.obligatorio_sin_valor_neto'),
                'discount_percent.numeric' => trans('textos.valor_numerico')
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

        //Validación modificar cliente:
        }elseif(isset($data['submit_set_customer'])){
            return Validator::make($data, [
                'new_customer' => 'required|integer'
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
        $arrStatus = helpers::orderStatus();

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

        //Obtenemos pedidos simples, no agrupaciones:
        $data = Order::select('orders.id', 'orders.is_budget', 'orders.budget_num', 'orders.discount', 'orders.order_parent', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'orders.estimated_delivery_date', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.delivery_date', 'orders.order_status', 'orders.invoiced', 'orders.created_at', 'business_areas.name AS businessArea', 'cost_centers.name AS costCenter')
        ->leftJoin('business_areas', 'orders.customer_business_area_id', '=', 'business_areas.id')
        ->leftJoin('cost_centers', 'orders.customer_cost_center_id', '=', 'cost_centers.id')
        //->leftJoin('workplaces', 'orders.workplace_id', '=', 'workplaces.id')
        ->where('orders.is_order', true)
        ->where('orders.is_group', false)
        ->when($owner, function($q) use($owner){
            return $q->where('orders.owner', $owner);
        })
        ->when($customer && is_numeric($customer), function($q) use($customer){
            return $q->where('orders.customer_id', $customer)
            ->orWhere('orders.final_customer_id', $customer);
        })
        ->when($provider && is_numeric($provider), function($q) use($provider){
            return $q->where('orders.provider_id', $provider);
        })
        ->orderBy('orders.created_at', 'DESC');
        //->get();
        //Filtro status:
        /*$query->when($status && $status != '', function($q) use($status){
            return $q->where('orders.status', $status);
        });*/

        $estados = helpers::orderStatus();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('num', function($row){
            return view('admin.orders.partials.dt-num', compact('row')); 
        })
        ->addColumn('budget_num', function($row){
            return view('admin.orders.partials.dt-budget-num', compact('row')); 
        })
        ->addColumn('order_parent', function($row){
            if($row->order_parent){
                return '<a target="_blank" href="/admin/orders/'.$row->order_parent.'/edit">'.$row->order_parent.'</a>';
            }else{
                return '';
            }
        })
        ->addColumn('purchase_num', function($row){
            $purchase_number = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');
            return $purchase_number;
        })
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('amount', function($row) use($currency){
            $amount = $row->getAmount();    
            return $amount['amount_with_discount'].$currency->symbol;
        })
        ->addColumn('fechaEntregaPrevista', function($row){
            return Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]);   
        })
        ->addColumn('cost_center', function($row){
            return $row->costCenter;    
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.orders.partials.dt-status', compact('row', 'estados'));    
        })
        ->addColumn('acciones', function($row) use($estados){
            $manage_status = true;
            return view('admin.orders.partials.dt-actions', compact('row', 'estados', 'manage_status'));    
        })
        ->rawColumns(['order_parent'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo pedido.
     */
    public function create($type, $company_id = false){
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

        return view('admin.orders.create')->with(compact('header', 'type', 'company_id', 'companies', 'module_products'));    
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

        //Agrupación de pedidos o pedido simple:
        $is_group = $request->is_group? true:false;

        //Importe bolsa:
        $bag_amount = $request->bag_amount? $request->bag_amount:'0';

        $customer_id = $request->type == 'sale'? $request->company_id:session('currentCompany');
        $provider_id = $request->type == 'purchase'? $request->company_id:session('currentCompany');

        //Slug:
        $slug = $request->name? Str::slug($request->name):NULL;

        //Creamos pedido:
        $order = new Order();
        $order->is_order = true;
        $order->name = $request->name;
        $order->slug = $slug;
        $order->is_group = $is_group;
        if($is_group){
            $order->order_status = config('constants.ORDER_GROUPS_DEFAULT_');
            $order->bag_amount = $bag_amount;
        }
        $order->created_by = Auth::user()->id;
        $order->customer_id = $customer_id;
        if($request->type == 'purchase'){
            $order->customer_manager_id = Auth::user()->id;
        }
        $order->provider_id = $provider_id;
        if($request->type == 'sale'){
            $order->provider_manager_id = Auth::user()->id;
        }
        $order->save();

        //Guardamos previsión de facturación por defecto:
        OrderPrevisions::store($order->id);

        //Descuento por defecto:
        $order->setDefaultDiscount();

        //Si se trata de un pedido de compra, se envía notificación al proveedor:
        if($order->customer_id == session('currentCompany')){
            $buyer = '';
            foreach(session('companies') as $co){
                if($co->id == session('currentCompany')){
                    $buyer = $co->name;
                    break;
                }
            }

            //Destinatarios de la notificación:
            if($request->receivers){
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
        }

        $msg = trans('textos.pedido_creado');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 3.1. Generar pedido desde presupuesto.
     */
    public function orderFromBudget(Order $order){
        //Lo marcamos como pedido:
        $order->is_order = true;
        $order->save();

        //Guardamos previsión de facturación por defecto:
        OrderPrevisions::store($order->id);

        $msg = trans('textos.pedido_creado');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 4. Editar pedido.
     */
    public function edit(Order $order, $tab = false){
        if($order->is_group){
            $suboption = 'agrupacion_pedidos_editar';
            $slug = 'order-groups';
        }else{
            $suboption =  $order->customer_id == session('currentCompany')? 'compra_editar':'venta_editar';
            $slug = 'orders';    
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.'.$suboption,
            'module' => $this->module,
            'slug' => $slug
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
            $purchase_patterns = PurchasePatterns::where('company_id', $order->customer_id)
            ->pluck('name', 'id')
            ->toArray();
        }

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        $provider_managers = Employee::select(
            DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
        ->join('users', 'employees.user_id', '=', 'users.id')
        ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        ->where('users.status', 1)
        ->where('users.isAdmin', 1)
        ->where('employees.status', 1)
        ->where('contracts.company_id', $order->provider_id)
        ->orderBy('users.name')
        ->pluck('user', 'users.id')
        ->toArray();

        $provider_business_areas = BusinessAreas::where('company_id', $order->provider_id)
        ->pluck('name', 'id')
        ->toArray();

        $provider_cost_centers = CostCenters::where('company_id', $order->provider_id)
        ->pluck('name', 'id')
        ->toArray();

        $provider_workplaces = Workplace::where('company_id', $order->provider_id)
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
        $customer = Company::select('id', 'name', 'tradename')->find($order->customer_id);

        //31/05/22: duda sobre si obtener sólo empleados o usuarios del cliente. Por el momento modifico la query para obtener usuarios.
        // $customer_managers = Employee::select(
        //     DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
        // ->join('users', 'employees.user_id', '=', 'users.id')
        // ->join('contracts', 'employees.contract_id', '=', 'contracts.id')
        // ->where('users.status', 1)
        // ->where('users.isAdmin', 1)
        // ->where('employees.status', 1)
        // ->where('contracts.company_id', $order->customer_id)
        // ->orderBy('users.name')
        // ->pluck('user', 'users.id')
        // ->toArray();
        
        $customer_managers_ = UserCompanies::getCompanyUsers($order->customer_id, 1); 
        $customer_managers = [];
        foreach($customer_managers_ as $cm){
            $customer_managers[$cm->id] = $cm->name.' '.$cm->surname;
        }

        $customer_business_areas = BusinessAreas::where('company_id', $order->customer_id)
        ->pluck('name', 'id')
        ->toArray();

        $customer_cost_centers = CostCenters::where('company_id', $order->customer_id)
        ->pluck('name', 'id')
        ->toArray();

        $customer_workplaces = Workplace::where('company_id', $order->customer_id)
        ->pluck('name', 'id')
        ->toArray();

        //Configuraciones de cliente. Es relación obligatoria, de modo que en caso de no existir debe crearse.
        $customer_settings = CustomerProviders::where('customer_id', $order->customer_id)
        ->where('provider_id', $order->provider_id)
        ->first();
        if(!$customer_settings){
            $customer_settings = new CustomerProviders();  
            $customer_settings->customer_id = $order->customer_id;
            $customer_settings->provider_id = $order->provider_id;
            $customer_settings->status = 1;
            $customer_settings->save();
        }

        //Confirmación pedido:
        $confirmed_by = false;
        if($order->customer_confirmation){
            $confirmed_by = User::select('name', 'surname')->find($order->confirmed_by);    
        }

        //Clientes:
        $customers = Company::where('status', 1)
        ->where('id', '!=', $order->provider_id)
        ->where('id', '!=', $order->customer_id)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Importe del pedido:
        if($order->free_amount){
            $order_amount = $order->free_amount;
            $order_amount_without_discount = false;
        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['amount_with_discount'];
            $order_amount_without_discount = $arr_amounts['amount_without_discount'];
        } 

        //Agrupaciones de pedidos:
        $order_groups = Order::select('orders.id', 'orders.order_num', 'orders.name')
        ->where('orders.provider_id', session('currentCompany'))
        ->where('orders.customer_id', $order->customer_id)
        ->where('orders.order_status', config('constants.ORDER_GROUPS_OPENED_'))
        ->where('orders.is_group', 1)
        ->get();

        //Pedidos de las agrupación:
        $ordersInGroup = [];
        $orders_off = false;
        $group_amount = false;
        if($order->is_group){
            $ordersInGroup = $order->getGroupOrders();

            foreach($ordersInGroup as $oig){
                //Calculando importe de la agrupación:
                $orderAmount = $oig->getAmount();
                $group_amount = $group_amount + $orderAmount['amount_with_discount'];
            }

            //Pedidos fuera de agrupaciones:
            $orders_off = OrderPrevisions::select('orders.order_num', 'orders.order_old_num', 'orders.name', 'orders.delivery_date', 'order_previsions.id', 'order_previsions.order_id', 'order_previsions.year', 'order_previsions.month', 'order_previsions.percent', 'invoices.id AS invoiceId', 'invoices.invoice_num')
            ->join('orders', 'order_previsions.order_id', '=', 'orders.id')
            ->leftJoin('invoice_orders', 'orders.id', '=', 'invoice_orders.order_id')
            ->leftJoin('invoices', 'invoice_orders.invoice_id', '=', 'invoices.id')
            ->where('orders.customer_id', $order->customer_id)
            ->where('orders.provider_id', session('currentCompany'))
            ->where('orders.is_group', false)
            ->where('orders.invoiced', false)
            ->whereNull('orders.order_parent')
            ->whereNull('order_previsions.invoice_id')
            ->whereNull('orders.deleted_at')
            ->orderBy('orders.order_num', 'ASC')
            ->get();
        }

        //Importe facturado:
        $invoiced = $order->getInvoicedAmount();

        //Facturas por agrupación:
        if($order->parent){
            $invoices = Invoice::select('invoices.id', 'invoices.invoice_num', 'invoices.amount', 'invoices.amount_with_iva')
            ->join('invoice_orders', 'invoices.id', '=', 'invoice_orders.invoice_id')
            ->where('invoice_orders.order_id', $order->id)
            ->get();

        //Facturas pedido simple:
        }else{
            $invoices = Invoice::select('id', 'invoice_num', 'amount', 'amount_with_iva')
            ->where('order_id', $order->id)
            ->get();
        }
        if($invoices->count()) $permitted = false;

        //Previsiones de facturación del pedido:      
        $previsions = OrderPrevisions::select('order_previsions.*', 'invoices.id AS invoiceId', 'invoices.invoice_num')
        ->leftJoin('invoices', 'order_previsions.invoice_id', '=', 'invoices.id')
        ->where('order_previsions.order_id', $order->id)
        ->orderBy('order_previsions.year', 'ASC')
        ->orderBy('order_previsions.month', 'ASC')
        ->get();

        //Productos. Activos y aptos para venta:
        $products = Product::companyProducts($order->provider_id, $order->customer_id);

        //Proyectos:
        $projects = Project::select('id', 'code', 'name')
        ->where('company_id', session('currentCompany'))
        ->get();

        //Proyecto vinculado a ventas:
        $project = ProjectOrders::select('projects.id', 'projects.code', 'projects.contest_code', 'projects.name')
        ->join('projects', 'project_orders.project_id', '=', 'projects.id')
        ->where('project_orders.order_id', $order->id)
        ->where('projects.company_id', session('currentCompany'))
        ->first();

        //Proyectos vinculados a compras:
        $project_purchases = [];
        if($order->customer_id == session('currentCompany')){
            $project_purchases = ProjectPurchases::select('project_purchases.id', 'projects.id AS projectId', 'projects.code', 'projects.name')
            ->join('projects', 'project_purchases.project_id', '=', 'projects.id')
            ->where('project_purchases.order_id', $order->id)
            ->where('projects.company_id', session('currentCompany'))
            ->get();    
        } 

        //Entregas del pedido:
        $deliveries = DeliveryOrders::select('delivery_orders.id', 'deliveries.id AS deliveryId', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.origin', 'deliveries.reception_date', 'deliveries.customer_observations', 'deliveries.provider_observations')
        ->join('deliveries', 'delivery_orders.delivery_id', '=', 'deliveries.id')
        ->where('delivery_orders.order_id', $order->id)
        ->get();

        //Recepciones previstas, a partir de las entregas programadas:
        $receivings = [];
        if($order->customer_id == session('currentCompany')){
            foreach($deliveries as $dlv){
                array_push($receivings, $dlv);
            }
        }
      
        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Items:
        $items = Item::where('order_id', $order->id)
        ->orderBy('iva', 'ASC')
        ->orderBy('concept', 'ASC')
        ->get();

        //Autor:
        $author = User::select('name', 'surname')->find($order->created_by);

        //Propietario:
        $owner = User::select('name', 'surname')->find($order->provider_manager_id);

        //IVA:
        $iva = CompanySettings::co_iva();

        $order_type = $order->customer_id == session('currentCompany')? 'purchase':'sale';

        //Templates:
        $templates = false;
        if($order->provider_id == session('currentCompany')){
            $templates = OrderSettings::select('id', 'name', 'featured')
            ->where('company_id', $order->provider_id)
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
        
        return view('admin.orders.edit')->with(compact('header', 'order', 'tab', 'patterns', 'purchase_patterns', 'provider', 'provider_managers', 'provider_business_areas', 'provider_cost_centers', 'provider_workplaces', 'default_workplace', 'customer', 'customer_managers', 'customer_business_areas', 'customer_cost_centers', 'customer_workplaces', 'customer_settings', 'confirmed_by', 'customers', 'order_amount', 'order_amount_without_discount', 'order_groups', 'ordersInGroup', 'orders_off', 'group_amount', 'invoiced', 'invoices', 'previsions', 'products', 'projects', 'project', 'project_purchases', 'deliveries', 'receivings', 'currency', 'items', 'author', 'owner', 'iva', 'order_type', 'templates', 'languages', 'permitted', 'module_accounting', 'module_budgets', 'module_products', 'module_projects', 'module_rrhh', 'module_logistics'));
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
        if($request->pattern_id && $order->provider_id == session('currentCompany')){
            if(!empty($patternNum)){
                $order->order_num = $patternNum['pattern_num'];  
                $order->order_num_cardinal = $patternNum['num']; 
            }            
            $order->order_pattern_id = $request->pattern_id;
        }
        if($order->provider_id == session('currentCompany')){
            $order->order_old_num = $request->order_old_num;
        }
        if($request->purchase_pattern_id && $order->customer_id == session('currentCompany')){
            if(!empty($purchasePatternNum)){
                $order->purchase_num = $purchasePatternNum['pattern_num'];   
                $order->purchase_num_cardinal = $purchasePatternNum['num'];
            }
            $order->purchase_pattern_id = $request->purchase_pattern_id;
        }
        $order->purchase_old_num = $request->purchase_old_num;
        if($purchase_doc){
            $order->purchase_doc = $purchase_doc;
        }
        if($order->provider_id == session('currentCompany')){
            $order->name = $request->name;
            $order->slug = $slug;
        }
        if($order->provider_id == session('currentCompany')){
            $order->briefing = $request->briefing;
            $order->observations = $request->observations;
        }
        if($order->customer_id == session('currentCompany')){
            $order->observations_customer = $request->observations_customer;
        }
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
        //La fecha de orden de pago recibida sólo se actualiza cuando se recibe por primera vez la orden o es cambiada.
        if($request->pay_order && $order->pay_order != $request->pay_order){
            $order->pay_order_received = NOW();
        }
        $order->pay_order = $request->pay_order;
        if($order->is_group){
            $order->bag_amount = $request->bag_amount;
        }
        if($request->status){
            $order->order_status = $request->status;
        }

        $order->save();

        //Actualizando registro compartido:
        $order->customer_manager_id = $request->customer_manager_id;
        $order->customer_workplace_id = $request->customer_workplace_id;
        $order->customer_cost_center_id = $request->customer_cost_center_id;
        $order->customer_business_area_id = $request->customer_business_area_id;
        if($order->provider_id == session('currentCompany')){
            $order->provider_manager_id = $request->provider_manager_id;
            $order->provider_workplace_id = $request->provider_workplace_id;
            $order->provider_cost_center_id = $request->provider_cost_center_id;
            $order->provider_business_area_id = $request->provider_business_area_id;
        }
        $order->save();

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
        /**
         * Comprobamos que el estado permita la eliminación del pedido - condiciones:
         * 1. El pedido no puede estar facturado.
         * 2. Debe estar en un estado que permita su eliminación.
         **/
        if($order->invoiced 
            || ($order->is_group && !in_array($order->status, config('constants.ORDER_GROUPS_OPENED_'))) 
            || (!$order->is_group && in_array($order->status, config('constants.ORDER_STATUS_NOT_DELETED_')))
        ){
            $alert = trans('textos.pedido_no_eliminable');
            if($order->is_group){
                return redirect()->route('order-groups.index')->with(compact('alert'));
            }else{
                return redirect()->route('orders.index')->with(compact('alert'));
            }
            exit;
        }

        //Si se trata de agrupación, desvinculamos los pedidos asociados:
        if($order->is_group){
            Order::where('parent', $order->id)
            ->update(['parent' => NULL, 'bag_amount' => 0]);
        }

        //Si está declarado como presupuesto, sólo quitamos condición de pedido. En caso contrario, se elimina por completo.
        if($order->is_budget){
            $order->is_order = false;
            $order->save();
        }else{
            $order->delete();
        }

        $msg = trans('textos.pedido_deleted');
        return redirect()->route('orders.index')->with(compact('msg'));    
    }

    /**
     * 7. Resetear numeración del pedido.
     */
    public function resetNum(Order $order){
        $order->order_num = NULL;
        $order->order_num_cardinal = NULL;
        $order->order_pattern_id = NULL;
        $order->save();

        $msg = trans('textos.numero_deleted');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 8. Obtener equipo de pedido.
     */
    public function getTeamDT($order = false, $environment = false){
        if($environment == 'customer'){
            $data = Order::select('customer_id AS company_id', 'customer_manager_id as manager_id', 'customer_team as team')->find($order);    
        }else{
            $data = Order::select('provider_id AS company_id', 'provider_manager_id as manager_id', 'provider_team as team')->find($order);     
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
    public function getItemsDT(Order $order, $environment){
        $items = Item::where('order_id', $order->id);

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Permisos del usuario:
        $editar = Auth::user()->can('orders.edit');

        //Estados de línea:
        $estados = helpers::orderLineStatus();

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
            if($item->price > 0){
                $amount = helpers::itemAmount($item); 
                return $amount.$currency->symbol;

            }elseif(!$item->price && $item->calculated_by_percent > 0){
                $amount = helpers::itemAmountPercentage($item->calculated_by_percent, $order_amount['amount_with_discount']);
                return $amount.$currency->symbol;

            }else{
                return trans('textos.precio_sin_definir');
            }              
        })
        ->addColumn('estado', function($item) use($estados){
            return view('admin.orders.partials.dt-item-status', compact('item', 'estados'));    
        })
        ->addColumn('observations', function($item){
            return $item->observations;
        })
        ->addColumn('acciones', function($item) use($estados, $order, $environment){
            return view('admin.orders.partials.dt-item-actions', compact('item', 'estados', 'order', 'environment'));    
        })
        ->rawColumns(['observations'])
        ->make(true);
    }

    /**
     * 10. Actualizar estado de pedido.
     */
    public function status(Request $request, Order $order, $status){
        if($request->ajax()){    
            $order->order_status = $status;
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

        $currency = $this->co_currency;

        return view('admin.orders.control-panel')->with(compact('header', 'year', 'min_year', 'max_year', 'order_type', 'currency'));
    }

    /**
     * 11.1. Filtrar año para panel de control de ventas.
     */
    public function controlPanelSalesPost(Request $request){
        return redirect()->route('orders.control-panel-sales', $request->year);
    }

    /**
     * 11.2. Listado de pedidos del panel de control de ventas para DataTable.
     */
    public function getOrdersControlPanelSalesDT($year){
        //Estados de pedido que quedan fuera del panel: pedidos cancelados,...
        $statusRejected = config('constants.ORDER_STATUS_NOT_INVOICED_');

        $estados = helpers::orderStatus();

        $currency = $this->co_currency;

        $data = Order::select('orders.id', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.estimated_delivery_date', 'orders.delivery_date', 'orders.order_status', 'orders.invoiced', 'orders.discount', 'order_previsions.year', 'order_previsions.month', 'order_previsions.percent', 'orders.created_at', 'companies.name AS Customer', 'users.name AS Nombre', 'users.surname', 'cc.name AS finalCustomer', 'u_.name AS CustomerManagerName', 'u_.surname AS CustomerManagerSurname', 'business_areas.name AS businessArea')
        ->join('order_previsions', 'orders.id', '=', 'order_previsions.order_id')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->leftJoin('companies AS cc', 'orders.final_customer_id', '=', 'cc.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->leftJoin('users AS u_', 'orders.customer_manager_id', '=', 'u_.id')
        ->leftJoin('business_areas', 'orders.customer_business_area_id', '=', 'business_areas.id')
        ->where('orders.provider_id', session('currentCompany'))
        ->where('orders.is_order', 1)
        ->where('orders.invoiced', 0)
        ->whereNull('orders.order_parent')
        ->whereNull('order_previsions.invoice_id')
        ->whereNull('order_previsions.deleted_at')
        ->where('order_previsions.year', $year)
        ->whereNotIn('orders.order_status', $statusRejected)
        ->orderBy('orders.id', 'DESC')
        ->orderBy('orders.created_at', 'DESC');

        return DataTables::of($data)
        ->addColumn('fecha', function($row){
            $month = $row->month? helpers::yearMonths($row->month):'';
            return '<span style="display: none">'.strtotime($row->year.'-'.$row->month.'-01').'</span>'.$month.' '.$row->year;
        })
        ->filterColumn('fecha', function($query, $keyword){
            $sql = "order_previsions.year like ?";
            $query->whereRaw($sql, ["%$keyword%"]);
        })
        ->addColumn('customer', function($row){
            return $row->Customer;
        })
        ->addColumn('business_area', function($row){
            return $row->businessArea;
        })
        ->addColumn('order_num', function($row){
            $order_num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
            return '<a target="_blank" href="'.route('orders.edit', $row->id).'">'.$order_num.'</a>';
        })   
        ->addColumn('purchase_num', function($row){
            $purchase_num = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');
            return $purchase_num;
        })
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('Nombre', function($row){
            return $row->Nombre;
        })
        ->addColumn('finalCustomer', function($row){
            return $row->finalCustomer;
        })
        ->addColumn('amount', function($row){
            $amount = $row->getAmount();
            return number_format($amount['amount_with_discount'], 2, '.', '');
        })  
        ->addColumn('a_facturar', function($row){ 
            $amount = $row->getAmount();
            if($row->percent && $row->percent < 100){
                $a_facturar = helpers::percent($amount['amount_with_discount'], $row->percent);  
                return number_format($a_facturar, 2, '.', '');
            }else{
                return number_format($amount['amount_with_discount'], 2, '.', '');
            }    
        })
        ->addColumn('invoice_amount', function($row){ 
            return Invoice::amountByOrder($row->id);
        })
        ->addColumn('acciones', function($row) use($estados){
            $manage_status = false;
            return view('admin.orders.partials.dt-actions', compact('row', 'estados', 'manage_status'));    
        })
        ->rawColumns(['fecha', 'order_num'])
        ->orderColumn('fecha', 'customer')
        ->make(true);
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

        $currency = $this->co_currency;

        return view('admin.orders.control-panel')->with(compact('header', 'year', 'min_year', 'max_year', 'order_type', 'currency'));
    }

    /**
     * 12.1. Filtrar año para panel de control de compras.
     */
    public function controlPanelPurchasesPost(Request $request){
        return redirect()->route('orders.control-panel-purchases', $request->year);
    }

    /**
     * 12.2. Listado de pedidos del panel de control de compras para DataTable.
     */
    public function getOrdersControlPanelPurchasesDT($year){
        //Estados de pedido que quedan fuera del panel: pedidos cancelados,...
        $statusRejected = config('constants.ORDER_STATUS_NOT_INVOICED_');

        $estados = helpers::orderStatus();

        $currency = $this->co_currency;

        $data = Order::select('orders.id', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.estimated_delivery_date', 'orders.delivery_date', 'orders.order_status', 'orders.invoiced', 'orders.discount', 'order_previsions.year', 'order_previsions.month', 'order_previsions.percent', 'orders.created_at', 'companies.name AS Provider', 'users.name AS Nombre', 'users.surname', 'cc.name AS finalCustomer', 'u_.name AS CustomerManagerName', 'u_.surname AS CustomerManagerSurname', 'business_areas.name AS businessArea')
        ->join('order_previsions', 'orders.id', '=', 'order_previsions.order_id')
        ->join('companies', 'orders.provider_id', '=', 'companies.id')
        ->leftJoin('companies AS cc', 'orders.final_customer_id', '=', 'cc.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->leftJoin('users AS u_', 'orders.customer_manager_id', '=', 'u_.id')
        ->leftJoin('business_areas', 'orders.customer_business_area_id', '=', 'business_areas.id')
        ->where('orders.customer_id', session('currentCompany'))
        ->where('orders.is_order', 1)
        ->where('orders.invoiced', 0)
        ->whereNull('orders.order_parent')
        ->whereNull('order_previsions.invoice_id')
        ->whereNull('order_previsions.deleted_at')
        ->where('order_previsions.year', $year)
        ->whereNotIn('orders.order_status', $statusRejected)
        ->orderBy('orders.id', 'DESC')
        ->orderBy('orders.created_at', 'DESC');

        return DataTables::of($data)
        ->addColumn('fecha', function($row){
            $month = $row->month? helpers::yearMonths($row->month):'';
            return '<span style="display: none">'.strtotime($row->year.'-'.$row->month.'-01').'</span>'.$month.' '.$row->year;
        })
        ->filterColumn('fecha', function($query, $keyword){
            $sql = "order_previsions.year like ?";
            $query->whereRaw($sql, ["%$keyword%"]);
        })
        ->addColumn('provider', function($row){
            return $row->Provider;
        })
        ->addColumn('business_area', function($row){
            return $row->businessArea;
        })
        ->addColumn('order_num', function($row){
            $order_num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
            return '<a target="_blank" href="'.route('orders.edit', $row->id).'">'.$order_num.'</a>';
        })   
        ->addColumn('purchase_num', function($row){
            $purchase_num = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');
            return $purchase_num;
        })
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('Nombre', function($row){
            return $row->Nombre;
        })
        ->addColumn('finalCustomer', function($row){
            return $row->finalCustomer;
        })
        ->addColumn('amount', function($row){
            $amount = $row->getAmount();
            return number_format($amount['amount_with_discount'], 2, '.', '');
        })  
        ->addColumn('a_facturar', function($row){ 
            $amount = $row->getAmount();
            if($row->percent && $row->percent < 100){
                $a_facturar = helpers::percent($amount['amount_with_discount'], $row->percent);  
                return number_format($a_facturar, 2, '.', '');
            }else{
                return number_format($amount['amount_with_discount'], 2, '.', '');
            }    
        })
        ->addColumn('invoice_amount', function($row){ 
            return Invoice::amountByOrder($row->id);
        })
        ->addColumn('acciones', function($row) use($estados){
            $manage_status = false;
            return view('admin.orders.partials.dt-actions', compact('row', 'estados', 'manage_status'));    
        })
        ->rawColumns(['fecha', 'order_num'])
        ->orderColumn('fecha', 'customer')
        ->make(true);
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

        $purchase_ = Order::select('id')
        ->where('id', $purchase->order_id)
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
//dd($request->all(), $order);
        //Si viene declarada la plantilla:
        if($request->template_id){
            //Plantilla original:
            if($order->order_template_id == $request->template_id){
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

        $order->order_template_id = $request->template_id;
        $order->order_template = $template;
        $order->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('orders.edit', [$order->id, 'template'])->with(compact('msg'));
    } 

    /**
     * 17. Eliminar plantilla del pedido.
     */
    public function removeTemplate(Order $order){
        $order->order_template_id = NULL;
        $order->order_template = NULL;
        $order->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('orders.edit', [$order->id, 'template'])->with(compact('msg'));
    }

    /**
     * 18. Añadir descuento a pedido.
     */
    public function addDiscount(Request $request, Order $order){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-budget-discount');

            return redirect('admin/'.$request->return.'/'.$order->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Calculamos ambos tipos de descuento a partir del import total:
        $budget_amount = $order->itemsBudgetOrderAmount(true);

        //Descuento neto
        if($request->discount_amount){
            $discount_amount = $request->discount_amount;
            $discount = $budget_amount > 0? round((($request->discount_amount * 100) / $budget_amount), 2):'0';

        }elseif($request->discount_percent){
            $discount_amount = helpers::percent($budget_amount, $request->discount_percent);
            $discount = $request->discount_percent;
        }

        $order->discount = $discount;
        $order->discount_amount = $discount_amount;
        $order->save();

        //Eliminamos descuentos parciales de cada línea del presupuesto:
        Item::where('order_id', $order->id)
        ->update(['discount' => NULL]);

        $msg = trans('textos.descuento_aplicado_ok');
        if($request->return == 'budgets'){
            return redirect()->route('budgets.edit', [$order->id, 'items'])->with(compact('msg'));    
        }else{
            return redirect()->route('orders.edit', [$order->id])->with(compact('msg'));
        }
    }

    /**
     * 18.1. Eliminar descuento de pedido.
     */
    public function removeDiscount(Order $order, $return = false){
        //Eliminamos descuento sobre cada línea del presupuesto que tuviese ese porcentaje aplicado.  Puede haber alguna línea que tenga un descuento diferente.
        Item::where('order_id', $order->id)
        ->where('discount', $order->discount)
        ->update(['discount' => NULL]);

        $order->discount = NULL;
        $order->discount_amount = NULL;
        $order->save();

        $msg = trans('textos.descuento_deleted_ok');
        if($return == 'orders'){
            return redirect()->route('orders.edit', $order->id)->with(compact('msg'));
        }else{
            return redirect()->route('budgets.edit', [$order->id, 'items'])->with(compact('msg'));    
        }
    }

    /**
     * 19. Modificar cliente de pedido.
     */
    public function setCustomer(Request $request, Order $order){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-set-customer');

            return redirect('/admin/'.$request->environment.'/'.$order->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Comprobamos de nuevo que no se haya facturado el pedido:
        if($order->invoiced){
            $alert = trans('textos.no_permitida');  
            return redirect()->route('orders.edit', $order->id)->with(compact('alert')); 
            exit;     
        }

        //Actualizamos valores:
        $order->customer_id = $request->new_customer;
        $order->customer_manager_id = NULL;
        $order->customer_workplace_id = NULL;
        $order->customer_cost_center_id = NULL;
        $order->customer_business_area_id = NULL;
        $order->customer_order = NULL;
        $order->customer_order_doc = NULL;
        $order->customer_team = NULL;
        $order->customer_products = NULL;
        $order->confirmed_by = NULL;
        $order->confirm_code = NULL;
        $order->confirm_code_date = NULL;
        $order->save();

        $msg = trans('textos.cliente_modificado');
        if($request->environment == 'budgets'){
            return redirect()->route('budgets.edit', $order->id)->with(compact('msg'));    
        }else{
            return redirect()->route('orders.edit', $order->id)->with(compact('msg'));
        }
    }

    /**
     * 20. Eliminar documento de orden de compra.
     */
    public function removePurchaseDoc(Order $order){
        File::delete(public_path().'/img/purchase-orders/'.$order->purchase_doc);

        $order->purchase_doc = NULL;
        $order->save();

        $msg = trans('textos.archivo_deleted');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 21. Imprimir pedido.
     */
    public function print(Order $order){
        if(!$order){
            return redirect('/');
            exit;   
        }

        $lines = Item::select('products.name', 'products.ref', 'products.price AS originalPrice', 'items.*')
        ->leftJoin('products', 'items.product_id', '=', 'products.id')
        ->where('items.order_id', $order->id)
        ->get();

        $provider = Company::find($order->provider_id);
        $customer = Company::find($order->customer_id);

        $template = $order->template? unserialize($order->template):false;

        //Customizando CIF, ponemos espacio en blanco:
        //$company->nif = helpers::setCif($company->nif);

        $owner = User::select('name', 'surname')->find($order->owner);

        

        $fechaLiteral = Helpers::fechaLiteral($order->created_at, session()->get('locale'));

        $company_settings = session('companySettings');

        //Sede proveedor:
        if($order->provider_workplace_id){
            $provider_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $order->provider_workplace_id)
            ->first();  

            $provider_phones = Phone::select('phone_number')
            ->where('workplace_id', $provider_workplace->id)
            ->get();
        }else{
            $provider_workplace = false;
            $provider_phones = false;
        }

        //Sede cliente:
        if($order->workplace_id){
            $customer_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $order->workplace_id)
            ->first();  

        }else{
            $customer_workplace = false;
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
        $currency = $this->co_currency;





        //Título .pdf:
        $pdf_title = $order->order_num? helpers::slugFiles($order->order_num, '_', false):$order->id;

        $pdf = PDF::loadView('pdf/order', compact('order', 'lines', 'provider', 'customer', 'owner', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'provider_phones', 'customer_workplace', 'provider_manager', 'customer_manager', 'currency'));
        return $pdf->download($pdf_title.'.pdf'); 
    }
    
}
