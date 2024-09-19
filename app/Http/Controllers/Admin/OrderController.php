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
use App\Models\AccountingAccount;
use App\Models\BusinessAreas;
use App\Models\Category;
use App\Models\Company;
use App\Models\CompanyAccounts;
use App\Models\CompanySettings;
use App\Models\CostCenters;
use App\Models\CustomerProviders;
use App\Models\Delivery;
use App\Models\DeliveryOrders;
use App\Models\Employee;
use App\Models\Group;
use App\Models\GroupMembers;
use App\Models\Invoice;
use App\Models\InvoicePatterns;
use App\Models\Item;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderCategory;
use App\Models\OrderDocs;
use App\Models\OrderPatterns;
use App\Models\OrderPrevisions;
use App\Models\OrderProductionPrevision;
use App\Models\OrderSettings;
use App\Models\Phone;
use App\Models\Product;
use App\Models\ProductProviders;
use App\Models\Project;
use App\Models\ProjectOrders;
use App\Models\ProjectPurchases;
use App\Models\PurchasePatterns;
use App\Models\Sat;
use App\Models\TempExportOrders;
use App\Models\User;
use App\Models\UserCompanies;
use App\Models\UserWorkplaces;
use App\Models\Workplace;

//Exports XLS:
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller{
    /**
     * 1. Pedidos por cliente o proveedor.
     * 1.1. Filtro de pedidos por cliente o proveedor.
     * 1.2. Listado de pedidos por cliente y proveedor para DataTable.
     * 1.3. Query - Obtiene el total de pedidos.
     * 1.4. Obtiene el total de pedidos para el datatable
     * 2. Formulario nuevo pedido.
     * 3. Guardar nuevo pedido.
     * 3.1. Generar pedido desde presupuesto.
     * 3.2. Generar pedido desde producto.
     * 4. Editar pedido.
     * 4.1. Mostrar pedido.
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
     * 18.2. Eliminar descuento sobre todas las líneas del pedido.
     * 19. Modificar cliente de pedido.
     * 20. Eliminar documento de orden de compra.
     * 21. Imprimir pedido.
     * 22. Añadir miembro a equipo.
     * 23. Eliminar miembro de equipo.
     * 24. Pedidos según proveedor.
     * 25. Exportar .xls pedidos por rango de fecha.
     * 26. Pedidos para autocomplete.
     * 26.1. Pedidos de compra para autocomplete.
     * 27. Cerrar pedido.
     * 28. Recálculo previsiones en pedidos pendientes.
     */
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:orders');
        $this->middleware('order_get', ['only' => ['orderFromBudget', 'edit', 'update', 'updateConditions', 'destroy', 'print', 'setOrderDelivery', 'resetNum', 'removePurchaseDoc', 'close', 'deleteDiscount']]);
        $this->middleware('project_purchases', ['only' => ['addPurchaseToProject']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación filtro:
        if(isset($data['submit_filter'])){

            // Si viene el orden_buy realizar la validación de los campos necesarios
            if($data['order_buy']){
                return Validator::make($data, [
                    'year' => 'required|integer',
                    'order_buy' => 'required',
                ]);
            }

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
                'percent' => 'nullable|array',
                'pattern_id' => 'nullable|integer',
                'purchase_pattern_id' => 'nullable|integer',
                'status' => 'nullable|integer',
                'customer_manager_id' => 'nullable|integer',
                'customer_workplace_id' => 'nullable|integer',
                'customer_business_area_id' => 'nullable|integer',
                'customer_cost_center_id' => 'nullable|integer',
                'provider_manager_id' => 'nullable|integer',
                'provider_workplace_id' => 'nullable|integer',
                'provider_business_area_id' => 'nullable|integer',
                'provider_cost_center_id' => 'nullable|integer',
                'commercial_id' => 'nullable|integer',
            ],[
                'pattern_id.integer' => trans('textos.required_valor_listado'),
                'status.integer' => trans('textos.required_valor_listado'),
                'provider_manager_id.integer' => trans('textos.required_valor_listado'),
                'provider_workplace_id.integer' => trans('textos.required_valor_listado'),
                'provider_business_area_id.integer' => trans('textos.required_valor_listado'),
                'provider_cost_center_id.integer' => trans('textos.required_valor_listado'),
                'customer_manager_id.integer' => trans('textos.required_valor_listado'),
                'customer_workplace_id.integer' => trans('textos.required_valor_listado'),
                'customer_business_area_id.integer' => trans('textos.required_valor_listado'),
                'customer_cost_center_id.integer' => trans('textos.required_valor_listado'),
                'commercial_id.integer' => trans('textos.required_valor_listado'), 
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

        //Validación añadir miembro:
        }elseif(isset($data['submit_member'])){
            return Validator::make($data, [
                'environment' => 'required',
                'member' => 'required|integer'
            ]); 
        } 
    }
    
    /**
     * 1. Pedidos por cliente o proveedor.
     */
    public function index($year = false, $customer_id = false, $provider_id = false, $owner = false, $groups_ = false, $status = false, $from = '', $to = '', $order_buy = false){
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

        if($provider_id == session('currentCompany') || $customer_id == 'all'){
            $ordersType = 'ventas';
        }elseif($customer_id == session('currentCompany') || $provider_id == 'all'){
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

        $year = $year? $year:Date('Y');

        $customer = $customer_id && $customer_id != 'all'? Company::find($customer_id):false;
        $provider = $provider_id && $provider_id != 'all'? Company::find($provider_id):false;

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

        //Grupos:
        $groups = Group::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //Estados de pedido:
        $order_status = helpers::orderStatus();

        // //Pasamos el índice del status de pedido:
        // if($status && $status != ''){
        //     foreach($arrStatus as $i => $os){
        //         if($status == $os){
        //             $status = $i;
        //             break;
        //         }
        //     }
        // }

        // //Status de pedidos:
        // $order_status = '';
        // foreach($arrStatus as $i => $os){
        //     $order_status .= '<options value="'.$i.'">'.$os.'</option>';
        // }

        //Módulos activos:
        $module_accounting = $this->settedModule('accounting');

        $languages = $this->languages;

        return view('admin.orders.index')->with(compact('header', 'year', 'owner', 'status', 'from', 'to', 'customer_id', 'provider_id', 'customer', 'provider', 'ordersType', 'customers', 'providers', 'owners', 'groups_', 'groups', 'order_status', 'module_accounting', 'order_buy', 'languages'));
    }

    /**
     * 1.1. Filtro de pedidos por cliente o proveedor.
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
            $orderNum = $request->order_id;

            //Comprobamos si el valor recibido corresponde a id, order_num o old_order:
            $order = Order::select('id')
            ->where('provider_id', session('currentCompany'))
            ->where(function($q) use ($orderNum){
                $q->where('id', $orderNum)
                ->orWhere('order_num', $orderNum)
                ->orWhere('order_old_num', $orderNum);
            })
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

        // Si viene el código de orden de compra, comprobamos si existe un pedido con este numero, si existe uno solo se redirige
        if($request->order_buy){
            $orderBuy = $request->order_buy;

            $orders_of_order_buy = Order::select('id')
            ->where('customer_id', session('currentCompany'))
            ->where(function($q) use ($orderBuy){
                $q->where('id', $orderBuy)
                ->orWhere('purchase_num', 'like', "%$orderBuy%")
                ->orWhere('purchase_old_num', 'like', "%$orderBuy%");
            })
            ->get();
            if ($orders_of_order_buy->count() == 1) {
                return redirect()->route('orders.edit', $orders_of_order_buy[0]->id);    
                exit;   
            }
        }

        //Si no viene declarado ni cliente ni proveedor redirigimos al inicio:
        if(!$request->customer_id && !$request->provider_id && !$request->order_buy){
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

        //Formateando fechas:
        $from = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:false;
        $to = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:false;
        //La fecha fin no puede ser anterior a la fecha inicio:
        if($from && $to && strtotime($from) > strtotime($to)){
            $alert = trans('textos.fecha_final_inferior_aviso');
            return redirect()->route('orders.index')->with(compact('alert'));
            exit;
        }

        // Si no cumple con ningun requisito, se asigna la empresa actual como proveedor:
        if (!$request->provider_id && !$request->customer_id) {
            $customer_id = 'all';
            $provider_id =  session('currentCompany');
            session(['orderCompanySelected' => ['customer', $request->customer_id]]);
        }

        //Estado:
        $status = $request->status? $request->status:'0';

        $groups = $request->groups? implode('-', $request->groups):'0';
        
        return redirect()->route('orders.index', [$request->year, $customer_id, $provider_id, $request->owner, $groups, $status, $from, $to, $request->order_buy]);
    }

    /**
     * 1.2. Listado de pedidos por cliente y proveedor para DataTable.
     * 
     * @param Request $request *Nuevo*
     * @param int $year
     * @param int $customer
     * @param int $provider
     * @param int $owner
     * @param int $status
     * @param date $from
     * @param date $to
     * @param string $order_buy
     * 
     * @return json
     */
    public function getOrdersDT(Request $request, $year = false, $customer = false, $provider = false, $owner = false, $groups = false, $status = false, $from = false, $to = false, $order_buy = false){
        //La empresa en sesión debe ser cliente o proveedor del pedido.
        if($customer && $customer != session('currentCompany') && $provider && $provider != session('currentCompany')){
            return false;
            exit;
        }

        $company_field = $provider == session('currentCompany') || $customer == 'all'? 'customer':'provider';

        /* $order_buy = "%4700480218%"; */
        // Se comrpueba que el campo no sea false
        $order_buy = $order_buy == 'false' ? false : $order_buy;

        $owners = [];
        //Usuarios de los grupos:
        if($groups && $groups != '0'){
            $arr_groups = explode('-', $groups);

            $owners = GroupMembers::distinct('user_id')
            ->whereIn('group_id', $arr_groups)
            ->pluck('user_id')
            ->toArray();
        }
        if($owner && !in_array($owner, $owners)) array_push($owners, $owner);

        //Obtenemos pedidos simples, no agrupaciones:
        $fields_order = [
            'orders.id',
            'orders.is_budget',
            'orders.budget_num',
            'orders.discount',
            'orders.order_parent',
            'orders.name',
            'orders.order_num',
            'orders.order_old_num',
            'orders.estimated_delivery_date',
            'orders.purchase_num',
            'orders.purchase_old_num',
            'orders.delivery_date',
            'orders.provider_id',
            'orders.order_status',
            'orders.invoiced',
            'orders.created_at',
            'companies.name AS company', 
            'business_areas.name AS businessArea', 
            'cost_centers.name AS costCenter', 
            'users.name AS nombre', 
            'users.surname'
        ];
        $data = $this->buildOrdersQuery($company_field, $from, $to, $status, $order_buy, $owners, $customer, $provider, $year, $fields_order);

        // $arrIds = [];
        // foreach($data as $r){
        //     array_push($arrIds, $r->id);
        //     //$arrIds .= $r->id.',';
        // }

        $total = $this->getOrdersTotal($company_field, $from, $to, $status, $order_buy, $owners, $customer, $provider, $year, $request->all());

        $estados = helpers::orderStatus();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('created_at', function($row){
            return $row->created_at; 
        })
        ->addColumn('num', function($row){
            return view('admin.orders.partials.dt-num', compact('row')); 
        })
        ->addColumn('manager', function($row){
            if($row->nombre){
                return $row->nombre.' '.$row->surname;
            }else{
                return '';
            }
        })
        ->addColumn('company', function($row){
            return $row->company;
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
            return $amount['total_amount'];
        })
        //02/05/2023: se sustituye la columna de fecha prevista por fecha de entrega a petición de Marcelo. Además la fecha de entrega se actualizará en el momento que el estado de un pedido cambie a Finalizado.
        ->addColumn('fechaEntrega', function($row){
            if($row->delivery_date){
                return Carbon::parse($row->delivery_date)->format($this->languages[session()->get('locale')][4]);   
            }else{
                return '';
            }
        })
        // ->addColumn('fechaEntregaPrevista', function($row){
        //     if($row->estimated_delivery_date){
        //         return Carbon::parse($row->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]);   
        //     }else{
        //         return '';
        //     }
        // })
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
        ->with('total', $total)
        ->rawColumns(['order_parent'])
        ->make(true);
    }

    /**
     * 1.3. Query - Obtiene el total de pedidos.
     * 
     * @param string $company_field
     * @param string $from
     * @param string $to
     * @param string $status
     * @param string $order_buy
     * @param string $owner
     * @param string $customer
     * @param string $provider
     * @param string $year
     * @param array $fields - Listado de campos a obtener.
     * 
     * @return Eloquent<Order> Query de eloquent para obtener los pedidos.
     */
    public function buildOrdersQuery($company_field, $from, $to, $status, $order_buy, $owners, $customer, $provider, $year, $fields){
        //Obtenemos pedidos simples, no agrupaciones:
        $data = Order::select($fields)
        ->join('companies', 'orders.'.$company_field.'_id', '=', 'companies.id')
        ->leftJoin('business_areas', 'orders.customer_business_area_id', '=', 'business_areas.id')
        ->leftJoin('cost_centers', 'orders.customer_cost_center_id', '=', 'cost_centers.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        //->leftJoin('workplaces', 'orders.workplace_id', '=', 'workplaces.id')
        ->where('orders.is_order', true)
        ->where('orders.is_group', false)
        //Por año, si no viene declarado el intervalo de fechas:
        ->when(!$from || !$to, function($q) use($year){
            return $q->whereYear('orders.created_at', $year);
        })
        //Por estado:
        ->when($status && $status > 0, function($q) use($status){
            return $q->where('orders.order_status', $status);
        })
        // Consulta para comprobar el numero de orden de compra:
        ->when($order_buy, function($q) use($order_buy) {
            return $q->where(function($q) use($order_buy) {
                $q->orWhere('orders.purchase_num', 'like', "%$order_buy%")
                ->orWhere('orders.purchase_old_num', 'like', "%$order_buy%");
            });
        })
        // Intervalo fechas (por fecha de creación):
        ->when($from && $to, function($q) use($from, $to){
            return $q->whereBetween('orders.created_at', [$from, $to]);
        })
        // Por manager:
        ->when(count($owners), function($q) use($owners){
            return $q->whereIn('orders.provider_manager_id', $owners);
        })
        ->when($customer && $provider, function($q) use($customer, $provider){
            if(is_numeric($customer) && is_numeric($provider)){
                return $q->where('orders.customer_id', $customer)
                ->where('orders.provider_id', $provider);

            }elseif($customer == 'all'){
                return $q->where('orders.provider_id', $provider);

            }elseif($provider == 'all'){
                return $q->where('orders.customer_id', $customer);
            }
        });

        // IMPORTANTE! Si ordenamos desde el controlador, no se ordena desde la vista:
        //->orderBy('orders.created_at', 'DESC');

        //->orderBy('orders.id', 'ASC')
        //->get();
        //Filtro status:
        /*$q->when($status && $status != '', function($q) use($status){
            return $q->where('orders.status', $status);
        });*/

        return $data;
    }

    /**
     * 1.4. Obtiene el total de pedidos para el datatable.
     * 
     * @param string $company_field
     * @param string $from
     * @param string $to
     * @param int $status
     * @param string $order_buy
     * @param int $owner
     * @param int $customer
     * @param int $provider
     * @param int $year
     * @param array $request_all_data
     * 
     * @return array
     */
    public function getOrdersTotal($company_field, $from, $to, $status, $order_buy, $owners, $customer, $provider, $year, $request_all_data){

        // Filtro de campos explicitos:
        $filter_data_raw = [];
        foreach($request_all_data['columns'] as $item) {
            if ($item['search']['value'] != null && $item['search']['value'] != '' ) {
                $filter_data_raw[$item['name']] = "%".$item['search']['value']."%";
            }
        }

        // Filtro de campos global:
        $filter_data_raw_or = [];
        foreach($request_all_data['columns'] as $item) {
            if ( $item['searchable'] == "true" && $item['name'] != '') {
                $filter_data_raw_or[$item['name']] = "%".$request_all_data['search']['value']."%";
            }
        }

        // Campos minimos para que la consulta funcione:
        $fields = ['orders.id', 'orders.discount'];

        //$owners = [$owner];

        // Obtenemos la consulta:
        $data = $this->buildOrdersQuery($company_field, $from, $to, $status, $order_buy, $owners, $customer, $provider, $year, $fields);

        // Filtro global:
        $data->where(function($q) use ($filter_data_raw_or) {
            foreach ($filter_data_raw_or as $key => $item) {
                if($item != '' && $key != 'estado'){
                    $q->orWhere($key, 'like', $item);
                }
            }
        });

        // Filtro explícito:
        foreach ($filter_data_raw as $key => $item) {
            if($item != ''){
                $data->where($key, 'like', $item);
            }
        }

        $all_data = $data->get();
        
        // Obtenemos el total:
        $total = 0;

        foreach ($all_data as $key => $item) {
            $total += $item->getAmount()['total_amount'];
        }

        // Formateamos el total, con dos decimales y punto como separador de miles:
        $total = number_format($total, 2, '.', '');
        return $total;
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
            ->where('customer_providers.status', 1)
            ->where('companies.id', '!=', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->get();
        }

        //La empresa ha de tener habilitada la gestión de proveedores:
        if($type == 'purchase' && session('companySettings') && session('companySettings')['providers_management']){
            $companies = CustomerProviders::select('companies.id', 'companies.name', 'companies.tradename')
            ->join('companies', 'customer_providers.provider_id', '=', 'companies.id')
            ->where('customer_providers.customer_id', session('currentCompany'))
            ->where('customer_providers.status', 1)
            ->where('companies.id', '!=', session('currentCompany'))
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

        //Centro de trabajo del empleado autor del pedido:
        $workplace = UserWorkplaces::select('workplaces.id')
        ->join('workplaces', 'user_workplaces.workplace_id', '=', 'workplaces.id')
        ->where('user_workplaces.user_id', Auth::user()->id)
        ->where('workplaces.company_id', session('currentCompany'))
        ->first();
        //Según sea una compra o una venta:
        if($request->type == 'sale'){
            $provider_workplace_id = $workplace? $workplace->id:NULL;
            $customer_workplace_id = NULL;

        }elseif($request->type == 'purchase'){
            $customer_workplace_id = $workplace? $workplace->id:NULL;
            $provider_workplace_id = NULL;
        }

        //Slug:
        $slug = $request->name? Str::slug($request->name):NULL;

        //Condiciones comerciales del pedido:
        $cu_pr = CustomerProviders::customerProvider($customer_id, $provider_id);

        //Cuenta contable de ventas del proveedor:
        $provider_accounting_account_default = AccountingAccount::select('id')
        ->where('company_id', $provider_id)
        ->where('featured', config('constants.ACCOUNT_DEFAULT_SALES_'))
        ->first();

        //Creamos pedido:
        $order = new Order();
        $order->is_order = true;
        $order->name = $request->name? $request->name:NULL;
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
        $order->provider_workplace_id = $provider_workplace_id;
        if($request->type == 'sale'){
            $order->provider_manager_id = Auth::user()->id;
        }
        $order->customer_workplace_id = $customer_workplace_id;
        $order->num_deadlines = $cu_pr->num_deadlines;
        $order->days_between_deadlines = $cu_pr->days_between_deadlines;
        $order->accounting_sale_id = $provider_accounting_account_default? $provider_accounting_account_default->id:NULL;
        $order->save();

        //Guardamos previsión de facturación por defecto:
        OrderPrevisions::store($order->id);

        //Guardamos previsión de producción por defecto:
        OrderProductionPrevision::store($order->id);

        //Descuento por defecto:
        $order->setDefaultDiscount();

        //Generación de proyecto:
        if($request->set_project){
            Project::setProject($order);
        }

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

        //SAT:
        if($request->sat){
            if($order->customer_id == session('currentCompany')){  
                Sat::where('id', $request->sat)
                ->update(['purchase_id' => $order->id]);
            }else{
                Sat::where('id', $request->sat)
                ->update(['sale_id' => $order->id]);
            }  
        }

        $msg = trans('textos.pedido_creado');
        if($request->sat){
            return redirect()->route('sat.edit', $request->sat)->with(compact('msg'));
        }else{
            return redirect()->route('orders.edit', $order)->with(compact('msg'));
        }
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

        //Creamos proyecto:
        // if(session('companySettings')->project_required){
        //     Project::setProject($order);
        // }

        $msg = trans('textos.pedido_creado');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 3.2. Generar pedido desde producto.
     */
    public function orderFromProduct(ProductProviders $product_provider){
        //Condiciones comerciales del pedido:
        $cu_pr = CustomerProviders::customerProvider(session('currentCompany'), $product_provider->provider_id);

        //Creamos pedido:
        $order = new Order();
        $order->is_order = true;
        $order->name = NULL;
        $order->slug = NULL;
        $order->is_group = false;
        $order->created_by = Auth::user()->id;
        $order->customer_id = session('currentCompany');
        $order->customer_manager_id = Auth::user()->id;
        $order->provider_id = $product_provider->provider_id;
        $order->num_deadlines = $cu_pr->num_deadlines;
        $order->days_between_deadlines = $cu_pr->days_between_deadlines;
        $order->order_status = config('constants.ORDER_STATUS_PENDENT_');
        $order->save();

        //Guardamos previsión de facturación por defecto:
        OrderPrevisions::store($order->id);

        //Descuento por defecto:
        $order->setDefaultDiscount();

        //Producto del proveedor:
        $product = Product::select('id', 'name', 'ref', 'price', 'iva')->find($product_provider->provider_product_id);

        //Generamos el item del producto.
        $item = new Item();
        $item->product_id = $product->id;
        $item->order_id = $order->id;
        $item->concept = $product->name? $product->name:$product_provider->provider_product;
        $item->qty = '0';
        $item->price = $product->price;
        $item->iva = $product->iva;
        $item->user_id = Auth::user()->id;
        $item->save();

        $msg = trans('textos.pedido_creado');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 4. Editar pedido.
     */
    public function edit(Order $order, $tab = false){
        //Si no consta como pedido se redirige a budget:
        if(!$order->is_order){
            $alert = trans('textos.presupuesto_no_pedido_warning');
            return redirect()->route('budgets.edit', $order)->with(compact('alert')); 
            exit;   
        }

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

        $order_type = $order->customer_id == session('currentCompany')? 'purchase':'sale';

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

        //Patrones de numeración de facturas:
        $invoice_patterns = InvoicePatterns::select('id', 'name', 'yearly_reset')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        //Cuenta del proveedor:
        $provider_account = CompanyAccounts::where('company_id', $provider->id)
        ->where('status', 1)
        ->first();

        // 09/06/2023: se modifica la consulta para traer usuarios vinculados a la empresa, aunque no sean empleados.
        $provider_managers = UserCompanies::select(
            DB::raw("CONCAT(users.name,' ',users.surname) AS user"), 'users.id')
        ->join('users', 'user_companies.user_id', '=', 'users.id')
        ->where('users.status', 1)
        ->where('users.isAdmin', 1)
        ->where('user_companies.company_id', $order->provider_id)
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

        //CLIENTE FINAL:
        $final_customer = $order->final_customer_id? Company::select('id', 'name', 'tradename')->find($order->final_customer_id):false;

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

        //Catálogo del cliente, para asociar producto:
        $customer_products = [];
        $customer_catalogue = Product::select('id', 'name', 'ref', 'manual_ref')
        ->where('company_id', $order->customer_id)
        ->where('status', 1)
        ->orderBy('name', 'ASC')
        ->get();
        foreach($customer_catalogue as $cc){
            $ref = $cc->ref? $cc->ref:($cc->manual_ref? $cc->manual_ref:false);
            $val = $ref? $cc->name.' ('.$ref.')':$cc->name; 
            $customer_products[$cc->id] = $val;
        }

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
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
            $order_amount_without_discount = false;
            $order_amount_without_discount_in_lines = false;

        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['total_amount'];
            $order_amount_without_discount = $arr_amounts['total_amount_without_discounts'];
            //$order_amount_without_discount_in_lines = $arr_amounts['amount_without_discount_in_lines'];
            $order_amount_without_discount_in_lines = $arr_amounts['total_amount_without_discounts'];
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
                $group_amount = $group_amount + $orderAmount['total_amount'];
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
            //Pedido perteneciente a agrupación:
            if($order->order_parent && $order->order_parent > 0){
                $invoices = Invoice::select('invoices.id', 'invoices.invoice_num', 'invoices.amount', 'invoices.amount_with_iva')
                ->join('invoice_orders', 'invoices.id', '=', 'invoice_orders.invoice_id')
                ->where('invoice_orders.order_id', $order->id)
                ->get();
            
            //Pedido independiente:    
            }else{         
                $invoices = Invoice::select('id', 'invoice_num', 'amount', 'amount_with_iva')
                ->where('order_id', $order->id)
                ->get();
            }
        }
        if($invoices->count()) $permitted = false;

        //Previsiones de facturación del pedido:    
        $generate = $order->blocked == 1? false:true;  
        $previsions = OrderPrevisions::previsions($order->id, $order_amount, $generate);

        //Debe existir siempre al menos una previsión:
        // if($previsions && !$previsions->count()){
        //     OrderPrevisions::store($order->id);

        //     $previsions = OrderPrevisions::previsions($order->id, $order_amount);
        // }

        //Previsiones de producción:
        $production_previsions = OrderProductionPrevision::where('order_id', $order->id)
        ->get();

        //Productos. Activos y aptos para venta:
        $products = Product::companyProducts($order->provider_id, $order->customer_id, true, true);

        //Proyectos:
        $projects = Project::select('id', 'code', 'name')
        ->where('company_id', session('currentCompany'))
        ->where(function($q) use ($order){
            $q->where('customer_id', $order->customer_id)
            ->orWhereNull('customer_id');
        })
        ->orderBy('name')
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

        //Proyecto del cliente:
        $customer_projects = false;
        if($order_type == 'sale'){
            $customer_projects_arr = ProjectPurchases::select('projects.id', 'projects.project_num', 'projects.project_old_num')
            ->join('projects', 'project_purchases.project_id', '=', 'projects.id')
            ->where('project_purchases.order_id', $order->id)
            ->get();

            if($customer_projects_arr->count()){
                $customer_projects = '';
                foreach($customer_projects_arr as $cp){
                    $p = $cp->project_num? $cp->project_num:($cp->project_old_num? $cp->project_old_num:$cp->id);
                    $customer_projects .= $p.', ';                     
                }
                $customer_projects = rtrim($customer_projects,', ');
            }
        }

        //Entregas del pedido:
        $deliveries = DeliveryOrders::select('delivery_orders.id', 'deliveries.id AS deliveryId', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.origin', 'deliveries.estimated_reception_date', 'deliveries.reception_date', 'deliveries.customer_observations', 'deliveries.provider_observations')
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

        //Manager:
        $owner = User::select('name', 'surname')->find($order->provider_manager_id);

        //Comercial:
        $commercial = User::select('name', 'surname')->find($order->commercial_id);

        //IVA:
        $iva = CompanySettings::co_iva();

        //Tipos de IVA:
        $ivaTypeController = new IvaTypeController();
        $ivaTypes = $ivaTypeController->ivaTypes;

        //Cuenta contable de ventas del proveedor:
        $provider_accounting_account_default = AccountingAccount::select('id')
        ->where('company_id', $order->provider_id)
        ->where('featured', config('constants.ACCOUNT_DEFAULT_SALES_'))
        ->first();

        //Cuentas contables de proveedor:
        $provider_accounting_accounts = AccountingAccount::select(DB::raw("CONCAT(account,' - ',name) AS cuenta"), 'id')
        ->where('company_id', $order->provider_id)
        ->where('company_target', $order->provider_id)
        ->where('mode', 4)      //Mode = de contabilidad
        ->pluck('cuenta', 'id')
        ->toArray();

        //Cuentas contables de cliente:
        $customer_accounting_accounts = AccountingAccount::select('id', 'account', 'name')
        ->where('company_id', $order->provider_id)
        ->where('company_target', $order->customer_id)
        ->where('mode', 1)
        ->get();
        //Servicio de Asistencia Técnica (SAT), según sea la venta o la compra:
        $sat = $order->provider_id == session('currentCompany')? Sat::select('id', 'sat_num')
        ->where('sale_id', $order->id)->first():Sat::select('id', 'sat_num')
        ->where('purchase_id', $order->id)->first();
        
        //Templates:
        $templates = false;
        if($order->provider_id == session('currentCompany')){
            $templates = OrderSettings::select('id', 'name', 'featured', 'conditions', 'payment_method', 'observations', 'footer_legend')
            ->where('company_id', $order->provider_id)
            ->where('status', 1)
            ->get();
        }

        if($templates && count($templates) == 1 && !$order->order_template_id){
            $default_template = OrderSettings::select('conditions', 'payment_method', 'observations', 'footer_legend')
            ->find($templates[0]->id);

            $template = [
                'conditions' => $default_template? $default_template->conditions:'', 
                'payment_method' => $default_template? $default_template->payment_method:'',
                'observations' => $default_template? $default_template->observations:'',
                'footer_legend' => $default_template? $default_template->footer_legend:''
            ];

            $order->order_template_id = $templates[0]->id;
            $order->order_template = serialize($template);
            $order->save();
        }

        //Categorías vinculadas al proyecto:
        $categories_in = OrderCategory::where('order_id', $order->id)
        ->pluck('category_id')
        ->toArray();

        $languages = $this->languages;

        //Categorías:
        $module_ = Module::select('id')->where('slug', $this->module)->first();
        $categories = Category::getModuleCategoriesStatic($module_->id, session('currentCompany'));

        //Módulos activos:
        $module_accounting = $this->settedModule('accounting');
        $module_budgets = $this->settedModule('budgets');
        $module_logistics = $this->settedModule('logistics');
        $module_products = $this->settedModule('products');
        $module_projects = $this->settedModule('projects');
        $module_rrhh = $this->settedModule('rrhh');
        
        return view('admin.orders.edit')->with(compact('header', 'order', 'tab', 'patterns', 'purchase_patterns', 'invoice_patterns', 'provider', 'provider_account', 'provider_managers', 'provider_business_areas', 'provider_cost_centers', 'provider_workplaces', 'default_workplace', 'customer', 'final_customer', 'customer_managers', 'customer_business_areas', 'customer_cost_centers', 'customer_workplaces', 'customer_products', 'customer_settings', 'confirmed_by', 'customers', 'order_amount', 'order_amount_without_discount', 'order_amount_without_discount_in_lines', 'order_groups', 'ordersInGroup', 'orders_off', 'group_amount', 'invoiced', 'invoices', 'previsions', 'production_previsions', 'products', 'projects', 'project', 'project_purchases', 'customer_projects', 'deliveries', 'receivings', 'currency', 'items', 'author', 'owner', 'commercial', 'iva', 'ivaTypes', 'provider_accounting_account_default', 'provider_accounting_accounts', 'customer_accounting_accounts', 'sat', 'order_type', 'templates', 'categories', 'categories_in', 'languages', 'permitted', 'module_accounting', 'module_budgets', 'module_products', 'module_projects', 'module_rrhh', 'module_logistics'));
    }

    /**
     * 4.1. Mostrar pedido.
     */
    public function show(Order $order){
        //Si no consta como pedido se redirige a budget:
        if(!$order->is_order){
            $alert = trans('textos.presupuesto_no_pedido_warning');
            return redirect()->route('budgets.edit', $order)->with(compact('alert')); 
            exit;   
        }

        if($order->is_group){
            $slug = 'order-groups';
        }else{
            $slug = 'orders';    
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.pedido_detalle',
            'module' => $this->module,
            'slug' => $slug
        ];   

        $order_type = $order->customer_id == session('currentCompany')? 'purchase':'sale';

        //PROVEEDOR:
        $provider = Company::select('id', 'name', 'tradename')->find($order->provider_id);

        //CLIENTE:
        $customer = Company::select('id', 'name', 'tradename')->find($order->customer_id);

        //CLIENTE FINAL:
        $final_customer = $order->final_customer_id? Company::select('id', 'name', 'tradename')->find($order->final_customer_id):false;
        
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

        //Importe del pedido:
        if($order->bag_amount){
            $order_amount = $order->bag_amount;
            $order_amount_without_discount = false;
            $order_amount_without_discount_in_lines = false;

        }else{
            $items = $order->items();
            $arr_amounts = $order->getAmount();    

            $order_amount = $arr_amounts['total_amount'];
            $order_amount_without_discount = $arr_amounts['total_amount_without_discounts'];
            //$order_amount_without_discount_in_lines = $arr_amounts['amount_without_discount_in_lines'];
            $order_amount_without_discount_in_lines = $arr_amounts['total_amount_without_discounts'];
        } 

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $languages = $this->languages;

        return view('admin.orders.show')->with(compact('header', 'order', 'order_type', 'provider', 'customer', 'final_customer', 'customer_managers', 'customer_business_areas', 'customer_cost_centers', 'customer_workplaces', 'order_amount', 'order_amount_without_discount', 'order_amount_without_discount_in_lines', 'currency', 'languages'));  
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

        $alert = false;

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
        $order->customer_products = $request->customer_products? serialize($request->customer_products):NULL;
        $order->customer_items = $request->customer_items;
        if($request->customer_confirmation){
            $order->customer_confirmation = NOW();
            $order->confirmed_by = Auth::user()->id;
        }else{
            $order->customer_confirmation = NULL;   
            $order->confirmed_by = NULL; 
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
            //02/05/2023: la fecha de entrega se actualiza automáticamente si el pedido se encuentra en algunos de los estados de finalizado:
            if(in_array($request->status, config('constants.ORDER_STATUS_FINISHED_'))){
                $order->delivery_date = Carbon::today('Europe/Madrid');
                $order->finished_at = now();
                $order->finished_by = Auth::user()->id;
            }
        }
        if($request->hours){
            $order->hours = $request->hours;
        }
        if($request->hours_extra){
            $order->hours_extra = $request->hours_extra;
        }
        //$order->save();

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
            $order->commercial_id = $request->commercial_id;
        }
        $order->save();

        //Seteamos Centro de Coste para los items sin valor indicado:
        //1. Exclusivo proveedor:
        if($order->provider_id == session('currentCompany') && $request->provider_cost_center_id){
            Item::where('order_id', $order->id)
            ->whereNull('provider_cost_center_id')
            ->update(['provider_cost_center_id' => $request->provider_cost_center_id]);
        }
        
        if($request->year){
            $order_amount = $order->getAmount(true, false, true); 

            //Guardamos previsiones:
            for($i=0; $i<count($request->year); $i++){
                //Validamos datos si es previsión NO facturada:
                if(is_numeric($request->year[$i]) && !$request->invoiced[$i]){
                    //Actualizar o...
                    //29/12/2023: trasladamos la actualización de las previsiones a la edición del pedido.  Como primer paso se recalcularán cada vez importes y porcentajes de las previsiones de facturación.
                    if(isset($request->op_id[$i])){
                        $op = OrderPrevisions::find($request->op_id[$i]);

                        //Actualizamos si no se encuentra facturado:
                        if( !$op->invoice_id){
                            $op->year = $request->year[$i];
                            $op->month = isset($request->month[$i])? $request->month[$i]:NULL;
                            $op->amount = isset($request->amount[$i])? $request->amount[$i]:NULL;
                            $op->percent = isset($request->percent[$i])? $request->percent[$i]:NULL;
                            $op->save();
                        }

                    //...insertar
                    }else{
                        $op = new OrderPrevisions();
                        $op->order_id = $order->id;
                        $op->year = $request->year[$i];
                        $op->month = isset($request->month[$i])? $request->month[$i]:NULL;
                        $op->amount = isset($request->amount[$i])? $request->amount[$i]:NULL;
                        $op->percent = isset($request->percent[$i])? $request->percent[$i]:NULL;
                        $op->save();
                    }
                }
            }

            //Sincronizando previsión de facturación entre proveedor y cliente si el pedido está vinculado a otro pedido de proveedor y entre ambas empresas hay sincronización de facturaciones:
            if($order->first_provider_order){
                //Pedido del proveedor primero.
                $first_provider_order = Order::select('id', 'is_order', 'provider_id', 'customer_id', 'final_customer_id')
                ->find($order->first_provider_order);

                if($first_provider_order && $first_provider_order->is_order){
                    //Comprobamos que cliente y proveedor tenga habilitada la sincronización de previsiones de facturación.
                    $customer_settings = CustomerProviders::select('syncro_invoice')
                    ->where('customer_id', $first_provider_order->customer_id)
                    ->where('provider_id', $first_provider_order->provider_id)
                    ->first();

                    if($customer_settings && $customer_settings->syncro_invoice){
                        //Previsiones del cliente aún no facturadas:
                        $customer_previsions = OrderPrevisions::where('order_id', $order->id)
                        ->whereNull('invoice_id')
                        ->get();

                        if($customer_previsions->count()){
                            //Eliminamos las previsiones no facturadas del proveedor primero:
                            OrderPrevisions::where('order_id', $first_provider_order->id)
                            ->whereNull('invoice_id')
                            ->delete();

                            //Guardamos las nuevas previsiones:
                            foreach($customer_previsions as $cp){
                                $op_ = new OrderPrevisions();
                                $op_->order_id = $first_provider_order->id;
                                $op_->year = $cp->year;
                                $op_->month = $cp->month;
                                $op_->amount = $cp->amount;
                                $op_->percent = $cp->percent;
                                $op_->save();
                            }
                        }
                    }                            
                }
            }

            if($request->pr_year){
                //Guardamos previsiones de producción eliminando las existentes y volviendo a crearlas:
                OrderProductionPrevision::where('order_id', $order->id)->forceDelete();

                for($j=0; $j<count($request->pr_year); $j++){
                    OrderProductionPrevision::create([
                        'order_id' => $order->id,
                        'year' => $request->pr_year[$j],
                        'month' => isset($request->pr_month[$j])? $request->pr_month[$j]:NULL,
                        'amount' => isset($request->pr_amount[$j])? $request->pr_amount[$j]:NULL,
                        'percent' => isset($request->pr_percent[$j])? $request->pr_percent[$j]:NULL,
                    ]);    
                }
            }
        }

        //Categorías. Limpiamos las existentes y volvemos a generarlas:
        OrderCategory::where('order_id', $order->id)->delete();  
        if($request->categories && count($request->categories)){
            foreach($request->categories as $ctg){
                //Comprobamos que el valor recibido sea categoría:
                $categ = Category::select('id')->find($ctg);
                if($categ){
                    OrderCategory::create([
                        'order_id' => $order->id,
                        'category_id' => $categ->id
                    ]);
                }else{
                    $alert = trans('textos.categoria_selec_aviso');
                }
            }
        }

        $msg = trans('textos.pedido_updated');
        return redirect()->route('orders.edit', [$order->id, false])->with(compact('msg', 'alert')); 
    }

    /**
     * 5.1. Actualizar condiciones comerciales.
     */
    public function updateConditions(Request $request, Order $order){
        $order->num_deadlines = $request->num_deadlines;
        $order->days_between_deadlines = $request->days_between_deadlines;
        $order->accounting_sale_id = $request->accounting_sale_id;
        $order->save();

        $msg = trans('textos.pedido_updated');
        return redirect()->route('orders.edit', [$order->id, 'conditions'])->with(compact('msg'));
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
    public function getItemsDT(Order $order, $environment, $invoice_id = false){
        $items = Item::where('order_id', $order->id);

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        //Permisos del usuario:
        $editar = Auth::user()->can('orders.edit');

        //Estados de línea:
        $estados = helpers::orderLineStatus();

        //Importe del pedido sin contar los items con precio porcentual:
        $order_amount = $order->getAmount(true, false, false);

        //Cuenta del proveedor:
        $provider_account = CompanyAccounts::where('company_id', $order->provider_id)
        ->where('status', 1)
        ->first();

        //Facturas:
        $invoices = Invoice::select('id', 'blocked')->where('order_id', $order->id)->get();

        return DataTables::of($items)
        ->addColumn('price', function($item) use($currency){
            if($item->price > 0){
                $price = number_format($item->price,2 , ',', '.').$currency->symbol;
                //IRPF:
                if($item->irpf > 0){
                    $price .= '<br><small>-'.$item->irpf.'% IRPF</small>';
                }
                return $price;

            }elseif(!$item->price && $item->calculated_by_percent > 0){
                return trans('textos.por_porcentaje').' ('.$item->calculated_by_percent.'%)';
            }
        })
        ->addColumn('iva', function($item){
            if($item->iva > 0){
                return $item->iva.'%';
            }else{
                return '';
            }
        })
        ->addColumn('discount', function($item){
            //Puede que la línea contenga 2 descuentos: el propio y el de días consecutivos por rodaje:
            $discounts = '';
            $other_discount = false;
            if($item->days_continue_discount > 0){
                $discounts .= $item->days_continue_discount.'%';
                $other_discount = true;
            }

            if($item->discount > 0){
                if($other_discount){
                    $discounts .= ', ';
                }
                $discounts .= $item->discount.'%';
            }

            return $discounts;
        })
        ->addColumn('amount', function($item) use($currency, $order_amount, $environment){
            $amount = '';
            if($item->price != 0){
                //Cálculo importe por porcentaje:
                if($item->calculated_by_percent > 0){
                    $amount = helpers::itemAmountPercentage($item->price, $order_amount['total_amount']);
                    return $amount.$currency->symbol.'<br><small>('.trans('textos.precio_porcentual').')</small>';
                }else{
                    $arr_amount = helpers::itemAmount($item);
                    $amount = $arr_amount['amount']; 

                    //Importe con iva en facturas:
                    if($environment == 'invoices'){
                        if($item->iva > 0){
                            $amount_with_iva = helpers::itemAmount($item, true);

                            return number_format($amount,2 , ',', '.').$currency->symbol.'<br><small>+iva '.number_format($amount_with_iva['amount'],2 , ',', '.').$currency->symbol.'</small>';
                        }else{
                            return $amount.$currency->symbol.'<br><small>'.trans('textos.iva_exento').'</small>';    
                        }
                    }else{
                        return number_format($amount,2 , ',', '.').$currency->symbol;    
                    }
                }            
            }else{
                return trans('textos.precio_sin_definir');
            }              
        })
        ->addColumn('cost_center', function($item){
            if($item->provider_cost_center_id && $item->cost_center_provider){
                return $item->cost_center_provider->name;    
            }elseif($item->customer_cost_center_id && $item->cost_center_customer){
                return $item->cost_center_customer->name;;
            }
        })
        ->addColumn('linked_orders', function($item){
            $orders_ = Order::select('orders.id', 'orders.order_num', 'orders.order_old_num')
            ->join('item_linked_orders', 'orders.id', '=', 'item_linked_orders.order_id')
            ->where('item_linked_orders.item_id', $item->id)
            ->get();

            if($orders_->count()){
                $html = '';
                foreach($orders_ as $o){
                    $o_ = $o->order_num? $o->order_num:($o->order_old_num? $o->order_old_num:$o->id);
                    $html .= '<a target="_blank" href="/admin/orders/'.$o->id.'/edit">'.$o_.'</a> ';
                }
                return $html;
            }else{
                return '';
            }
        })
        ->addColumn('estado', function($item) use($estados){
            return view('admin.orders.partials.dt-item-status', compact('item', 'estados'));    
        })
        ->addColumn('observations', function($item){
            return $item->observations;
        })
        ->addColumn('acciones', function($item) use($estados, $order, $environment, $provider_account, $invoices, $invoice_id){
            return view('admin.orders.partials.dt-item-actions', compact('item', 'estados', 'order', 'environment', 'provider_account', 'invoices', 'invoice_id'));    
        })
        ->rawColumns(['price', 'amount', 'linked_orders', 'observations'])
        ->make(true);
    }

    /**
     * 10. Actualizar estado de pedido.
     */
    public function status(Request $request, Order $order, $status){
        if($request->ajax()){    
            $order->order_status = $status;

            if(in_array($status, config('constants.ORDER_STATUS_FINISHED_'))){
                $order->delivery_date = Carbon::today('Europe/Madrid');
                $order->finished_at = now();
                $order->finished_by = Auth::user()->id;
            }

            $order->save();

            return response()->json(true);
        }
    }

    /**
     * 11. Panel control de ventas.
     */
    public function controlPanelSales($year = false, $workplace_id = false){
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

        $currency = CompanySettings::co_currency(0);

        //Centros de trabajo:
        $workplaces = Workplace::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $workplaces[''] = trans('textos.todos');
        ksort($workplaces);     //Reordenamos array.

        return view('admin.orders.control-panel')->with(compact('header', 'year', 'workplace_id', 'min_year', 'max_year', 'order_type', 'currency', 'workplaces'));
    }

    /**
     * 11.1. Filtrar año para panel de control de ventas.
     */
    public function controlPanelSalesPost(Request $request){
        return redirect()->route('orders.control-panel-sales', [$request->year, $request->workplace]);
    }

    /**
     * 11.2. Listado de pedidos del panel de control de ventas para DataTable.
     */
    public function getOrdersControlPanelSalesDT($year, $workplace_id = false){
        //Estados de pedido que quedan fuera del panel: pedidos cancelados,...
        $statusRejected = config('constants.ORDER_STATUS_NOT_INVOICED_');

        $estados = helpers::orderStatus();

        $currency = CompanySettings::co_currency(0);

        $data = Order::select('orders.id', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.is_group', 'orders.estimated_delivery_date', 'orders.delivery_date', 'orders.order_status', 'orders.invoiced', 'orders.discount', 'orders.bag_amount', 'order_previsions.year', 'order_previsions.month', 'order_previsions.percent', 'orders.created_at', 'companies.name AS Customer', 'users.name AS nombre', 'users.surname', 'cc.name AS finalCustomer', 'u_.name AS CustomerManagerName', 'u_.surname AS CustomerManagerSurname', 'business_areas.name AS businessArea', 'workplaces.name AS workplace')
        ->join('order_previsions', 'orders.id', '=', 'order_previsions.order_id')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->leftJoin('companies AS cc', 'orders.final_customer_id', '=', 'cc.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->leftJoin('users AS u_', 'orders.customer_manager_id', '=', 'u_.id')
        ->leftJoin('business_areas', 'orders.customer_business_area_id', '=', 'business_areas.id')
        ->leftJoin('workplaces', 'orders.provider_workplace_id', '=', 'workplaces.id')
        ->where('orders.provider_id', session('currentCompany'))
        ->where('orders.is_order', 1)
        //->where('orders.invoiced', 0)
        ->whereNull('orders.order_parent')
        ->whereNull('order_previsions.invoice_id')
        ->whereNull('order_previsions.deleted_at')
        ->where('order_previsions.year', $year)
        ->whereNotIn('orders.order_status', $statusRejected)
        ->when($workplace_id, function($q) use($workplace_id){
            return $q->where('orders.provider_workplace_id', $workplace_id);
        })
        ->orderBy('orders.id', 'DESC')
        ->orderBy('orders.created_at', 'DESC');
        //->get();

        return DataTables::of($data)
        ->addColumn('fecha', function($row){
            $month = $row->month && in_array($row->month, ['01','02','03','04','05','06','07','08','09','10','11','12'])? helpers::yearMonths($row->month):'';
            return '<span style="display: none">'.strtotime($row->year.'-'.$row->month.'-01').'</span>'.$month.' '.$row->year;
        })
        ->filterColumn('fecha', function($q, $keyword){
            $sql = "order_previsions.year like ?";
            $q->whereRaw($sql, ["%$keyword%"]);
        })
        ->addColumn('customer', function($row){
            return $row->Customer;
        })
        // ->addColumn('business_area', function($row){
        //     return $row->businessArea;
        // })
        ->addColumn('order_num', function($row){
            $order_num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->id);
            return '<a target="_blank" href="'.route('orders.edit', $row->id).'">'.$order_num.'</a>';
        })   
        ->addColumn('project', function($row){
            $projects = $row->projects;
            if($projects->count()){
                $projects_ = '';
                foreach ($projects as $project){
                    $num_ = $project->project_num ?? $project->id;
                    $projects_ .= '<a href="/admin/projects/'.$project->id.'/edit">'.$num_.'</a> ';
                }
                return $projects_;
            }else{
                return '';
            }
        })
        ->addColumn('purchase_num', function($row){
            $purchase_num = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');
            return $purchase_num;
        })
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('manager', function($row){
            if($row->nombre){
                return $row->nombre.' '.$row->surname;
            }else{
                return '';
            }
        })
        ->addColumn('workplace', function($row){
            return $row->workplace;
        })
        ->addColumn('finalCustomer', function($row){
            return $row->finalCustomer;
        })
        ->addColumn('amount', function($row){
            if($row->is_group){
                $amount = $row->getGroupAmount();
            }else{
                $arr_amount = $row->getAmount();
                $amount = $arr_amount['total_amount'];    
            }
            
            return number_format($amount, 2, '.', '');
        })  
        ->addColumn('a_facturar', function($row){ 
            if($row->is_group){
                if($row->bag_amount > 0){
                    $amount = $row->bag_amount;
                }else{
                    $amount = $row->getGroupAmount();
                }
            }else{
                $arr_amount = $row->getAmount();
                $amount = $arr_amount['total_amount'];    
            }

            //$amount = $row->getAmount();
            if($row->percent && $row->percent < 100){
                $a_facturar = helpers::percent($amount, $row->percent);  
                return number_format($a_facturar, 2, '.', '');
            }else{
                return number_format($amount, 2, '.', '');
            }  
        })
        ->addColumn('invoice_amount', function($row){ 
            return Invoice::amountByOrder($row->id);
        })
        ->addColumn('estado', function($row) use($estados){ 
            if(isset($estados[$row->order_status])){
                return ucfirst($estados[$row->order_status]);
            }else{
                return '';
            }
        })
        ->addColumn('acciones', function($row) use($estados){
            $manage_status = false;
            return view('admin.orders.partials.dt-actions', compact('row', 'estados', 'manage_status'));    
        })
        ->rawColumns(['fecha', 'order_num', 'project'])
        ->orderColumn('fecha', 'customer')
        ->make(true);
    }

    /**
     * 12. Panel control de compras.
     */
    public function controlPanelPurchases($year = false, $workplace_id = false){
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

        //Centros de trabajo:
        $workplaces = Workplace::where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $workplaces[''] = trans('textos.todos');
        ksort($workplaces);     //Reordenamos array.

        $currency = CompanySettings::co_currency(0);

        return view('admin.orders.control-panel')->with(compact('header', 'year', 'min_year', 'max_year', 'order_type', 'workplaces', 'workplace_id', 'currency'));
    }

    /**
     * 12.1. Filtrar año para panel de control de compras.
     */
    public function controlPanelPurchasesPost(Request $request){
        return redirect()->route('orders.control-panel-purchases', [$request->year, $request->workplace]);
    }

    /**
     * 12.2. Listado de pedidos del panel de control de compras para DataTable.
     */
    public function getOrdersControlPanelPurchasesDT($year){
        //Estados de pedido que quedan fuera del panel: pedidos cancelados,...
        $statusRejected = config('constants.ORDER_STATUS_NOT_INVOICED_');

        $estados = helpers::orderStatus();

        $currency = CompanySettings::co_currency(0);

        $data = Order::select('orders.id', 'orders.name', 'orders.order_num', 'orders.order_old_num', 'orders.purchase_num', 'orders.purchase_old_num', 'orders.estimated_delivery_date', 'orders.delivery_date', 'orders.order_status', 'orders.invoiced', 'orders.discount', 'order_previsions.year', 'order_previsions.month', 'order_previsions.percent', 'orders.created_at', 'companies.name AS Provider', 'users.name AS nombre', 'users.surname', 'cc.name AS finalCustomer', 'u_.name AS CustomerManagerName', 'u_.surname AS CustomerManagerSurname', 'business_areas.name AS businessArea', 'workplaces.name AS workplace')
        ->join('order_previsions', 'orders.id', '=', 'order_previsions.order_id')
        ->join('companies', 'orders.provider_id', '=', 'companies.id')
        ->leftJoin('companies AS cc', 'orders.final_customer_id', '=', 'cc.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->leftJoin('users AS u_', 'orders.customer_manager_id', '=', 'u_.id')
        ->leftJoin('business_areas', 'orders.customer_business_area_id', '=', 'business_areas.id')
        ->leftJoin('workplaces', 'orders.customer_workplace_id', '=', 'workplaces.id')
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
        ->filterColumn('fecha', function($q, $keyword){
            $sql = "order_previsions.year like ?";
            $q->whereRaw($sql, ["%$keyword%"]);
        })
        ->addColumn('provider', function($row){
            return $row->Provider;
        })
        // ->addColumn('business_area', function($row){
        //     return $row->businessArea;
        // })
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
        ->addColumn('manager', function($row){
            if($row->nombre){
                return $row->nombre.' '.$row->surname;
            }else{
                return '';
            }
        })
        ->addColumn('workplace', function($row){
            return $row->workplace;
        })
        ->addColumn('finalCustomer', function($row){
            return $row->finalCustomer;
        })
        ->addColumn('amount', function($row){
            $amount = $row->getAmount();
            return number_format($amount['total_amount'], 2, '.', '');
        })  
        ->addColumn('a_facturar', function($row){ 
            $amount = $row->getAmount();
            if($row->percent && $row->percent < 100){
                $a_facturar = helpers::percent($amount['total_amount'], $row->percent);  
                return number_format($a_facturar, 2, '.', '');
            }else{
                return number_format($amount['total_amount'], 2, '.', '');
            }    
        })
        ->addColumn('invoice_amount', function($row){ 
            return Invoice::amountByOrder($row->id);
        })
        ->addColumn('estado', function($row) use($estados){ 
            if(isset($estados[$row->order_status])){
                return ucfirst($estados[$row->order_status]);
            }else{
                return '';
            }
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

        //Eliminamos compra del proyecto:
        $purchase->delete();

        if(!$project || !$purchase_){
            $alert = trans('textos.compra_proyecto_no_valida');
            return redirect()->route('orders.index')->with(compact('alert'));    
        }else{
            $order_id = $purchase->order_id;
            $project_id = $purchase->project_id;

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

        //09/04/22: aquí se debería incluir la inicialización de picking para pedidos que lo requieran y si el módulo respectivo se encuentra activo.

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
            //Si viene declarada una plantilla distinta a la original:
            if($request->template_id != $order->order_template_id){
                $new_template = OrderSettings::find($request->template_id);

                $template = [
                    'conditions' => $new_template->conditions, 
                    'payment_method' => $new_template->payment_method,
                    'observations' => $new_template->observations,
                    'footer_legend' => $new_template->footer_legend    
                ];

            }else{
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

        //Calculamos ambos tipos de descuento a partir del importe total:
        $budget_amount = $order->itemsBudgetOrderAmount(true);

        //Descuento neto
        if($request->discount_amount){
            $discount_amount = $request->discount_amount;
            $discount = $budget_amount > 0? round((($request->discount_amount * 100) / $budget_amount), 2):'0';

        }elseif($request->discount_percent){
            $discount_amount = helpers::percent($budget_amount, $request->discount_percent);
            $discount = $request->discount_percent;
        }

        //$order->discount = $discount;
        //$order->discount_amount = $discount_amount;
        $order->save();

        //Actualizamos el descuento en cada línea del presupuesto:
        Item::leftJoin('products', 'items.product_id', '=', 'products.id')
        ->where('items.order_id', $order->id)
        ->whereNull('products.calculated_by_percent')
        ->where('items.price', '>', 0)
        ->update(['items.discount' => $discount]);

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
        //Eliminamos descuento sobre cada línea del presupuesto.
        Item::where('order_id', $order->id)
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
     * 18.2. Eliminar descuento sobre todas las líneas del pedido.
     */
    public function deleteDiscount(Request $request, $order_id){
        if(!$order_id || !is_numeric($order_id)){
            $alert = trans('textos.descuento_deleted_error');
            return redirect()->route('orders.index')->with(compact('alert'));
        }

        Item::where('order_id', $order_id)
        ->update(['discount' => NULL]);

        $msg = trans('textos.descuento_deleted_ok');
        return redirect()->route('orders.edit', [$order_id, 'items'])->with(compact('msg'));
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
        $order->customer_confirmation = NULL;
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

        $template = $order->order_template? unserialize($order->order_template):false;

        //Customizando CIF, ponemos espacio en blanco:
        //$company->nif = helpers::setCif($company->nif);

        $owner = User::select('name', 'surname')->find($order->provider_manager_id);

        $fechaLiteral = helpers::fechaLiteral($order->created_at, session()->get('locale'));

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
        if($order->customer_workplace_id){
            $customer_workplace = Workplace::select('workplaces.*', 'towns.name AS Town')
            ->join('towns', 'workplaces.town_id', '=', 'towns.id')
            ->where('workplaces.id', $order->customer_workplace_id)
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
        $currency = CompanySettings::co_currency(0);

        //Título .pdf:
        $pdf_title = $order->order_num? helpers::slugFiles($order->order_num, '_', false):$order->id;

        $pdf = PDF::loadView('pdf/order', compact('order', 'lines', 'provider', 'customer', 'owner', 'template', 'fechaLiteral', 'company_settings', 'provider_workplace', 'provider_phones', 'customer_workplace', 'provider_manager', 'customer_manager', 'currency'));
        return $pdf->download($pdf_title.'.pdf'); 
    }

    /**
     * 22. Añadir miembro a equipo.
     */
    public function addTeamMember(Request $request, Order $order){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $modal = $request->environment == 'customer'? 'modal-add-member-customer':'modal-add-member-provider';
            $validator->getMessageBag()->add('accion', $modal);

            return redirect('admin/orders/'.$order->id.'/edit/teams')
                ->withErrors($validator)
                ->withInput();
        }

        $team = $request->environment == 'customer'? unserialize($order->customer_team):unserialize($order->provider_team);

        if(!$team){
            $team = [(int)$request->member];
        }else{
            //Comprobamos que no exista en el array:
            if(!in_array($request->member, $team)){
                array_push($team, (int)$request->member);
            }    
        }

        if($request->environment == 'customer'){
            $order->customer_team = serialize($team);
        }else{
            $order->provider_team = serialize($team);    
        }
        $order->save();
        
        $msg = trans('textos.miembro_creado');
        return redirect()->route('orders.edit', [$order, 'teams'])->with(compact('msg'));
    }

    /**
     * 23. Eliminar miembro de equipo.
     */
    public function removeTeamMember(Request $request, Order $order){
        $team = $request->environment == 'customer'? unserialize($order->customer_team):unserialize($order->provider_team);
        $new_team = [];
        foreach($team as $t){
            if($t != $request->member){
                array_push($new_team, $t);    
            }
        }

        if($request->environment == 'customer'){
            $order->customer_team = count($new_team)? serialize($new_team):NULL;
        }else{
            $order->provider_team = count($new_team)? serialize($new_team):NULL;    
        }
        $order->save();
        
        $msg = trans('textos.miembro_deleted');
        return redirect()->route('orders.edit', [$order, 'teams'])->with(compact('msg'));
    }

    /**
     * 24. Pedidos según proveedor.
     *
     * Pedidos no facturados.
     */
    public function aboutProviders(Request $request, $provider_id){
        if($request->ajax()){ 
            $data = Order::select('id', 'purchase_num', 'purchase_old_num', 'name')
            ->where('is_order', 1)
            ->where('provider_id', $provider_id)
            ->where('customer_id', session('currentCompany'))
            ->where('invoiced', false)
            ->orderBy('purchase_num')
            ->orderBy('purchase_old_num')
            ->get();

            return response()->json($data);
        }     
    }

    /**
     * 25. Exportar .xls pedidos por rango de fecha.
     */
    public function exportOrdersByDateRange(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-set-report');

            return redirect('admin/orders/list/'.$request->customer_id.'/'.$request->provider_id)
                ->withErrors($validator)
                ->withInput();
        }

        //Eliminamos registros de la tabla temporal anteriores a la última hora:
        TempExportOrders::where('created_at', '<', Carbon::now()->subHours(1))->delete();

        $from = $request->from != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->from):$request->from:NULL;
        $to = $request->to != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->to):$request->to:NULL;

        //Pasamos los datos a una tabla temporal:
        $tempData = Item::select('orders.created_at', 
            'orders.delivery_date',
            'orders.estimated_delivery_date',
            'orders.order_num', 
            'orders.order_old_num', 
            'orders.name AS Order',
            'orders.order_status',
            'users.name', 
            'users.surname',
            'companies.name AS Company',
            'orders.customer_team',
            'business_areas.name AS Ba',
            'cost_centers.name AS CostCenter',
            'products.name AS Product', 
            'items.price', 
            'items.qty',
            'items.discount',
            DB::raw("(items.qty * items.price) AS amount")
        )
        ->join('orders', 'items.order_id', '=', 'orders.id')
        ->leftJoin('users', 'orders.provider_manager_id', '=', 'users.id')
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->leftJoin('business_areas', 'orders.provider_business_area_id', '=', 'business_areas.id')
        ->leftJoin('cost_centers', 'orders.provider_cost_center_id', '=', 'cost_centers.id')
        ->join('products', 'items.product_id', '=', 'products.id')
        ->whereBetween('orders.delivery_date', [$from, $to])
        ->where('orders.customer_id', $request->customer_id)
        ->where('orders.provider_id', $request->provider_id)
        ->get();

        //Pasamos la info ya customizada a una tabla temporal:
        $temp_num = $request->customer_id.$request->provider_id.time();
        foreach($tempData as $td){
            //Fecha customizada:
            $fecha = $td->created_at? $td->created_at->format($this->languages[session()->get('locale')][4]):'';

            //10/11/20: se cambia la fecha de creación por la fecha de entrega del pedido:
            //$fecha = $td->delivery_date && $td->delivery_date != config('constants.DATE_NULL_')? Carbon::parse($td->delivery_date)->format(config('locale.languages.'.config('app.locale'))[4]):'';

            //19/01/21: se solicita que aparezcan tres columnas de fechas: creación pedido, fecha prevista entrega, fecha real de entrega.
            $delivery_date = $td->delivery_date && $td->delivery_date != config('constants.DATE_NULL_')? Carbon::parse($td->delivery_date)->format($this->languages[session()->get('locale')][4]):'';

            $fecha_prevista = $td->estimated_delivery_date && $td->estimated_delivery_date != config('constants.DATE_NULL_')? Carbon::parse($td->estimated_delivery_date)->format($this->languages[session()->get('locale')][4]):'';

            //Manager cliente:
            $arr_customer_manager = $td->customer_team? unserialize($td->customer_team):false;

            if(is_array($arr_customer_manager) && count($arr_customer_manager) > 0){
                $cm = $arr_customer_manager[0];
                if(substr(rtrim($cm), -1) == "|"){
                    $cm_id = substr($cm, 0, -1);
                }else{
                    $cm_id = $cm;
                }
                $cm_data = User::select('name', 'surname')->find($cm_id);
                $customer_manager = $cm_data? $cm_data->name.' '.$cm_data->surname:'';
            }else{
                $customer_manager = ''; 
            }
            
            //Status:
            $status = $td->status? Helpers::orderStatus($td->status):'';

            //Descuento en el producto:
            if($td->discount && $td->discount > 0){
                $percentage = helpers::percent($td->amount, $td->discount); 
                $amount = $td->amount - $percentage;    
            }else{
                $discount = 0;
                $amount = $td->amount? $td->amount:'0';
            }

            $toe = new TempExportOrders();
            $toe->temp_num = $temp_num;
            $toe->order_date = $fecha;
            $toe->estimated_date = $fecha_prevista;
            $toe->delivery_date = $delivery_date;
            $toe->order_num = $td->order_num;
            $toe->order_old = $td->old_order;
            $toe->order_title = $td->Order;
            $toe->order_status = $status;
            $toe->order_provider_manager = $td->name.' '.$td->surname;
            $toe->order_customer = $td->Company;
            $toe->order_customer_manager = $customer_manager;
            $toe->order_business_area = $td->Ba;
            $toe->order_cost_center = $td->CostCenter;
            $toe->order_tipology = $td->Product;
            $toe->order_price = $td->price? $td->price:'0';
            $toe->order_qty = $td->qty;
            $toe->order_discount = $discount;
            $toe->order_amount = $amount;
            $toe->save();
        }

        return (new OrdersExport($temp_num))->download('orders.xlsx');
    }

    /**
     * 26. Pedidos para autocomplete.
     */
    public function searchOrders(Request $request) {
        $search = $request->search;

        $data = Order::select('id', 'name', 'order_num', 'order_old_num')  
        ->where('is_order', 1)
        ->where('provider_id', session('currentCompany'))
        ->where(function($q) use ($search){  
            $q->where('order_num', 'like', '%'.$search.'%') 
            ->orWhere('order_old_num', 'like', '%'.$search.'%')   
            ->orWhere('name', 'like', '%'.$search.'%');
        })
        ->orderBy('name', 'ASC')
        ->limit(15)
        ->get(); 

        return response()->json($data);
    }

    /**
     * 26.1. Pedidos de compra para autocomplete.
     */
    public function searchPurchases(Request $request) {
        $search = $request->search;

        $data = Order::select('id', 'name', 'purchase_num', 'purchase_old_num')  
        ->where('is_order', 1)
        ->where('customer_id', session('currentCompany'))
        ->where(function($q) use ($search){  
            $q->where('purchase_num', 'like', '%'.$search.'%') 
            ->orWhere('purchase_old_num', 'like', '%'.$search.'%')   
            ->orWhere('name', 'like', '%'.$search.'%');
        })
        ->orderBy('name', 'ASC')
        ->limit(15)
        ->get(); 

        return response()->json($data);
    }

    /**
     * 27. Cerrar pedido.
     */
    public function close(Order $order){
        //Eliminamos las previsiones no facturadas.
        OrderPrevisions::where('order_id', $order->id)
        ->whereNull('invoice_id')
        ->delete();

        //Bloqueamos pedido:
        $order->blocked = 1;
        $order->save();

        $msg = trans('textos.pedido_cerrado');
        return redirect()->route('orders.edit', $order)->with(compact('msg'));
    }

    /**
     * 28. Recálculo previsiones en pedidos pendientes.
     */
    public function resetPrevisions(){
        if(env('RESET_PREV_') == true){
            $data = Order::select('id')
            ->where('is_order', 1)
            ->whereIn('provider_id', [3,4,8,202,227,275])
            ->where('created_at', '>', '2023-01-01')
            ->get();

            foreach($data as $r){
                $arr_amounts = $r->getAmount();    

                OrderPrevisions::previsions($r->id, $arr_amounts['total_amount']);
            }
        }
    }
    
}
