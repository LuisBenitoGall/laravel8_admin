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
use App\Models\Company;
use App\Models\CompanySettings;
use App\Models\Delivery;
use App\Models\DeliveryItems;
use App\Models\DeliveryOrders;
use App\Models\Item;
use App\Models\Order;
use App\Models\Stock;

class ReceivingController extends Controller{
    /**
     * 1. Recepción de pedidos.
     * 1.1. Listado de recepciones por DataTable.
     * 2. Guardar nueva recepción de pedido.
     * 3. Editar recepción de pedido.
     * 4. Actualizar recepción de pedido.
     * 5. Recepción completa de pedido.
     */
    
    private $option = 'recepciones';
    private $module = 'logistics';
    private $languages;
    private $co_currency;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        //Moneda:
        $this->co_currency = CompanySettings::co_currency(0);

        $this->middleware('module_setted:orders');
        $this->middleware('order_get', ['only' => ['store']]);
        $this->middleware('receiving_get', ['only' => ['edit', 'update']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación update:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                
            ]);
        }
    }

    /**
     * 1. Recepción de pedidos.
     */
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
        $data = Delivery::select('deliveries.id', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.origin', 'deliveries.send_date', 'deliveries.reception_date', 'deliveries.estimated_reception_date', 'deliveries.customer_observations', 'orders.id AS orderId', 'orders.purchase_num', 'orders.purchase_old_num', 'companies.name AS company')
        ->join('companies', 'deliveries.provider_id', '=', 'companies.id')
        ->leftJoin('delivery_orders', 'deliveries.id', '=', 'delivery_orders.delivery_id')
        ->leftJoin('orders', 'delivery_orders.order_id', '=', 'orders.id')
        ->where('deliveries.customer_id', session('currentCompany'))
        ->where('deliveries.status', '0')
        ->orderBy('deliveries.reception_date', 'ASC');

        return DataTables::of($data)   
        ->addColumn('estimated_reception_date', function($row){
            return Carbon::parse($row->estimated_reception_date)->format($this->languages[session()->get('locale')][4]);
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
        $estimated_reception_date = $request->estimated_reception_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->estimated_reception_date):$request->estimated_reception_date:NULL;

        $dlv = new Delivery();
        $dlv->origin = 'c';     //Item que genera el cliente.
        $dlv->provider_id = $order->provider_id;
        $dlv->customer_id = $order->customer_id;
        $dlv->estimated_reception_date = $estimated_reception_date;
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
    public function edit(Delivery $delivery, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.recepcion_tramitar',
            'module' => $this->module,
            'slug' => 'receiving'
        ]; 

        //Pedidos de la recepción:
        $orders = Order::select('orders.id', 'orders.order_num', 'orders.order_old_num', 'orders.name')
        ->join('delivery_orders', 'orders.id', '=', 'delivery_orders.order_id')
        ->where('delivery_orders.delivery_id', $delivery->id)
        ->get();  

        $ordersArr = [];
        foreach($orders as $o){
            array_push($ordersArr, $o->id);
        }
        //Items de los pedidos y su correspondencia con referencias del cliente.
        $items = Item::select('items.id', 'items.product_id', 'items.order_id', 'items.concept', 'items.qty', 'items.price', 'items.iva',
            DB::raw('(SELECT SUM(qty) FROM delivery_items WHERE item_id = items.id) AS prev_qty'),
            DB::raw('(SELECT p.name FROM products p 
                left join product_providers pp
                on p.id = pp.customer_product_id
                WHERE pp.provider_product_id = items.product_id
                AND pp.provider_id = '.$delivery->provider_id.'
                AND p.company_id = '.session('currentCompany').') AS internal_ref'),
            DB::raw('(SELECT p.id FROM products p 
                left join product_providers pp
                on p.id = pp.customer_product_id
                WHERE pp.provider_product_id = items.product_id
                AND pp.provider_id = '.$delivery->provider_id.'
                AND p.company_id = '.session('currentCompany').') AS internal_id')
        )
        ->whereIn('items.order_id', $ordersArr)
        ->orderBy('items.order_id', 'ASC')
        ->orderBy('items.concept', 'ASC')
        ->get();

        $provider = Company::find($delivery->provider_id); 
                                                                                                                                                                           
        $currency = $this->co_currency; 

        $languages = $this->languages;

        return view('admin.receivings.edit')->with(compact('header', 'delivery', 'tab', 'orders', 'items', 'provider', 'currency', 'languages'));
    }

    /**
     * 4. Actualizar recepción de pedido.
     */
    public function update(Request $request, Delivery $delivery){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/receiving/'.$delivery->id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $delivery->delivery_old_num = $request->delivery_old_num;
        if($request->reception_date){
            $delivery->reception_date = $request->reception_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->reception_date):$request->reception_date:NULL;
        }
        $delivery->customer_observations = $request->customer_observations;
        $delivery->save();

        $msg = trans('textos.recepcion_actualizada');
        return redirect()->route('receiving.edit', [$delivery->id, false])->with(compact('msg'));
    }

    /**
     * 5. Recepción completa de pedido.
     */
    public function storeAllOrder(Delivery $delivery, Order $order){
        //Recepciones del pedido, en array:
        $receptions =  DeliveryOrders::where('order_id', $order->id)
        ->pluck('delivery_id')
        ->toArray();

        if(count($receptions)){
            //Creamos la recepción:
            $dlv = new Delivery();
            $dlv->origin = 'c';     //Item que genera el cliente.
            $dlv->provider_id = $order->provider_id;
            $dlv->customer_id = $order->customer_id;
            $dlv->estimated_reception_date = $estimated_reception_date;
            $dlv->customer_observations = $request->customer_observations;
            $dlv->user_id = Auth::user()->id;
            $dlv->save();

            //Guardamos vínculo pedido - entrega:
            $do = new DeliveryOrders();
            $do->delivery_id = $dlv->id;
            $do->order_id = $order->id;
            $do->save();

            //Líneas del pedido y condición de producto si tiene gestión de stock y lotes.
            $order_items = Item::select('items.id', 'items.qty', 'products.id AS productId', 'products.stock_management', 'products.batch')
            ->leftJoin('products', 'items.product_id', '=', 'products.id')
            ->where('items.order_id', $order->id)
            ->get();

            foreach($order_items as $oi){
                //Comprobamos la cantidad pendiente de recibir:
                $received = DeliveryItems::where('item_id', $oi->id)->sum('qty');

                //Si la cantidad recibida es inferior a la del pedido, completamos la entrega con la diferencia:
                if($received < $oi->qty){
                    $diff = $oi->qty - $received;

                    //Agregamos la línea de recepción:
                    $row = new DeliveryItems();
                    $row->delivery_id = $delivery->id;
                    $row->item_id = $oi->id;
                    $row->qty = $diff;
                    $row->user_id = Auth::user()->id;
                    $row->save();

                    //Si el producto tiene gestión de stock:
                    if($oi->stock_management){
                        //Obtenemos correspondencia de referencia del proveedor:
                        $my_ref = 

                        $data = [
                            'product_id' => $request->input('product_id'),
                            'stock_mov_id'  => $request->input('stock_mov_id'), 
                            'batch_id' => $batchId,
                            'store_id' => $request->input('store_id'),
                            'user_id' => Auth::user()->id,
                            'qty' => $request->input('qty'),
                            'operation' => $operation,
                            'result' => $result,
                            'doc' => $fileName,
                            'doc_num' => $request->doc_num,
                            'for_user' => $for_user,
                            'returned_at' => $returned_at,
                            'product_serial_id' => $request->product_serial_id,
                            'observations' => $request->input('observations'),
                            'store_destiny' => $request->input('store_destiny')    
                        ];
                        Stock::movStock($data);
                    }
                }
            }

        }else{
            $alert = trans('textos.recepciones_pendientes_no');
            return redirect()->route('receiving.edit', [$delivery->id, 'tab-'.$order->id])->with(compact('alert'));
        }



        dd($order, $receptions);
    }

}
