<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Models:
use App\Models\Phone;

class PhoneController extends Controller{
    /**
     * 1. Guardar teléfono.
     * 2. Eliminar teléfono.
     * 3. Guardar teléfono de usuario.
     */
    
    public function __construct(){
        $this->middleware('phone_get', ['only' => ['destroy']]);
    }
    
    /**
     * 1. Guardar teléfono.
     */
    public function store(Request $request){
        if($request->ajax()){
            $phone = new Phone();
            $phone->workplace_id = $request->input('workplace_id');
            $phone->phone_number = $request->input('phone_number');
            $phone->save();

            return response()->json($phone);
        }   
    }

    /**
     * 2. Eliminar teléfono.
     */
    public function destroy(Request $request, Phone $phone){
        if($request->ajax()){   
            $phone->delete();

            return response()->json(true);
        }
    }

    /**
     * 3. Guardar teléfono de usuario.
     */
    public function storeFromUser(Request $request){
        //Eliminamos los registros anteriores:
        Phone::where('user_id', $request->user_id)->delete();
        
        if($request->phone_number && count($request->phone_number) > 0){
            $i = 0;
            foreach($request->phone_number as $row){
                if($row != ''){
                    $phone = new Phone();
                    $phone->user_id = $request->user_id;
                    $phone->phone_number = $row;
                    $phone->concept = $request->concept[$i];
                    $phone->save();
                    $i++;
                }
            }
        }

        $msg = trans('textos.usuario_updated');
        return redirect()->route('users.edit', [$request->user_id, 'contact'])->with(compact('msg'));   
    }
}
