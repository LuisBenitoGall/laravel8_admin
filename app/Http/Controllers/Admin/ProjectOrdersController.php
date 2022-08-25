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
}
