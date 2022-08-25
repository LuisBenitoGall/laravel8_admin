<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

//Models:
use App\Models\DayTypesUsersConfig;

class DayTypesUsersConfigController extends Controller{
    /**
     * 1. Guardar registro.
     * 2. Eliminar registro.
     */

    private $option = 'calendario';
    private $module = 'calendar';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();
        
        $this->middleware('module_setted:calendar');
        $this->middleware('user_companies_get', ['only' => ['destroy']]);
        $this->middleware('user_companies_post2', ['only' => ['store']]);
    }
    
    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'user_id' => 'required|integer',
                'year' => 'required|numeric|max:3000',
                'day_type_id' => 'required|integer',
                'qty' => 'required|integer',
            ]);
        }
    }

    /**
     * 1. Guardar registro.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-modify-day-type');

            return redirect('/admin/work-user-calendar/'.$request->user_id)
                ->withErrors($validator)
                ->withInput();
        }

        //Comprobamos que no exista el registro:
        $dtuc = DayTypesUsersConfig::where('user_id', $request->user_id)
        ->where('day_type_id', $request->day_type_id)
        ->where('year', $request->year)
        ->first();

        if($dtuc){
            $dtuc->qty= $request->qty;
            $dtuc->save();
            
        }else{
            $dtuc = new DayTypesUsersConfig();
            $dtuc->user_id = $request->user_id;
            $dtuc->day_type_id = $request->day_type_id;
            $dtuc->year = $request->year;
            $dtuc->qty= $request->qty;
            $dtuc->save();
        }

        $msg = trans('textos.tipo_jornada_updated');
        return redirect()->route('work-user-calendar.index', [$request->user_id, $request->year])->with(compact('msg')); 
    }

    /**
     * 2. Eliminar registro.
     */
    public function destroy($user_id, $day_type_id, $year){
        if($user_id && $day_type_id && $year){
            DayTypesUsersConfig::where('user_id', $user_id)
            ->where('day_type_id', $day_type_id)
            ->where('year', $year)
            ->delete();    

            $msg = trans('textos.registro_deleted');
            return redirect()->route('work-user-calendar.index', [$user_id, $year])->with(compact('msg'));

        }else{
            $alert = trans('textos.no_permisos_accion');
            return redirect('/')->with(compact('alert'));     
        }
    }
}
