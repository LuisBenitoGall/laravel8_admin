<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\ProjectOrders;

class ProjectOrdersController extends Controller{
    /**
     * 1. Vincular venta a proyecto.
     * 2. Desvincular pedido - presupuesto de proyecto.
     */

    private $option = 'proyectos';
    private $module = 'projects';

    public function __construct(){
        $this->middleware('module_setted:projects');
        $this->middleware('project_post', ['only' => ['store']]);
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
     * 1. Vincular venta a proyecto.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-order');

            return redirect('/admin/projects/'.$request->project_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        foreach($request->orders as $order){
            $pp = new ProjectOrders();
            $pp->project_id = $request->project_id;
            $pp->order_id = $order;
            $pp->save();
        }

        $msg = trans('textos.ventas_vinculadas');
        return redirect()->route('projects.edit', [$request->project_id, 'orders'])->with(compact('msg'));
    }

    /**
     * 2. Desvincular pedido - presupuesto de proyecto.
     */
    public function destroy(Request $request){
        //dd($request->all());
        if(!$request->project || !is_integer((int)$request->project) || !$request->order || !is_integer((int)$request->order)){
            $alert = trans('textos.proyecto_pedido_error');
            return redirect()->route('projects.edit', [$request->project, 'orders'])->with(compact('alert'));
            exit;
        }

        ProjectOrders::where('project_id', $request->project)
        ->where('order_id', $request->order)
        ->delete();

        $msg = trans('textos.proyecto_pedido_desvinculado');
        return redirect()->route('projects.edit', [$request->project, 'orders'])->with(compact('msg'));        
    }
}
