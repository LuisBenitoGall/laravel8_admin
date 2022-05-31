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
use App\Models\StaffPickCandidates;
use App\Models\StaffPickObservations;

class StaffPickObservationsController extends Controller{
    /**
     * 1. Guardar nuevo comentario.
     * 2. Eliminar comentario.
     */
    
    private $option = 'seleccion_personal';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
    }

     //0. Validator:
    protected function validator(array $data){
        //Validación store:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'candidate_id' => 'required|integer',
                'observations' => 'required'
            ]);   
        }
    }

    /**
     * 1. Guardar nuevo comentario.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/staff-pick-candidates/'.$request->candidate_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        } 

        $meeting_date = $request->meeting_date != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->meeting_date):$request->meeting_date:NULL;  

        //Guardamos comentarios:
        $spo = new StaffPickObservations();
        $spo->candidate_id = $request->candidate_id;
        $spo->meeting_date = $meeting_date;
        $spo->observations = $request->observations;
        $spo->save();

        $msg = trans('textos.comentario_creado');
        return redirect()->route('staff-pick-candidates.edit', [$request->candidate_id, 'observations'])->with(compact('msg'));
    }

    /**
     * 2. Eliminar comentario.
     */
    public function destroy(StaffPickObservations $observation){
        $observation->delete();

        $msg = trans('textos.comentario_eliminado');
        return redirect()->route('staff-pick-candidates.edit', [$observation->candidate_id, 'observations'])->with(compact('msg'));
    }
}
