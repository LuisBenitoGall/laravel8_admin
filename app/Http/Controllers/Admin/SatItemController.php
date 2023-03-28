<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\SatItem;

class SatItemController extends Controller{
    /**
     * 1. Guardar item.
     * 2. Actualizar observaciones por ajax.
     */
    
    private $module = 'sat';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:sat');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación store:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'sat_id' => 'required|integer',
                'observations' => 'required'
            ]);
        }
    }

    /**
     * 1. Guardar item.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/sat/'.$request->sat_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $doc = NULL;
        if($request->hasFile('doc')){
            $file = $request->file('doc');
            $path = public_path().'/docs/sat';
            $fileName = uniqid().$file->getClientOriginalName();
            $moved = $file->move($path, $fileName);

            if($moved){
                $doc = $fileName;
            }
        }

        $row = new SatItem();
        $row->sat_id = $request->sat_id;
        $row->observations = $request->observations;
        $row->doc = $doc;
        $row->user_id = Auth::user()->id;
        $row->status = 1;
        $row->save();

        $msg = trans('textos.informacion_guardada');
        return redirect()->route('sat.edit', [$request->sat_id, 'sale'])->with(compact('msg'));
    }

    /**
     * 2. Actualizar observaciones por ajax.
     */
    public function updateObservations(Request $request, $id, $observations){
        if($request->ajax()){
            SatItem::where('id', $id)
            ->update(['observations' => $observations]);

            return response()->json(true);
        }    
    }
}
