<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\UserBusinessAreas;

class UserBusinessAreaController extends Controller{
    /**
     * 1. Guardar área de negocio de usuario.
     * 2. Eliminar área de negocio de usuario.
     */
    
    private $module = 'companies';
    
    public function __construct(){
        $this->middleware('user_post', ['only' => ['store']]);
        $this->middleware('business_area_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        return Validator::make($data, [
            'user_id' => 'required|numeric',
            'business_area_id' => 'required|numeric'
        ]);
    }

    /**
     * 1. Guardar área de negocio de usuario.
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
        $exist = UserBusinessAreas::where('user_id', $request->user_id)
        ->where('business_area_id', $request->business_area_id)
        ->first();

        if($exist){
            $alert = trans('textos.registro_duplicado_alert');
            return redirect()->route('users.edit', [$request->user_id, 'company'])->with(compact('alert')); 
        }

        $ucc = new UserBusinessAreas();
        $ucc->user_id = $request->user_id;
        $ucc->business_area_id = $request->business_area_id;
        $ucc->save();

        $msg = trans('textos.usuario_updated');
        return redirect()->route('users.edit', [$request->user_id, 'company'])->with(compact('msg'));
    }

    /**
     * 2. Eliminar área de negocio de usuario.
     *
     * @param  int  $business_area
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserBusinessAreas $business_area){
        $user_id = $business_area->user_id;
        
        $business_area->delete();

        $msg = trans('textos.area_negocio_deleted');
        return redirect()->route('users.edit', [$user_id, 'company'])->with(compact('msg'));
    }    
}
