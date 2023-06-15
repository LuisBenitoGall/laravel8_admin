<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\ProjectPurchases;

class ProjectPurchasesController extends Controller{
    /**
     * 1. Vincular compra a proyecto.
     */
    
    private $option = 'proyectos';
    private $module = 'projects';

    public function __construct(){
        $this->middleware('module_setted:projects');
        $this->middleware('project_purchases', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'project_id' => 'required|integer',
                'orders' => 'required'
            ]);
        }
    }

    /**
     * 1. Vincular compra a proyecto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-purchase');

            return redirect('/admin/projects/'.$request->project_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        foreach($request->orders as $order){
            $pp = new ProjectPurchases();
            $pp->project_id = $request->project_id;
            $pp->order_id = $order;
            $pp->save();
        }

        $msg = trans('textos.compras_vinculadas');
        if($request->invoice){
            return redirect()->route('invoices.edit-purchase', $request->invoice)->with(compact('msg'));
        }elseif($request->return){
            return redirect()->route('orders.edit', $request->return)->with(compact('msg'));    
        }else{
            return redirect()->route('projects.edit', [$request->project_id, 'purchases'])->with(compact('msg'));
        }
    }
}
