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
use Carbon\Carbon;

//Models:
use App\Models\Delivery;
use App\Models\DeliveryOrders;
use App\Models\Order;

class ReceivingController extends Controller{
    /**
     * 1. Recepción de pedidos.
     * 1.1. Listado de recepciones por DataTable.
     * 2. Guardar nueva recepción de pedido.
     * 3. Editar recepción de pedido.
     */
    
    private $option = 'recepciones';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:orders');

        $this->middleware('order_get', ['only' => ['store']]);
        $this->middleware('receiving_get', ['only' => ['edit']]);
    }
    
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.recepciones_pendientes',
            'module' => $this->module,
            'slug' => 'receiving'
        ]; 

        return view('admin.receivings.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de recepciones por DataTable.
     */
    public function getReceivingsDT(){
        $data = Delivery::select('deliveries.id', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.origin', 'deliveries.send_date', 'deliveries.reception_date', 'deliveries.customer_observations', 'orders.id AS orderId', 'orders.purchase_num', 'orders.purchase_old_num', 'companies.name AS company')
        ->join('companies', 'deliveries.provider_id', '=', 'companies.id')
        ->leftJoin('delivery_orders', 'deliveries.id', '=', 'delivery_orders.delivery_id')
        ->leftJoin('orders', 'delivery_orders.order_id', '=', 'orders.id')
        ->where('deliveries.customer_id', session('currentCompany'))
        ->where('deliveries.status', '0')
        ->orderBy('deliveries.reception_date', 'ASC');

        return DataTables::of($data)   
        ->addColumn('reception_date', function($row){
            return Carbon::parse($row->reception_date)->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('provider', function($row){
            return $row->company;
        })
        ->addColumn('orderNum', function($row){
            $order = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:$row->orderId);
            return '<a target="_blank" href="'.route('orders.edit', $row->orderId).'">'.$order.'</a>';
        })
        ->addColumn('deliveryNum', function($row){
            if($row->delivery_num){
                return $row->delivery_num;
            }else{
                return '';
            }
        })
        ->addColumn('observations', function($row){
            return $row->customer_observations;
        })
        ->addColumn('acciones', function($row){
            return view('admin.receivings.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['orderNum'])
        ->make(true);
    }

    /**
     * 2. Guardar nueva recepción de pedido.
     */
    public function store(Request $request, Order $order){
        $reception_date = $request->reception_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->reception_date):$request->reception_date:NULL;

        $dlv = new Delivery();
        $dlv->origin = 'c';     //Item que genera el cliente.
        $dlv->provider_id = $order->provider_id;
        $dlv->customer_id = $order->customer_id;
        $dlv->reception_date = $reception_date;
        $dlv->customer_observations = $request->customer_observations;
        $dlv->user_id = Auth::user()->id;
        $dlv->save();

        //Guardamos vínculo pedido - entrega:
        $do = new DeliveryOrders();
        $do->delivery_id = $dlv->id;
        $do->order_id = $order->id;
        $do->save();

        $msg = trans('textos.recepcion_generada');
        return redirect()->route('orders.edit', [$order->id, false])->with(compact('msg'));
    }

    /**
     * 3. Editar recepción de pedido.
     */
    public function edit(Delivery $delivery){
        //Pedidos de la recepción:
        $orders = Order::select('orders.id')
        ->join('delivery_orders', 'orders.id', '=', 'delivery_orders.order_id')
        ->where('delivery_orders.delivery_id', $delivery->id)
        ->get();    
    }

}
