<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\Budget;
use App\Models\BudgetOrderSharedFields;
use App\Models\Item;
use App\Models\Order;

class BudgetOrderSharedFieldsController extends Controller{
    /**
     * 1. Añadir descuento a presupuesto-pedido.
     * 2. Eliminar descuento de presupuesto-pedido.
     * 3. Modificar cliente de presupuesto-pedido.
     * 4. Añadir miembro a equipo.
     * 5. Eliminar miembro de equipo.
     */
    
    public function __construct(){
        $this->middleware('budget_order_get', ['only' => 'addDiscount', 'removeDiscount', 'setCustomer', 'addTeamMember', 'deleteTeamMember']);
    }
    
    //0. Validator:
    protected function validator(array $data){
        //Validación add discount:
        if(isset($data['submit_budget_discount'])){
            return Validator::make($data, [
                'discount_amount' => 'required_without:discount_percent|numeric|nullable',
                'discount_percent' => 'required_without:discount_amount|numeric|nullable'
            ],[
                'discount_amount.numeric' => trans('textos.valor_numerico'),
                'discount_amount.required_without' => trans('textos.descuento_tipo_obligatorio'),
                'discount_percent.numeric' => trans('textos.valor_numerico'),
                'discount_percent.required_without' => trans('textos.descuento_tipo_obligatorio')
            ]); 

        //Validación modificar cliente:
        }elseif(isset($data['submit_set_customer'])){
            return Validator::make($data, [
                'new_customer' => 'required|integer'
            ]); 

        //Validación añadir miembro:
        }elseif(isset($data['submit_member'])){
            return Validator::make($data, [
                'environment' => 'required',
                'member' => 'required|integer'
            ]); 
        } 
    }
    
    /**
     * 1. Añadir descuento a presupuesto.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addDiscount(Request $request, BudgetOrderSharedFields $shared){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-budget-discount');

            return redirect('admin/budgets/'.$shared->budget_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        //Calculamos ambos tipos de descuento a partir del import total:
        $budget_amount = $shared->itemsBudgetOrderAmount(true);

        //Descuento neto
        if($request->discount_amount){
            $discount_amount = $request->discount_amount;
            $discount = $budget_amount > 0? round((($request->discount_amount * 100) / $budget_amount), 2):'0';

        }elseif($request->discount_percent){
            $discount_amount = Helpers::percent($budget_amount, $request->discount_percent);
            $discount = $request->discount_percent;
        }

        $shared->discount = $discount;
        $shared->discount_amount = $discount_amount;
        $shared->save();

        //Eliminamos descuentos parciales de cada línea del presupuesto:
        Item::where('budget_order_id', $shared->id)
        ->update(['discount' => NULL]);

        $msg = trans('textos.descuento_aplicado_ok');
        if($request->return == 'budgets'){
            return redirect()->route('budgets.edit', [$shared->budget_id, 'items'])->with(compact('msg'));    
        }else{
            return redirect()->route('orders.edit', [$shared->order_id])->with(compact('msg'));
        }
    }

    /**
     * 2. Eliminar descuento de presupuesto-pedido.
     */
    public function removeDiscount(BudgetOrderSharedFields $shared, $return = false){
        //Eliminamos descuento sobre cada línea del presupuesto que tuviese ese porcentaje aplicado.  Puede haber alguna línea que tenga un descuento diferente.
        Item::where('budget_order_id', $shared->id)
        ->where('discount', $shared->discount)
        ->update(['discount' => NULL]);

        $shared->discount = NULL;
        $shared->discount_amount = NULL;
        $shared->save();

        $msg = trans('textos.descuento_deleted_ok');
        if($return == 'orders'){
            return redirect()->route('orders.edit', $shared->order_id)->with(compact('msg'));
        }else{
            return redirect()->route('budgets.edit', [$shared->budget_id, 'items'])->with(compact('msg'));    
        }
    }

    /**
     * 3. Modificar cliente de presupuesto-pedido.
     */
    public function setCustomer(Request $request, BudgetOrderSharedFields $shared){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-set-customer');

            if($request->environment == 'budgets'){
                return redirect('/admin/budgets/'.$shared->budget_id.'/edit')
                    ->withErrors($validator)
                    ->withInput();
            }else{
                return redirect('/admin/orders/'.$shared->order_id.'/edit')
                    ->withErrors($validator)
                    ->withInput();    
            }
        }

        //Comprobamos de nuevo que no se haya facturado el pedido:
        if($shared->order_id){
            $invoiced = Order::select('percentage_invoiced')->find($shared->order_id);
            if($invoiced->percentage_invoiced > 0){
                $alert = trans('textos.no_permitida');  
                return redirect()->route('orders.edit', $shared->order_id)->with(compact('alert')); 
                exit;     
            }
        }

        //Actualizamos valores:
        $shared->customer_id = $request->new_customer;
        $shared->customer_manager_id = NULL;
        $shared->customer_workplace_id = NULL;
        $shared->customer_cost_center_id = NULL;
        $shared->customer_business_area_id = NULL;
        $shared->customer_order = NULL;
        $shared->customer_order_doc = NULL;
        $shared->customer_team = NULL;
        $shared->customer_products = NULL;
        $shared->save();

        //Actualizaciones en el pedido:
        if($shared->order_id){
            Order::where('id', $shared->order_id)
            ->update(['customer_confirmation' => NULL, 'confirmed_by' => NULL]);
        }

        $msg = trans('textos.cliente_modificado');
        if($request->environment == 'budgets'){
            return redirect()->route('budgets.edit', $shared->budget_id)->with(compact('msg'));    
        }else{
            return redirect()->route('orders.edit', $shared->order_id)->with(compact('msg'));
        }
    }

    /**
     * 4. Añadir miembro a equipo.
     */
    public function addTeamMember(Request $request, BudgetOrderSharedFields $shared){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-member');

            return redirect('admin/orders/'.$shared->order_id.'/edit/teams')
                ->withErrors($validator)
                ->withInput();
        }

        $team = $request->environment == 'customer'? unserialize($shared->customer_team):unserialize($shared->provider_team);

        if(!$team){
            $team = [(int)$request->member];
        }else{
            //Comprobamos que no exista en el array:
            if(!in_array($request->member, $team)){
                array_push($team, (int)$request->member);
            }    
        }

        if($request->environment == 'customer'){
            $shared->customer_team = serialize($team);
        }else{
            $shared->provider_team = serialize($team);    
        }
        $shared->save();
        
        $msg = trans('textos.miembro_creado');
        return redirect()->route('orders.edit', [$shared->order_id, 'teams'])->with(compact('msg'));
    }

    /**
     * 5. Eliminar miembro de equipo.
     */
    public function deleteTeamMember(Request $request, BudgetOrderSharedFields $shared){
        // $validator = $this->validator($request->all());
        // if($validator->fails()){
        //     return redirect('admin/orders/'.$shared->order_id.'/edit/teams')
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        $team = $request->environment == 'customer'? unserialize($shared->customer_team):unserialize($shared->provider_team);
        $new_team = [];
        foreach($team as $t){
            if($t != $request->member){
                array_push($new_team, $t);    
            }
        }

        if($request->environment == 'customer'){
            $shared->customer_team = count($new_team)? serialize($new_team):NULL;
        }else{
            $shared->provider_team = count($new_team)? serialize($new_team):NULL;    
        }
        $shared->save();
        
        $msg = trans('textos.miembro_deleted');
        return redirect()->route('orders.edit', [$shared->order_id, 'teams'])->with(compact('msg'));
    }
}
