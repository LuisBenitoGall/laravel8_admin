<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\CustomerProviders;
use App\Models\Delivery;
use App\Models\DeliveryItems;
use App\Models\DeliveryOrders;
use App\Models\DeliveryPatterns;
use App\Models\Item;
use App\Models\Order;

class DeliveryController extends Controller{
    /**
     * 1. Entregas para cliente.
     * 1.1. Filtro de albaranes por cliente o proveedor.
     * 1.2. Listado de entregas para DataTable.
     * 1.3. Listado de pedidos para incluir en entregas para DataTable.
     * 2. Formulario nueva entrega.
     * 2.1. Generar albarán desde pedido.
     * 3. Guardar nueva entrega.
     * 4. Editar entrega.
     * 5. Actualizar entrega.
     * 6. Eliminar entrega.
     * 7. Actualizar estado de entrega.
     * 8. Resetear numeración de la entrega.
     */
    
    private $option = 'entregas';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:logistics');
        $this->middleware('delivery_get', ['only' => ['edit', 'update', 'destroy', 'resetNum']]);
        $this->middleware('order_get', ['only' => ['deliveryFromOrder']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación index:
        if(isset($data['submit_filter'])){
            return Validator::make($data, [
                'year' => 'nullable|integer',
                'customer_id' => 'nullable',
            ]);

        //Validación create:
        }elseif(isset($data['submit_save'])){
            return Validator::make($data, [
                'company_id' => 'required|integer'
            ]);
        }
    }

    /**
     * 1. Entregas para cliente.
     */
    public function index($year = false, $customer_id = false, $status = false, $tab = false){
        //Si se cambia la empresa en sesión se toman los albaranes relativos a ésta:
        if($customer_id 
            && $customer_id != session('currentCompany') 
            && $customer_id != 'all'
            && $provider_id 
            && $provider_id != session('currentCompany')
            && $provider_id != 'all'
        ){
            return redirect()->route('deliveries.index');
        }

        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.listado',
            'module' => $this->module,
            'slug' => 'deliveries'
        ]; 

        $customer = $customer_id && $customer_id != 'all'? Company::find($customer_id):false;

        //Comprobamos si la empresa hace gestión de clientes:
        $customers = false;
        if(session('companySettings') && session('companySettings')['customers_management'] == 1){
            $customers = CustomerProviders::getCompanyCustomers(session('currentCompany')); 
        }

        $year = $year? $year:Date('Y');

        //Estados de albarán:
        $delivery_status = helpers::deliveryStatus();

        //Módulos activos:
        $module_logistics = $this->settedModule('logistics');

        $languages = $this->languages;

        return view('admin.deliveries.index')->with(compact('header', 'year', 'customer', 'status', 'tab', 'customers', 'delivery_status', 'module_logistics', 'languages'));
    }

    /**
     * 1.1. Filtro de albaranes por cliente o proveedor.
     */
    public function getDeliveriesBy(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/deliveries/list')
                ->withErrors($validator)
                ->withInput();
        }  

        $customer_id = $request->customer_id? $request->customer_id:'all';

        //Estado:
        $status = $request->status? $request->status:'0';
        
        return redirect()->route('deliveries.index', [$request->year, $customer_id, $status]);  
    }

    /**
     * 1.2. Listado de entregas para DataTable.
     */
    public function getDeliveriesDT(Request $request, $year = false, $customer = false, $status = false){
        $data = Delivery::select('deliveries.id', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.status', 'companies.name', 'deliveries.created_at')
        ->join('companies', 'deliveries.customer_id', '=', 'companies.id')
        ->where('provider_id', session('currentCompany'));

        $estados = helpers::deliveryStatus();

        //Moneda:
        $currency = CompanySettings::co_currency(0);

        return DataTables::of($data)
        ->addColumn('num', function($row){
            $num = $row->delivery_num? $row->delivery_num:($row->delivery_old_num? $row->delivery_old_num:$row->id);
            return '<a href="/admin/deliveries/'.$row->id.'/edit">'.$num.'</a>';
        })
        ->addColumn('amount', function($row) use($currency){
            $amount = $row->getDeliveryAmount();
            return $amount.$currency->symbol;
        })
        ->addColumn('date', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('status', function($row) use($estados){
            return view('admin.projects.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function($row) use($estados){
            return view('admin.deliveries.partials.dt-actions', compact('row', 'estados')); 
        })
        ->rawColumns(['num'])
        ->make(true);
    }

    /**
     * 1.3. Listado de pedidos para incluir en entregas para DataTable.
     */
    public function getDeliveriesOrdersDT(){
        $orders = Order::select('orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.name', 'companies.name AS company', 'orders.customer_id') 
        ->join('companies', 'orders.customer_id', '=', 'companies.id')
        ->where('orders.provider_id', session('currentCompany')) 
        ->where('orders.prepare_delivery', 1)
        ->whereNotIn('orders.id', function($query){
            $query->select('order_id')->from('delivery_orders');
        })
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('orders.edit');
        $eliminar = Auth::user()->can('deliveries.edit');   //Eliminar en este caso se refiere a...

        //Status válidos para incluir nuevos pedidos:
        $status_on = [1,10];

        foreach($orders as $row){
            //Entregas posibles:
            $row->deliveries = Delivery::select('id', 'delivery_num', 'delivery_old_num')
            ->where('customer_id', $row->customer_id)
            ->where('provider_id', session('currentCompany'))
            ->whereIn('status', $status_on)
            ->get()
            ->toArray();

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($orders)->toJson();
    }

    /**
     * 2. Formulario nueva entrega.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.entrega_nueva',
            'module' => $this->module,
            'slug' => 'deliveries'
        ]; 

        $companies = CustomerProviders::join('companies', 'customer_providers.customer_id', '=', 'companies.id')
            ->where('customer_providers.provider_id', session('currentCompany'))
            ->orderBy('companies.name', 'ASC')
            ->pluck('companies.name', 'companies.id')
            ->toArray();

        return view('admin.deliveries.create')->with(compact('header', 'companies'));         
    }

    /**
     * 2.1. Generar albarán desde pedido.
     */
    public function deliveryFromOrder(Order $order){
        //Generar albarán:
        $dlv = new Delivery();
        $dlv->provider_id = $order->provider_id;
        $dlv->customer_id = $order->customer_id;
        $dlv->user_id = Auth::user()->id;
        $dlv->status = config('constants.DELIVERY_STATUS_PREPARATION_');
        $dlv->save();

        //Numeración de albarán:
        DeliveryPatterns::setDeliveryNum($dlv, $order);

        //Vincular con pedido:
        DeliveryOrders::create([
            'delivery_id' => $dlv->id,
            'order_id' => $order->id
        ]);

        //Líneas de albarán:
        $items = Item::select('id', 'product_id', 'qty')->where('order_id', $order->id)->get();
        foreach($items as $i){
            $data = [
                'delivery_id' => $dlv->id,
                'item_id' => $i->id,
                'qty' => $i->qty,
                'product_id' => $i->product_id? $i->product_id:false
            ];
            DeliveryItems::storeItem($data);
        }

        $msg = trans('textos.entrega_creada');
        return redirect()->route('deliveries.edit', $dlv)->with(compact('msg'));   
    }

    /**
     * 3. Guardar nueva entrega.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/deliveries/create')
                ->withErrors($validator)
                ->withInput();
        }   

        $dlv = new Delivery();
        $dlv->provider_id = session('currentCompany');
        $dlv->customer_id = $request->company_id;
        $dlv->user_id = Auth::user()->id;
        $dlv->save();

        $msg = trans('textos.entrega_creada');
        return redirect()->route('deliveries.edit', $dlv)->with(compact('msg')); 
    }

    /**
     * 4. Editar entrega.
     */
    public function edit(Delivery $delivery, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.entrega_editar',
            'module' => $this->module,
            'slug' => 'deliveries-edit'
        ]; 

        //Cliente:
        $customer = Company::find($delivery->customer_id);

        //Patrones de numeración de entregas:
        $patterns = false;
        if(!$delivery->pattern_id){
            $patterns = DeliveryPatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Estados de entrega:
        $status = Helpers::deliveryStatus();

        //Pedidos del cliente:
        //1. Sólo pedidos validados para entregar (prepare_delivery = true)
        $orders = Order::select('orders.id', 'orders.order_num', 'orders.name')
        ->where('orders.provider_id', session('currentCompany'))
        ->where('orders.customer_id', $delivery->customer_id)
        ->where('orders.prepare_delivery', true)
        ->get();

        //Pedidos de la entrega:
        $delivery_orders = DeliveryOrders::select('delivery_orders.id', 'orders.id AS orderId', 'orders.order_num', 'orders.name')
        ->join('orders', 'delivery_orders.order_id', '=', 'orders.id')
        ->where('delivery_orders.delivery_id', $delivery->id)
        ->get();

        //Array pedidos de la entrega:
        $arr_delivery_orders = [];
        foreach($delivery_orders as $do){
            array_push($arr_delivery_orders, $do->id);
        }

        $languages = $this->languages;

        return view('admin.deliveries.edit')->with(compact('header', 'delivery', 'tab', 'customer', 'patterns', 'status', 'orders', 'delivery_orders', 'arr_delivery_orders', 'languages'));
    }

    /**
     * 5. Actualizar entrega.
     */
    public function update(Request $request, Delivery $delivery){
        //Número de pedido:
        $delivery_num = NULL;
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('delivery', $request->pattern_id);
        }   

        //Formateando fechas:
        

        if($request->pattern_id){
            if(!empty($patternNum)){
                $delivery->delivery_num = $patternNum['pattern_num'];
                $delivery->num_cardinal = $patternNum['num']; 
            }  
            $delivery->pattern_id = $request->pattern_id;
        }          
        $delivery->delivery_old_num = $request->delivery_old_num;
        $delivery->observations = $request->observations;
        $delivery->status = $request->status;
        $delivery->save();

        $msg = trans('textos.entrega_updated');
        return redirect()->route('deliveries.edit', $delivery)->with(compact('msg'));
    }

    /**
     * 6. Eliminar entrega.
     */
    public function destroy(Delivery $delivery){
        $delivery->delete();
        
        $msg = trans('textos.entrega_deleted');
        return redirect()->route('deliveries.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado de entrega.
     */
    public function status(Request $request, Delivery $delivery, $status){
        if($request->ajax()){    
            $delivery->status = $status;
            $delivery->save();

            return response()->json(true);
        }
    }

    /**
     * 8. Resetear numeración de la entrega.
     */
    public function resetNum(Delivery $delivery){
        $delivery->delivery_num = NULL;
        $delivery->num_cardinal = NULL;
        $delivery->pattern_id = NULL;
        $delivery->save();

        $msg = trans('textos.numero_deleted');
        return redirect()->route('deliveries.edit', $delivery)->with(compact('msg'));
    }
}
