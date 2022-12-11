<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\CompanyAccounts;
use App\Models\CompanySettings;
use App\Models\Invoice;
use App\Models\InvoiceItems;
use App\Models\Item;
use App\Models\IvaType;
use App\Models\Order;
use App\Models\Product;
use App\Models\TempImportOrderItems;

//Import XLS:
use App\Imports\OrderItemsImport;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller{
    /**
     * 1. Eliminar item.
     * 2. Duplicar item.
     * 2.1. Duplicar bloque con sus items.
     * 3. Guardar item.
     * 4. Obtener item por ajax.
     * 5. Actualizar item.
     * 6. Importar líneas de pedido desde .xls.
     * 7. Estado de item.
     */
    
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:budgets');
        $this->middleware('item_get', ['only' => ['destroy', 'copy']]);
        $this->middleware('order_post', ['only' => ['store']]);
        $this->middleware('order_get', ['only' => ['copyBlock']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación store:
        if(isset($data['submit_store_items'])){
            return Validator::make($data, [
                'order_id' => 'required|integer',
                'product_id' => 'nullable|integer',
                'qty' => 'required|numeric'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update_items'])){
            return Validator::make($data, [  
                'id' => 'required|integer',
                'concept' => 'required',
                'qty' => 'required'
            ]);      
        }
    }

    /**
     * 1. Eliminar item.
     *
     * @param  str $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Item $item){
        //Se eliminan líneas que no hayan sido facturadas:
        if(!$item->invoiced){
            //Borrado definitivo:
            $item->forcedelete();

            $msg = trans('textos.linea_deleted');
            if($request->environment == 'budgets'){
                return redirect()->route('budgets.edit', [$item->order_id, 'items'])->with(compact('msg'));
            }else{
                return redirect()->route('orders.edit', [$item->order_id, 'items'])->with(compact('msg')); 
            }   
        }else{
            $alert = trans('textos.linea_facturada_alert');
            if($request->environment == 'budgets'){
                return redirect()->route('budgets.edit', [$item->order_id, 'items'])->with(compact('alert'));
            }else{
                return redirect()->route('orders.edit', [$item->order_id, 'items'])->with(compact('alert')); 
            }       
        }
    }

    /**
     * 2. Duplicar item.
     */
    public function copy(Item $item){
        if(!$item){
            return redirect('/');
            exit;
        }

        $ni = new Item();
        $ni->product_id = $item->product_id;
        $ni->order_id = $item->order_id;
        $ni->budget_form_block_id = $item->budget_form_block_id;
        $ni->budget_form_item_id = $item->budget_form_item_id;
        $ni->concept = $item->concept.'-'.trans('textos.copia');
        $ni->observations = $item->observations;
        $ni->qty = $item->qty;
        $ni->price = $item->price;
        $ni->item_no_price = $item->item_no_price;
        $ni->iva = $item->iva;
        $ni->discount = $item->discount;
        $ni->invoiced = $item->invoiced;
        $ni->deposit_used = $item->deposit_used;
        $ni->days = $item->days;
        $ni->days_continue = $item->days_continue;
        $ni->days_continue_discount = $item->days_continue_discount;
        $ni->active = $item->active;
        $ni->status = $item->status;
        $ni->print = $item->print;
        $ni->user_id = Auth::user()->id;
        $ni->save();

        $msg = trans('textos.linea_duplicada_ok');
        return redirect()->route('budgets.edit', [$item->order_id, 'items'])->with(compact('msg'));
    }

    /**
     * 2.1. Duplicar bloque con sus items.
     */
    public function copyBlock($order_id, $block_id){
        $block = BudgetFormItems::find($block_id);

        $items = Item::where('order_id', $order_id)
        ->where('budget_form_block_id', $block_id)
        ->get();

        //Creamos nuevo bloque y sus líneas:
        if($block){
            $bfi = new BudgetFormItems();
            $bfi->recid = 0;        
            $bfi->order_id = $order_id;
            $bfi->name = $block->name.' '.time();
            $bfi->save();

            if($items->count()){
                foreach($items as $item){
                    $i = new Item();
                    $i->product_id = $item->product_id;
                    $i->order_id = $order_id;
                    $i->budget_form_block_id = $bfi->id;
                    $i->budget_form_item_id = $item->budget_form_item_id;
                    $i->concept = $item->concept;
                    $i->observations = $item->observations;
                    $i->qty = $item->qty;
                    $i->price = $item->price;
                    $i->item_no_price = $item->item_no_price;
                    $i->iva = $item->iva;
                    $i->discount = $item->discount;
                    $i->calculated_by_percent = $item->calculated_by_percent;
                    $i->deposit_used = $item->deposit_used;
                    $i->days = $item->days;
                    $i->days_continue = $item->days_continue;
                    $i->days_continue_discount = $item->days_continue_discount;
                    $i->active = $item->active;
                    $i->status = $item->status;
                    $i->print = $item->print;
                    $i->user_id = Auth::user()->id;
                    $i->save();
                }
            }

            $msg = trans('textos.bloque_duplicado');
            return redirect()->route('budgets.edit', [$order_id, 'items'])->with(compact('msg'));

        }else{
            $alert = trans('textos.bloque_duplicado_error');
            return redirect()->route('budgets.edit', [$order_id, 'items'])->with(compact('alert'));
        }
    }

    /**
     * 3. Guardar item.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-new-line');

            if($request->environment == 'orders'){
                return redirect('admin/orders/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
            }else{
                return redirect('admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();    
            }
        } 

        //Si la línea pertenece a un presupuesto tipo formulario con bloques, la línea se incorpora a un bloque genérico:
        $order = Order::select('id', 'modality', 'from_customer', 'provider_id', 'customer_id')->find($request->order_id);

        //Producto:
        $product = $request->product_id? Product::select('name')->find($request->product_id):false;

        //Bloque al que pertenece el item:
        $budget_form_block_id = NULL;

        //Orden en el bloque:
        $budget_sort = NULL;

        //Presupuesto por formulario:
        if($order && $order->modality == config('constants.BUDGET_FORM_MODALITY_')){
            //Obtenemos bloque genérico del presupuesto:
            $block = BudgetFormItems::select('id')
            ->where('recid', '0')
            ->where('order_id', $order->id)
            ->where('name', config('constants.BUDGET_GENERIC_BLOCK_'))
            ->first();

            if(!$block){
                $block = new BudgetFormItems();
                $block->recid = '0';
                $block->order_id = $order->id;
                $block->name = config('constants.BUDGET_GENERIC_BLOCK_');
                $block->save();
            }
            $budget_form_block_id = $block->id; 

            //Orden en el bloque:
            $last_order = Item::select('budget_sort')
            ->where('order_id', $order->id)
            ->where('budget_form_block_id', $budget_form_block_id)
            ->whereNotNull('budget_sort')
            ->orderBy('budget_sort', 'DESC')
            ->first();

            $budget_sort = $last_order? $last_order->budget_sort+1:1;
        }

        //IVA:
        $iva = $request->iva? $request->iva:CompanySettings::co_iva();

        //Recargo de equivalencia:
        $equivalence_surcharge = NULL;
        if($iva > 0){
            $es = IvaType::select('equivalence_surcharge')
            ->where('iva', $iva)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

            $equivalence_surcharge = $es? $es->equivalence_surcharge:NULL;
        }

        $item = new Item();
        $item->product_id = $request->product_id? $request->product_id:NULL;
        $item->order_id = $order->id;
        $item->budget_form_block_id = $budget_form_block_id;
        $item->budget_sort = $budget_sort;
        $item->concept = $product? $product->name:$request->concept;
        $item->observations = $request->observations;
        $item->qty = $request->calculated_by_percent? 1:$request->qty;      //La cantidad siempre es 1 para precios calculados por porcentaje.
        $item->price = $request->price? $request->price:($request->calculated_by_percent? $request->calculated_by_percent:NULL);
        $item->days = $request->days? $request->days:'0';
        $item->iva = $iva;
        $item->equivalence_surcharge = $equivalence_surcharge;
        $item->discount = $request->discount > 0? $request->discount:NULL;
        $item->calculated_by_percent = $request->price? NULL:$request->calculated_by_percent;
        $item->user_id = Auth::user()->id;
        $item->save();

        //Si se trata de una inclusión de línea sobre un pedido de compra ya facturado, se genera la línea de factura:
        if($order->customer_id == session('currentCompany') && $order->from_customer){
            //Obtenemos última factura del pedido:
            $invoice = Invoice::select('id')
            ->where('order_id', $order->id)
            ->latest('id')
            ->first();

            $amount = helpers::itemAmount($item);

            $ii = new InvoiceItems();
            $ii->invoice_id = $invoice->id;
            $ii->item_id = $item->id;
            $ii->qty = $item->qty;
            $ii->price = $item->price;
            $ii->discount = $item->discount? $item->discount:'0';
            $ii->days = $item->days;
            $ii->amount = $amount['amount'];
            $ii->iva = $item->iva;
            $ii->save();
        }

        $msg = trans('textos.linea_creado_ok');
        return redirect()->route($request->environment.'.edit', [$request->order_id, 'items'])->with(compact('msg'));
    }

    /**
     * 4. Obtener item por ajax.
     */
    public function edit(Request $request, Item $item){
        if($request->ajax()){ 
            return response()->json($item);    
        }
    }

    /**
     * 5. Actualizar item.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-edit-line');

            return redirect('admin/orders/'.$request->order_id.'/edit/items')
                ->withErrors($validator)
                ->withInput();
        }   

        $order = Order::select('id', 'invoiced')->find($request->order_id);

        //Comprobamos si el proveedor dispone de cuenta, esto es, que pueda generar directamente sus facturas:
        $provider_account = false;
        if($order){
            $provider_account = CompanyAccounts::where('company_id', $order->provider_id)
            ->where('status', 2)
            ->first();
        }

        //Si pedido no existe o está facturado y proveedor gestiona sus propios pedidos y facturas:
        if(!$order || ($order->invoiced && $provider_account)){
            $alert = trans('textos.pedido_facturado_aviso');
            return redirect()->route('orders.edit', [$request->order_id, 'items'])->with(compact('alert'));
            exit;
        }

        //IVA:
        $iva = $request->iva? $request->iva:$item->iva;

        //Recargo de equivalencia:
        $equivalence_surcharge = NULL;
        if($iva > 0){
            $es = IvaType::select('equivalence_surcharge')
            ->where('iva', $iva)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

            $equivalence_surcharge = $es? $es->equivalence_surcharge:NULL;
        }

        $item = Item::find($request->id);
        $item->concept = $request->concept;
        $item->qty = $request->qty;
        $item->price = $request->price;
        $item->days = $request->days;
        $item->iva = $iva;
        $item->equivalence_surcharge = $equivalence_surcharge;
        $item->discount = $request->discount;
        $item->observations = $request->observations;
        $item->save();

        //Actualizamos la línea en líneas de factura, si ésta no está bloqueada:
        $invoice_item = InvoiceItems::select('invoice_items.*')
        ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
        ->where('invoice_items.item_id', $item->id)
        ->where('invoices.blocked', '0')
        ->first();
        if($invoice_item){
            InvoiceItems::updateInvoiceItem($item, $invoice_item->invoice_id);
        }

        $msg = trans('textos.linea_updated');
        if($request->environment == 'orders'){
            return redirect()->route('orders.edit', [$request->order_id, 'items'])->with(compact('msg'));
        }elseif($request->environment == 'invoices'){
            return redirect()->route('invoices.edit', [$request->invoice_id, 'items'])->with(compact('msg'));
        }else{
            return redirect()->route('budgets.edit', [$request->budget_id, 'items'])->with(compact('msg'));    
        }
    }

    /**
     * 6. Importar líneas de pedido desde .xls.
     */
    public function importOrderItems(Request $request){
        $file = $request->file('items');

        //Eliminamos registros anteriores del pedido:
        TempImportOrderItems::where('order_id', $request->order_id)->delete();

        //Importación:
        Excel::import(new OrderItemsImport($request->order_id), $file);

        //Procesamos los datos temporales hacia la tabla items:
        $data = TempImportOrderItems::where('order_id', $request->order_id)
        ->orderBy('id', 'ASC')
        ->get();
        if($data->count() > 0){
            foreach($data as $r){
                //Recargo de equivalencia:
                $equivalence_surcharge = NULL;
                if($r->iva > 0){
                    $es = IvaType::select('equivalence_surcharge')
                    ->where('iva', $r->iva)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->first();

                    $equivalence_surcharge = $es? $es->equivalence_surcharge:NULL;
                }

                $item = new Item();
                $item->order_id = $r->order_id;
                //$item->ref = $r->ref;
                $item->concept = $r->concept;
                $item->observations = $r->observations;
                $item->qty = $r->qty;
                $item->price = $r->price;
                $item->iva = $r->iva;
                $item->equivalence_surcharge = $equivalence_surcharge;
                $item->discount = $r->discount;
                $item->user_id = Auth::user()->id;
                $item->save();
            }    
        }

        //Eliminamos -de nuevo- registros anteriores del pedido:
        //TempImportOrderItems::where('budget_order_id', $request->order_id)->delete();

        $msg = trans('textos.importacion_ok');
        return redirect()->route('orders.edit', $item->order_id)->with(compact('msg'));
    }

    /**
     * 7. Estado de item.
     */
    public function status(Request $request, Item $item, $status){
        if($request->ajax()){   
            $item->status = $status;
            $item->save();

            return response()->json(true);
        }
    }

}
