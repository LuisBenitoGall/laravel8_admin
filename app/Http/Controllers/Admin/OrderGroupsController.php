<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Budget;
use App\Models\BudgetOrderSharedFields;
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderGroupOrders;
use App\Models\OrderGroups;
use App\Models\OrderPrevisions;
use App\Models\User;

class OrderGroupsController extends Controller{
    /**
     * 1. Listado de agrupaciones de pedidos.
     * 1.1. Lista de agrupaciones por cliente.
     * 1.2. Listado de agrupaciones por cliente para DataTable.
     * 2. Formulario nueva agrupación.
     * 3. Guardar nueva agrupación.
     * 4. Editar agrupación.
     * 5. Actualizar agrupación.
     * 6. Eliminar agrupación.
     * 7. Actualizar estado de la agrupación.
     */ 
    
    private $option = 'pedidos';
    private $module = 'orders';
    private $languages;   

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:orders');
        $this->middleware('order_groups_get', ['only' => ['edit']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación index:
        if(isset($data['submit_search'])){
            return Validator::make($data, [
                'customer_id' => 'nullable|integer'
            ]);

        //Validación guardar:
        }elseif(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'customer_id' => 'required|integer',
                'bag_amount' => 'nullable|numeric'
            ]);

        //Validación actualizar:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'status' => 'nullable|integer',
                'pay_order' => 'nullable|max:100'
            ]);
        }
    }

    /**
     * 1. Listado de agrupaciones de pedidos.
     */
    public function index($customer_id = false, $year = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agrupaciones',
            'module' => $this->module,
            'slug' => 'order-groups'
        ]; 

        $year = $year? $year:date('Y');
        $customer = $customer_id? Company::find($customer_id):false;
        $provider = Company::find(session('currentCompany'));

        //Comprobamos si la empresa hace gestión de clientes:
        if(session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }else{
            $customers = false;
        }

        return view('admin.order_groups.index')->with(compact('header', 'year', 'customer', 'provider', 'customers'));
    }

    /**
     * 1.1. Lista de agrupaciones por cliente.
     */
    public function getGroupsBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/order-groups')
                ->withErrors($validator)
                ->withInput();
        }

        //Si viene declarado el número de agrupación redirigimos:
        if($request->og_id){
            //Comprobamos si el valor recibido corresponde a id:
            $og = OrderGroups::select('id')->find($request->og_id);

            if(!$og){
                $alert = trans('textos.agrupacion_no_existe');
                return redirect()->route('order-groups.index')->with(compact('alert'));   
                exit;
            }else{
                return redirect()->route('order-groups.edit', $og->id);    
                exit;   
            }
        }

        return redirect()->route('order-groups.index', [$request->customer_id, $request->year]);
    }

    /**
     * 1.2. Listado de agrupaciones por cliente para DataTable.
     */
    public function getGroupsDT($customer = false, $year = false){
        if(!$customer || !$year){
            return false;
            exit;
        }

        $groups = OrderGroups::where('customer_id', $customer)
        ->where('provider_id', session('currentCompany'))
        ->whereYear('created_at', '=', $year)
        ->get();

        $arrEstados = Helpers::orderGroupStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        //Permisos del usuario:
        $editar = Auth::user()->can('orders.edit');
        $eliminar = Auth::user()->can('orders.destroy');

        //Ampliación de información de los objetos:
        if($groups){
            //Comprobamos si tiene activo el módulo de contabilidad para poder facturar el pedido:
            $invoice = $this->settedModule('contabilidad');

            foreach($groups as $row){
                $row->alta = $row->created_at->format('d/m/Y');

                //Estado del pedido:
                $row->estado = $row->status? ucfirst($arrEstados[$row->status]):'';
                $row->estados = $row->status.';'.$strEstados;

                //Agrupación abierta:
                $row->opened = $row->status == config('constants.ORDER_GROUPS_OPENED_')? true:false;

                //Facturación de pedido. El pedido debe estar en algún status que permita su facturación:
                $row->invoice = in_array($row->status, config('constants.ORDER_STATUS_INVOICED_'))? $invoice:false;

                //Permisos para las acciones:
                $row->editar = $editar;
                $row->eliminar = $eliminar;
            }  
        }

        return datatables($groups)->toJson();
    }

    /**
     * 2. Formulario nueva agrupación.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agrupacion_nueva',
            'module' => $this->module,
            'slug' => 'order-groups'
        ]; 

        //Comprobamos si la empresa hace gestión de clientes:
        if(session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }else{
            $customers = false;
        }   

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return view('admin.order_groups.create')->with(compact('header', 'customers', 'currency')); 
    }

    /**
     * 3. Guardar nueva agrupación.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/order-groups/create')
                ->withErrors($validator)
                ->withInput();
        }    

        $group = new OrderGroups();
        $group->name = $request->name;
        $group->customer_id = $request->customer_id;
        $group->provider_id = session('currentCompany');
        $group->bag_amount = $request->bag_amount;
        $group->status = config('constants.ORDER_GROUPS_OPENED_');
        $group->user_id = Auth::user()->id;
        $group->save();

        $msg = trans('textos.agrupacion_creada');
        return redirect()->route('order-groups.edit', $group->id)->with(compact('msg'));
    }

    /**
     * 4. Editar agrupación.
     */
    public function edit(OrderGroups $group, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.agrupacion_editar',
            'module' => $this->module,
            'slug' => 'order-groups'
        ]; 

        $customer = Company::find($group->customer_id);
        $provider = Company::find($group->provider_id);
        $author = User::find($group->user_id);

        //Presupuesto:
        $budget = false;
        if($group->budget_id){
            $budget = Budget::find($group->budget_id);
        }

        //Pedidos de la agrupación:
        $orders = Order::getGroupOrders($group->id);

        //Ampliación de la info:
        foreach($orders as $order){
            //Importe por pedido:
            $shared = BudgetOrderSharedFields::select('id', 'discount')
            ->where('order_id', $order->id)
            ->first();

            $order->amount = $shared->getAmount();
        }


        //Pedidos sin agrupación, sólo los pedidos pendientes de facturación que no pertenezcan a ninguna otra agrupación. ¡Atención! a los pasos a seguir y a la sintaxis para obtener los pedidos que no se encuentran en la tabla order_group_orders ni en order_previsions:
        /*$ordersInGroup = OrderGroupsOrders::pluck('order_id')->all();
        $ordersInPrevisions = OrderPrevisions::join('orders', 'order_previsions.order_id', '=', 'orders.id')
        ->where('orders.customer_id', $group->customer_id)
        ->where('orders.provider_id', session('currentCompany'))
        ->where('order_previsions.invoiced', 1)->pluck('order_previsions.order_id')->all();
        
        $orders_off = Order::select('id', 'order_num', 'old_order', 'name', 'delivery_date')
        ->whereNotIn('id', $ordersInGroup)
        ->whereNotIn('id', $ordersInPrevisions)
        ->whereNull('deleted_at')
        ->where('customer_id', $group->customer_id) 
        ->where('provider_id', session('currentCompany'))
        ->where('invoiced', 0)
        ->get(); */

        //01-03-21: Se modifica la lógica para que devuelva previsiones de facturación de un pedido en lugar del pedido, pues uno de éstos puede tener más de 1 previsión de facturación. Se descartan los pedidos en otras agrupaciones y en otras bolsas.
        $ordersInGroup = OrderGroupOrders::join('orders', 'order_group_orders.order_id', '=', 'orders.id')
        ->join('budget_order_shared_fields', 'orders.id', '=', 'budget_order_shared_fields.order_id')
        ->where('budget_order_shared_fields.customer_id', $group->customer_id) 
        ->where('budget_order_shared_fields.provider_id', session('currentCompany'))
        ->pluck('order_group_orders.order_id')
        ->all();

        $ordersInBudgets = BudgetOrderSharedFields::join('budgets', 'budget_order_shared_fields.budget_id', '=', 'budgets.id')
        ->where('budgets.modality', config('constants.BUDGET_FREE_MODALITY_'))
        ->pluck('budget_order_shared_fields.order_id')
        ->all();

        $orders_off = OrderPrevisions::select('orders.order_num', 'orders.order_old_num', 'orders.name', 'orders.delivery_date', 'order_previsions.id', 'order_previsions.order_id', 'order_previsions.year', 'order_previsions.month', 'order_previsions.percent')
        ->join('orders', 'order_previsions.order_id', '=', 'orders.id')
        ->leftJoin('budget_order_shared_fields', 'orders.id', '=', 'budget_order_shared_fields.order_id')
        ->whereNotIn('orders.id', $ordersInGroup)
        ->whereNotIn('orders.id', $ordersInBudgets)
        ->where('budget_order_shared_fields.customer_id', $group->customer_id)
        ->where('budget_order_shared_fields.provider_id', session('currentCompany'))
        ->whereNull('order_previsions.invoice_id')
        ->whereNull('orders.deleted_at')
        ->orderBy('orders.order_num', 'ASC')
        ->get();

        //Facturas de la agrupación:
        //$invoices = Invoice::select('id', 'refund', 'total_amount', 'discount_amount')->where('og_id', $group->id)->get();
        $invoices = [];

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        $languages = $this->languages;

        //Módulos activos:
        $module_accounting = $this->settedModule('accounting');

        return view('admin.order_groups.edit')->with(compact('header', 'group', 'tab', 'customer', 'provider', 'author', 'budget', 'orders', 'invoices', 'orders_off', 'currency', 'languages', 'module_accounting'));
    }

    /**
     * 5. Actualizar agrupación.
     */
    public function update(Request $request, OrderGroups $group){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/order-groups/'.$group->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        $group->name = $request->name;
        //La fecha de orden de pago recibida sólo se actualiza cuando se recibe por primera vez la orden o es cambiada.
        if($group->pay_order != $request->pay_order){
            $group->pay_order_received = NOW();
        }
        $group->pay_order = $request->pay_order;
        $group->status = $request->status;
        $group->save();        

        $msg = trans('textos.agrupacion_updated');
        return redirect()->route('order-groups.edit', $group->id)->with(compact('msg'));     
    }

    /**
     * 6. Eliminar agrupación.
     */
    public function destroy(OrderGroups $group){
        $customer_id = $group->customer_id;

        //Eliminamos todos los pedidos vinculados con este grupo:
        OrderGroupOrders::where('og_id', $group->id)->delete();

        $group->delete();

        $msg = trans('textos.agrupacion_pedidos_deleted');
        return redirect()->route('order-groups.index', $customer_id)->with(compact('msg'));
    }
    
    /**
     * 7. Actualizar estado de la agrupación.
     */
    public function status(Request $request, OrderGroups $group, $status){
        if($request->ajax()){       
            $group->status = $status;
            $group->save();

            return response()->json(true);
        }
    }
}
