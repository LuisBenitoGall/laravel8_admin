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
     */
    
    private $option = 'recepciones';
    private $module = 'logistics';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:orders');

        $this->middleware('order_get', ['only' => ['store']]);
    }
    
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.listado',
            'module' => $this->module,
            'slug' => 'receivings'
        ]; 


        return view('admin.receivings.index')->with(compact('header'));
    }

    /**
     * 1.1. Listado de recepciones por DataTable.
     */
    public function getReceivingsDT(){
        //$data = 
        

        // return DataTables::of($data)   


        // ->rawColumns(['order_parent'])
        // ->make(true);
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

}
