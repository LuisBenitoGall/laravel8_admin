<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\UserCostCenters;

class UserCostCentersController extends Controller{
    /**
     * 1. Guardar centro de coste de usuario.
     * 2. Eliminar centro de coste de usuario.
     */
    
    private $module = 'companies';
    
    public function __construct(){
        $this->middleware('user_post', ['only' => ['store']]);
        $this->middleware('cost_center_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        return Validator::make($data, [
            'user_id' => 'required|numeric',
            'cost_center_id' => 'required|numeric'
        ]);
    }

    /**
     * 1. Guardar centro de coste de usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/users/'.$request->user_id.'/company')
                ->withErrors($validator)
                ->withInput();
        }   

        //Comprobamos que no exista previamente el vínculo:
        $exist = UserCostCenters::where('user_id', $request->user_id)
        ->where('cost_center_id', $request->cost_center_id)
        ->first();

        if($exist){
            $alert = trans('textos.registro_duplicado_alert');
            return redirect()->route('users.edit', [$request->user_id, 'company'])->with(compact('alert')); 
        }

        $ucc = new UserCostCenters();
        $ucc->user_id = $request->user_id;
        $ucc->cost_center_id = $request->cost_center_id;
        $ucc->save();

        $msg = trans('textos.usuario_updated');
        return redirect()->route('users.edit', [$request->user_id, 'company'])->with(compact('msg'));
    }

    /**
     * 2. Eliminar centro de coste de usuario.
     *
     * @param  int  $cost_center
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserCostCenters $cost_center){
        $user_id = $cost_center->user_id;
        
        $cost_center->delete();

        $msg = trans('textos.centro_coste_deleted');
        return redirect()->route('users.edit', [$user_id, 'company'])->with(compact('msg'));
    }
}
