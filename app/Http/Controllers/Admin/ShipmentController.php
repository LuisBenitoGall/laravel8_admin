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
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use PDF;

//Models:
use App\Models\CompanySettings;
use App\Models\Dealer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentDealer;
use App\Models\ShipmentPatterns;
use App\Models\User;
use App\Models\Vehicle;

class ShipmentController extends Controller{
    /**
     * 1. Listado de envíos.
     * 1.1. Listado de envíos para DataTable.
     * 2. Formulario nuevo reparto.
     * 3. Editar reparto.
     * 4. Actualizar reparto.
     */
    
    private $option = 'envios';
    private $module = 'logistics';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:logistics');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación actualizar:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'status' => 'required|numeric'
            ]);
        }
    }

    /**
     * 1. Listado de envíos.
     */
    public function index($shipments_selec = false){
         $header = [
            'option' => $this->option, 
            'suboption' => 'textos.envios',
            'module' => $this->module,
            'slug' => 'shipments'
        ]; 

        $languages = $this->languages;

        $see_shipments = $shipments_selec ? $shipments_selec : config('constants.SHIPMENT_STATUS_OPENED_');

        $shipment_status = helpers::shipmentStatus();

        return view('admin.shipments.index')->with(compact('header', 'languages', 'see_shipments', 'shipment_status'));
    }

    /**
     * 1.1. Listado de envíos para DataTable.
     */
    public function getShipmentsDT($see_shipments){
        $data = Shipment::select('shipments.*', 'vehicles.name AS vehicle', 'vehicles.car_license', 'users.name AS nombre', 'users.surname')
        ->leftJoin('vehicles', 'shipments.vehicle_id', '=', 'vehicles.id')
        ->leftJoin('delivery_man', 'shipments.delivery_man_id', '=', 'delivery_man.id')
        ->leftJoin('users', 'delivery_man.user_id', '=', 'users.id')
        ->where('shipments.company_id', session('currentCompany'))
        ->where('shipments.status', $see_shipments)
        ->orderBy('shipments.scheduled_date', 'DESC');

        $estados = helpers::shipmentStatus();

        return DataTables::of($data)
        ->addColumn('num', function($row){
            $num = $row->shipment_num? $row->shipment_num:($row->shipment_old_num? $row->shipment_old_num:$row->id);
            return '<a href="/admin/shipments/'.$row->id.'/edit">'.$num.'</a>';
        })
        ->addColumn('vehicle', function($row){
            return $row->vehicle;
        })
        ->addColumn('car_license', function($row){
            return $row->car_license;
        })
        ->addColumn('delivery_man', function($row){
            return $row->nombre.' '.$row->surname;
        })
        ->addColumn('date', function($row){
            return Carbon::parse($row->scheduled_date)->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('estado', function($row) use($estados){
            return view('admin.shipments.partials.dt-status', compact('row', 'estados'));
        })
        ->addColumn('acciones', function($row) use($estados){
            $allow_show_button = false;
            return view('admin.shipments.partials.dt-actions', compact('row', 'estados', 'allow_show_button'));
        })
        ->rawColumns(['num'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo reparto.
     *
     * Se crea directamente el reparto y se lleva a edición.
     */
    public function create(){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.reparto_nuevo',
            'module' => $this->module,
            'slug' => 'shipments'
        ];

        $shipment = new Shipment();
        $shipment->company_id = session('currentCompany');
        $shipment->scheduled_date = Carbon::today()->addDays(1);
        $shipment->user_id = Auth::user()->id;
        $shipment->status = config('constants.SHIPMENT_STATUS_OPENED_');
        $shipment->save();

        //return view('admin.shipments.edit')->with(compact('header', 'shipment'));
        return redirect()->route('shipments.edit', $shipment);
    }

    /**
     * 3. Editar reparto.
     */
    public function edit(Shipment $shipment, $tab = false){
        $header = [
            'option' => $this->option,
            'suboption' => 'textos.reparto_editar',
            'module' => $this->module,
            'slug' => 'shipments'
        ];

        //Patrones de numeración de entregas:
        $patterns = false;
        if(!$shipment->pattern_id){
            $patterns = ShipmentPatterns::select('id', 'name', 'yearly_reset')
            ->where('company_id', session('currentCompany'))
            ->where('status', 1)
            ->get();
        }

        //Estados de reparto:
        $status = Helpers::shipmentStatus();

        //Vehículos:
        $vehicles = [];
        $vehicles_ = Vehicle::select('id', 'name', 'car_license')
        ->where('company_id', session('currentCompany'))
        ->where('status', 1)
        ->get();
        if($vehicles_->count()){
            foreach($vehicles_ as $v){
                $vehicles[$v->id] = $v->name.' - '.$v->car_license;
            }
        }

        //Repartidores:
        $dealers = Dealer::select(DB::raw("CONCAT(users.name, ' ', users.surname) as dealer"), 'dealers.id')
        ->join('users', 'dealers.user_id', '=', 'users.id')
        ->where('dealers.company_id', session('currentCompany'))
        ->orderBy('users.name', 'ASC')
        ->pluck('dealer', 'id')
        ->toArray();

        //Repartidores seleccionados:
        $selected_dealers = ShipmentDealer::where('shipment_id', $shipment->id)
        ->pluck('dealer_id')
        ->toArray();
       

        //Pedidos del reparto:
        //20/06/2023: se modifica la interfaz en la selección de pedidos. Ahora se mostrarán en un listado y se marcará con checkbox los pedidos seleccionados.
        // $orders_in = ShipmentOrders::select('shipment_orders.id AS soId', 'orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.delivery_date', 'orders.order_status', 'companies.name AS customer')
        // ->join('orders', 'shipment_orders.order_id', '=', 'orders.id')
        // ->join('companies', 'orders.customer_id', '=', 'companies.id')
        // ->where('shipment_orders.shipment_id', $shipment->id)
        // ->orderBy('orders.order_num', 'ASC')
        // ->get();
        
        // $orders_in = ShipmentOrders::where('shipment_orders.shipment_id', $shipment->id)
        // ->pluck('id')
        // ->toArray();
        
        $orders_in = false;

        //Pedidos sin reparto:
        //1. A entregar por Viudavila.
        //2. Order_Status = pendiente o parcial.
        // $orders_out = Order::select('orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.name', 'orders.delivery_date', 'orders.order_status', 'orders.created_at', 'companies.name as customer', DB::raw('(SELECT sum(i.qty * i.price) FROM items AS i WHERE i.order_id = orders.id) AS total_amount'))
        // ->join('companies', 'orders.customer_id', '=', 'companies.id')
        // //->doesntHave('shipments')     REVISAR ESTA CONDICIÓN PARA LOS PEDIDOS PARCIALES, PUES HAY QUE PREPARAR NUEVOS ALBARANES.
        // ->where('orders.provider_id', session('currentCompany'))
        // ->where('orders.is_order', 1)
        // ->where('orders.agency_id', config('constants.AGENCY_VIUDAVILA_'))
        // ->where('orders.draft', false)
        // ->whereIn('orders.order_status', [config('constants.ORDER_STATUS_PENDENT_'), config('constants.ORDER_STATUS_PARTIAL_')])
        // ->get();


        $orders_out = false;

        //Estados de pedido:
        $order_status = helpers::orderStatus();

        //Albaranes del reparto:
        // $deliveries_in = ShipmentDelivery::select('shipment_deliveries.id AS sdId', 'deliveries.id', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.send_date', 'deliveries.status', 'companies.name AS customer', 'orders.id as orderId', 'orders.order_num', 'orders.order_old_num')
        // ->join('deliveries', 'shipment_deliveries.delivery_id', '=', 'deliveries.id')
        // ->join('companies', 'deliveries.customer_id', '=', 'companies.id')
        // ->leftJoin('shipment_orders', 'shipment_deliveries.shipment_id', '=', 'shipment_orders.shipment_id')
        // ->leftJoin('orders', 'shipment_orders.order_id', '=', 'orders.id')
        // ->where('shipment_deliveries.shipment_id', $shipment->id)
        // ->orderBy('deliveries.delivery_num', 'ASC')
        // ->get();

        // $deliveries_in = Delivery::select('shipment_deliveries.id AS sdId', 'deliveries.id', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.send_date', 'deliveries.status', 'companies.name AS customer', 'orders.id as orderId', 'orders.order_num', 'orders.order_old_num')
        // ->join('delivery_orders', 'deliveries.id', '=', 'delivery_orders.delivery_id')
        // ->join('orders', 'delivery_orders.order_id', '=', 'orders.id')
        // ->join('shipment_deliveries', 'deliveries.id', '=', 'shipment_deliveries.delivery_id' )
        // ->join('companies', 'deliveries.customer_id', '=', 'companies.id')
        // ->leftJoin('shipment_orders', 'shipment_deliveries.shipment_id', '=', 'shipment_orders.shipment_id')
        // ->where('shipment_deliveries.shipment_id', $shipment->id)
        // ->whereNull('shipment_orders.deleted_at')
        // ->whereNull('shipment_deliveries.deleted_at')
        // ->groupBy('deliveries.id')
        // ->orderBy('deliveries.delivery_num', 'ASC')
        // ->get();
        //dd($deliveries_in);

        $deliveries_in = false;

        //Estados de entrega:
        $delivery_status = Helpers::deliveryStatus();

        //Albaranes sin reparto:
        $deliveries_out = Delivery::doesntHave('shipments')
        ->where('provider_id', session('currentCompany'))
        ->get();

        $author = User::select('name', 'surname')->find($shipment->user_id);

        $languages = $this->languages;

        $currency = $this->co_currency;

        return view('admin.shipments.edit')->with(compact('header', 'shipment', 'tab', 'patterns', 'status', 'vehicles', 'dealers', 'selected_dealers', 'orders_in', 'orders_out', 'order_status', 'deliveries_in', 'delivery_status', 'deliveries_out', 'author', 'languages', 'currency'));
    }

    /**
     * 4. Actualizar reparto.
     */
    public function update(Request $request, Shipment $shipment){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/shipments/'.$shipment->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Formateando fechas:
        $date = $request->date != '' ? (session()->get('locale') != 'en') ? helpers::convertFecha($request->date) : $request->date : NULL;

        //Número de pedido:
        $shipment_num = NULL;
        if($request->pattern_id){
            $patternNum = $this->setPatternNumber('shipment', $request->pattern_id);

            if(!empty($patternNum)){
                $shipment->shipment_num = $patternNum['pattern_num'];
                $shipment->num_cardinal = $patternNum['num'];
            }
            $shipment->pattern_id = $request->pattern_id;
        }
        $shipment->shipment_old_num = $request->shipment_old_num;
        $shipment->vehicle_id = $request->vehicle_id;
        $shipment->scheduled_date = $date;
        $shipment->observations = $request->observations;
        $shipment->status = $request->status;
        $shipment->save();

        //Repartidores:
        ShipmentDealer::where('shipment_id', $shipment->id)
        ->delete();
        foreach($request->dealers as $d){
            ShipmentDealer::create([
                'shipment_id' => $shipment->id,
                'dealer_id' => $d
            ]);    
        }

        $msg = trans('textos.reparto_updated');
        return redirect()->route('shipments.edit', $shipment)->with(compact('msg'));
    }
}
