<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Budget;
use App\Models\BudgetFormItems;
use App\Models\BudgetForms;
use App\Models\BudgetOrderSharedFields;
use App\Models\CompanySettings;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;

class ItemController extends Controller{
    /**
     * 1. Eliminar item.
     * 2. Duplicar item.
     * 3. Guardar item.
     * 4. Obtener item por ajax.
     * 5. Actualizar item.
     */
    
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:budgets');
        $this->middleware('item_get', ['only' => ['destroy', 'copy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación store:
        if(isset($data['submit_store_items'])){
            return Validator::make($data, [
                'order_id' => 'nullable|integer',
                'budget_id' => 'nullable|integer',
                'shared_id' => 'required|integer',
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
        $shared = BudgetOrderSharedFields::select('budget_id', 'order_id')
        ->find($item->budget_order_id);

        $order_id = $shared->order_id;
        $budget_id = $shared->budget_id;

        //Se eliminan líneas que no hayan sido facturadas:
        if(!$item->invoiced){
            //Borrado definitivo:
            $item->forcedelete();

            //Recalculamos las líneas del presupuesto que se calculan por porcentaje:
            //if($budget) $budget->calculatePercentageItems();

            $msg = trans('textos.linea_deleted');
            if($request->environment == 'budgets'){
                return redirect()->route('budgets.edit', [$budget_id, 'items'])->with(compact('msg'));
            }else{
                return redirect()->route('orders.edit', [$order_id, 'items'])->with(compact('msg')); 
            }   
        }else{
            $alert = trans('textos.linea_facturada_alert');
            if($request->environment == 'budgets'){
                return redirect()->route('budgets.edit', [$budget_id, 'items'])->with(compact('alert'));
            }else{
                return redirect()->route('orders.edit', [$order_id, 'items'])->with(compact('alert')); 
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
        $ni->budget_order_id = $item->budget_order_id;
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

        $shared = BudgetOrderSharedFields::select('budget_id', 'order_id')
        ->find($item->budget_order_id); 

        $msg = trans('textos.linea_duplicada_ok');
        return redirect()->route('budgets.edit', [$shared->budget_id, 'items'])->with(compact('msg'));
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
                return redirect('admin/budgets/'.$request->budget_id.'/edit')
                ->withErrors($validator)
                ->withInput();    
            }
        }   

        //Producto:
        $product = $request->product_id? Product::select('name')->find($request->product_id):false;

        //Si la línea pertenece a un presupuesto tipo formulario con bloques, la línea se incorpora a un bloque genérico:
        $budget = Budget::select('budgets.id', 'budgets.modality')
        ->join('budget_order_shared_fields', 'budgets.id', '=', 'budget_order_shared_fields.budget_id')
        ->where('budget_order_shared_fields.id', $request->shared_id)
        ->first();

        //Bloque al que pertenece el item:
        $budget_form_block_id = NULL;

        if($budget->modality == config('constants.BUDGET_FORM_MODALITY_')){
            //Obtenemos bloque genérico del presupuesto:
            $block = BudgetFormItems::select('id')
            ->where('recid', '0')
            ->where('budget_id', $budget->id)
            ->where('name', config('constants.BUDGET_GENERIC_BLOCK_'))
            ->first();

            if(!$block){
                $block = new BudgetFormItems();
                $block->recid = '0';
                $block->budget_id = $budget->id;
                $block->name = config('constants.BUDGET_GENERIC_BLOCK_');
                $block->save();
            }
            $budget_form_block_id = $block->id; 
        }

        $item = new Item();
        $item->product_id = $request->product_id? $request->product_id:NULL;
        $item->budget_order_id = $request->shared_id;
        $item->budget_form_block_id = $budget_form_block_id;
        $item->concept = $product? $product->name:$request->concept;
        $item->observations = $request->observations;
        $item->qty = $request->qty;
        $item->price = $request->price;
        $item->days = $request->days? $request->days:'0';
        $item->iva = $request->iva? $request->iva:CompanySettings::co_iva();
        $item->discount = $request->discount;
        $item->user_id = Auth::user()->id;
        $item->save();

        $msg = trans('textos.linea_creado_ok');
        if($request->environment == 'orders'){
            return redirect()->route('orders.edit', [$request->order_id, 'items'])->with(compact('msg'));
        }else{
            return redirect()->route('budgets.edit', [$request->budget_id, 'items'])->with(compact('msg'));    
        }
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

        $item = Item::find($request->id);
        $item->concept = $request->concept;
        $item->qty = $request->qty;
        $item->price = $request->price;
        $item->days = $request->days;
        $item->iva = $request->iva;
        $item->discount = $request->discount;
        $item->observations = $request->observations;
        $item->save();

        $msg = trans('textos.linea_updated');
        if($request->environment == 'orders'){
            return redirect()->route('orders.edit', [$request->order_id, 'items'])->with(compact('msg'));
        }else{
            return redirect()->route('budgets.edit', [$request->budget_id, 'items'])->with(compact('msg'));    
        }
    }

}
