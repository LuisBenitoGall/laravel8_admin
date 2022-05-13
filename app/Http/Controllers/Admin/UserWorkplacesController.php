<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\UserWorkplaces;

class UserWorkplacesController extends Controller{
    /**
     * 1. Guardar centro de trabajo de usuario.
     * 2. Eliminar centro de trabajo de usuario.
     */
    
    private $module = 'companies';
    
    public function __construct(){
        $this->middleware('user_post', ['only' => ['store']]);
        $this->middleware('workplace_post', ['only' => ['store']]);
    }

     //0. Validator:
    protected function validator(array $data){
        return Validator::make($data, [
            'user_id' => 'required|numeric',
            'workplace_id' => 'required|numeric'
        ]);
    }

    /**
     * 1. Guardar centro de trabajo de usuario.
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
        $exist = UserWorkplaces::where('user_id', $request->user_id)
        ->where('workplace_id', $request->workplace_id)
        ->first();

        if($exist){
            $alert = trans('textos.registro_duplicado_alert');
            return redirect()->route('users.edit', [$request->user_id, 'company'])->with(compact('alert')); 
        }

        $uwp = new UserWorkplaces();
        $uwp->user_id = $request->user_id;
        $uwp->workplace_id = $request->workplace_id;
        $uwp->save();

        $msg = trans('textos.usuario_updated');
        return redirect()->route('users.edit', [$request->user_id, 'company'])->with(compact('msg'));
    }

    /**
     * 2. Eliminar centro de trabajo de usuario.
     *
     * @param  int  $workplace
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserWorkplaces $workplace){
        $user_id = $workplace->user_id;
        
        $workplace->delete();

        $msg = trans('textos.centro_deleted');
        return redirect()->route('users.edit', [$user_id, 'company'])->with(compact('msg'));
    }    
}
