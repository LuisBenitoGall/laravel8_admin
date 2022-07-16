<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

//Models:
use App\Models\Company;
use App\Models\CustomerProviders;
use App\Models\Delivery;
use App\Models\DeliveryOrders;
use App\Models\DeliveryPatterns;
use App\Models\Order;

class DeliveryController extends Controller{
    /**
     * 1. Entregas para cliente.
     * 1.1. Listado de entregas para DataTable.
     * 1.2. Listado de pedidos para incluir en entregas para DataTable.
     * 2. Formulario nueva entrega.
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
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación create:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'company_id' => 'required|integer'
            ]);
        }
    }

    /**
     * 1. Entregas para cliente.
     */
    public function index($tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.listado',
            'module' => $this->module,
            'slug' => 'deliveries'
        ]; 

        return view('admin.deliveries.index')->with(compact('header', 'tab'));
    }

    /**
     * 1.1. Listado de entregas para DataTable.
     */
    public function getDeliveriesDT(){
        $deliveries = Delivery::select('deliveries.id', 'deliveries.delivery_num', 'deliveries.delivery_old_num', 'deliveries.status', 'companies.name', 'deliveries.created_at')
        ->join('companies', 'deliveries.customer_id', '=', 'companies.id')
        ->where('provider_id', session('currentCompany'))
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('deliveries.edit');
        $eliminar = Auth::user()->can('deliveries.destroy');

        $arrEstados = Helpers::deliveryStatus();
        //Pasamos array a string con los índices:
        $strEstados = '';
        foreach($arrEstados as $idx => $est){
            $strEstados .= $idx.'|'.$est.';';
        }

        foreach($deliveries as $row){
            //Enviamos las fechas ya formateadas:
            $row->fechaEnvio = $row->send_date? Carbon::parse($row->send_date)->format($this->languages[session()->get('locale')][4]):'';
            $row->fechaRecepcion = $row->reception_date? Carbon::parse($row->reception_date)->format($this->languages[session()->get('locale')][4]):'';
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            $row->estado = $row->status? ucfirst($arrEstados[$row->status]):'';
            $row->estados = $row->status.';'.$strEstados;

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;    
        }

        return datatables($deliveries)->toJson();
    }

    /**
     * 1.2. Listado de pedidos para incluir en entregas para DataTable.
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
        ->join('budget_order_shared_fields', 'orders.id', '=', 'budget_order_shared_fields.order_id')
        ->where('budget_order_shared_fields.provider_id', session('currentCompany'))
        ->where('budget_order_shared_fields.customer_id', $delivery->customer_id)
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
