<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\BudgetFormItems;
use App\Models\CompanySettings;
use App\Models\Product;

class BudgetFormItemsController extends Controller{
    /**
     * 1. Listado de items para DataTable.
     * 2. Guardar item.
     * 3. Actualizar item.
     * 4. Eliminar item.
     * 5. Establecer item fijo en los presupuestos.
     * 6. Añadir bloque a presupuesto.
     * 7. Actualizar nombre de bloque.
     * 8. Ordenar bloques en presupuesto.
     */  
    
    private $option = 'presupuestos';
    private $module = 'budgets';
    private $languages;   

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:budgets');
        $this->middleware('budget_form_post', ['only' => ['store', 'update']]);
        $this->middleware('budget_form_item_get', ['only' => ['destroy']]);
        $this->middleware('order_post', ['only' => ['addBlock', 'updateBlock', 'sortBlocks']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required_without:product_id|string|max:255',
                'form_id' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'name' => 'required|string|max:255'
            ]);

        //Validación añadir bloque desde presupuesto:
        }elseif(isset($data['submit-add-block'])){
            return Validator::make($data, [ 
                'order_id' => 'required|integer', 
                'name' => 'required|string|max:255' 
            ]); 

        //Validación actualizar bloque:
        }elseif(isset($data['submit_update_block'])){
            return Validator::make($data, [ 
                'order_id' => 'required|integer', 
                'bfi_id' => 'required|integer',
                'name' => 'required|string|max:255' 
            ]); 

        //Validación ordenar bloques:
        }elseif(isset($data['submit_sort_blocks'])){
            return Validator::make($data, [ 
                'order_id' => 'required|integer'
            ]); 
        }
    }

    /**
     * 1. Listado de items para DataTable.
     */
    public function getBudgetFormItemsDT($form_id = false, $recid = '0'){
        if($form_id){
            //Bloques:
            if($recid == '0'){
                $data = BudgetFormItems::select('budget_form_items.*', 'cost_centers.name AS cost_center')
                ->leftJoin('products', 'budget_form_items.product_id', '=', 'products.id')
                ->leftJoin('cost_centers', 'budget_form_items.cost_center_id', '=', 'cost_centers.id')
                ->where('budget_form_items.form_id', $form_id)
                ->where('budget_form_items.recid', $recid);

                return DataTables::of($data)
                ->addColumn('name', function($row){
                    return $row->name;
                })
                ->addColumn('cost_center', function($row){
                    return $row->cost_center? $row->cost_center:'';
                })
                ->addColumn('acciones', function($row){
                    return view('admin.budget_form_items.partials.dt-actions', compact('row'));    
                })
                ->make(true);

            //Items:
            }else{
                $data = BudgetFormItems::select(
                    DB::raw('(SELECT t.name FROM budget_form_items AS t WHERE budget_form_items.recid = t.id) AS block'),
                    'budget_form_items.*',
                    'products.name AS productName',
                    'products.price',
                    'products.calculated_by_percent AS price_by_percent',
                    'cost_centers.name AS cost_center'
                )
                ->leftJoin('products', 'budget_form_items.product_id', '=', 'products.id')
                ->leftJoin('cost_centers', 'products.cost_center_id', '=', 'cost_centers.id')
                ->where('budget_form_items.form_id', $form_id)
                ->where('budget_form_items.recid', '>', '0')
                ->orderBy('budget_form_items.name', 'ASC');

                //Moneda:
                $currency = CompanySettings::co_currency(0);

                return DataTables::of($data)
                ->addColumn('name', function($row){
                    return $row->name;
                })
                ->addColumn('block', function($row){
                    return $row->block;
                })
                ->addColumn('price', function($row) use($currency){
                    if($row->calculated_by_percent && $row->calculated_by_percent > 0){
                        return $row->price_by_percent.'%';
                    }else{
                        return $row->price.$currency->symbol;
                    }
                })
                ->addColumn('discount', function($row){
                    if($row->discount > 0){
                        return $row->discount.'%';
                    }
                })
                ->addColumn('cost_center', function($row){
                    return $row->cost_center? $row->cost_center:'';
                })
                ->addColumn('acciones', function($row){
                    return view('admin.budget_form_items.partials.dt-actions-items', compact('row'));    
                })
                ->make(true);
            }

        }else{
            return false;
        }   
    }

    /**
     * 2. Guardar item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $modal = $request->input('recid') == 0? 'modal-add-block':'modal-add-item';
            $validator->getMessageBag()->add('accion', $modal);
            
            return redirect('admin/budget-forms/'.$request->form_id.'/edit/blocks')
                ->withErrors($validator)
                ->withInput();
        }

        $product = false;
        if($request->product_id){
            $product = Product::select('name')->find($request->product_id);
        }

        $item = new BudgetFormItems;
        $item->recid = $request->recid;
        $item->form_id = $request->form_id;
        $item->product_id = $request->product_id;
        $item->name = $request->name? $request->name:($product? $product->name:'');
        $item->observations = $request->observations;
        $item->discount = $request->discount;
        $item->qty_min = $request->qty_min;
        $item->calculated_by_percent = $request->calculated_by_percent? $request->calculated_by_percent:NULL;
        //05/02/2024: el centro de coste quedará definido en el catálogo.
        //$item->cost_center_id = $request->cost_center;
        $item->save();

        //Actualizamos precio en catálogo:
        if($request->price_ > 0){
            Product::where('id', $request->product_id)
            ->update(['price' => $request->price_]);
        }
    
        $msg = trans('textos.bloque_creado_ok');
        $tab = $request->input('recid') == 0? 'blocks':'items';
        return redirect()->route('budget-forms.edit', [$item->form_id, $tab])->with(compact('msg'));
    }

    /**
     * 3. Actualizar item.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $modal = $request->input('price') == 0? 'modal-edit-block':'modal-edit-item';
            $validator->getMessageBag()->add('accion', $modal);
            
            return redirect('admin/budget-forms/'.$request->form_id.'/edit/blocks')
                ->withErrors($validator)
                ->withInput();
        }

        $item = BudgetFormItems::find($request->id);
        $item->name = $request->name;
        $item->observations = $request->observations;
        $item->qty_min = $request->qty_min;
        $item->discount = $request->discount;
        $item->calculated_by_percent = $request->calculated_by_percent? $request->calculated_by_percent:NULL;
        $item->cost_center_id = $request->cost_center;
        $item->save();
    
        $msg = $item->recid == '0'? trans('textos.bloque_updated'):trans('textos.item_updated');
        $tab = $item->recid == '0'? 'blocks':'items';
        return redirect()->route('budget-forms.edit', [$item->form_id, $tab])->with(compact('msg'));
    }

    /**
     * 4. Eliminar item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BudgetFormItems $item){
        //Si es bloque, obtenemos los items hijos para eliminarlos:
        if($item->recid == '0'){
            BudgetFormItems::where('recid', $item->id)->delete();   
        }

        $form_id = $item->form_id;

        $msg = $item->recid == '0'? trans('textos.bloque_deleted'):trans('textos.item_deleted');
        $tab = $item->recid == '0'? 'blocks':'items';

        $item->delete();
        
        return redirect()->route('budget-forms.edit', [$form_id, $tab])->with(compact('msg'));    
    }

    /**
     * 5. Establecer item fijo en los presupuestos.
     */
    public function setFixed(Request $request, $id, $status){
        if($request->ajax()){ 
            $item = BudgetFormItems::find($id);
            $item->fixed_in_budget = $status;
            $item->save();

            $resp = ($item)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 6. Añadir bloque a presupuesto.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addBlock(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-block');

            return redirect('admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        if($request->order_id && $request->name){
            $item = new BudgetFormItems;
            $item->recid = 0;
            $item->order_id = $request->order_id;
            $item->name = $request->name;
            $item->cost_center_id = $request->cost_center;
            $item->save();

            $msg = trans('textos.bloque_creado_ok');
            return redirect()->route('budgets.edit', [$request->order_id, 'items'])->with(compact('msg'));
        }else{
             return redirect()->route('home');    
        }
    }

    /**
     * 7. Actualizar nombre de bloque.
     */
    public function updateBlock(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-block');

            return redirect('admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        BudgetFormItems::where('id', $request->bfi_id)
        ->update(['name' => $request->name, 'cost_center_id' => $request->cost_center]); 

        $msg = trans('textos.bloque_updated');
        return redirect()->route('budgets.edit', [$request->order_id, 'items'])->with(compact('msg'));
    }

    /**
     * 8. Ordenar bloques en presupuesto.
     */
    public function sortBlocks(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-sort-blocks');

            return redirect('admin/budgets/'.$request->order_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }   

        $i = 1;
        foreach($request->id as $id){
            BudgetFormItems::where('id', $id)
            ->update(['sort_block' => $i]); 

            $i++;    
        }

        $msg = trans('textos.bloques_ordenados');
        return redirect()->route('budgets.edit', [$request->order_id, 'items'])->with(compact('msg'));
    }

    /**
     * 9. Eliminar Bloque.
     */
    public function deleteBlock(BudgetFormItems $bfi){
        $order_id = $bfi->order_id;

        $t = $bfi->delete();

        $msg = trans('textos.bloque_deleted');
        return redirect()->route('budgets.edit', [$order_id, 'items'])->with(compact('msg'));
    }
}
