<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

//Models:
use App\Models\DayTypesCriterions;

class DayTypesCriterionsController extends Controller{
    /**
     * 1. Criterios de calendario.
     * 2. Guardar criterio.
     * 3. Actualizar criterio.
     * 4. Generar .json de criterios.
     */
    
    private $option = 'calendario';
    private $module = 'calendar';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:calendar');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required',
                'method' => 'required'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|numeric',
                'name' => 'required'
            ]);
        }
    }

    /**
     * 1. Criterios de calendario.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.calendario_criterios',
            'module' => $this->module,
            'slug' => 'calendar-criterions'
        ];  

        $criterions = DayTypesCriterions::all();

        return view('admin.calendar_criterions.index')->with(compact('header', 'criterions'));   
    }

    /**
     * 2. Guardar criterio.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-criterion');

            return redirect('/admin/calendar-criterions')
                ->withErrors($validator)
                ->withInput();
        }   

        $r = new DayTypesCriterions();
        $r->name = $request->name;
        $r->description = $request->description;
        $r->method = $request->method;
        $r->save(); 

        //Generando .json:
        $this->jsonCriterions();

        $msg = trans('textos.criterio_creado');
        return redirect()->route('calendar-criterions.index')->with(compact('msg'));
    }

    /**
     * 3. Actualizar criterio.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-criterion');

            return redirect('/admin/calendar-criterions')
                ->withErrors($validator)
                ->withInput();
        }   

        DayTypesCriterions::where('id', $request->id)
        ->update(['name' => $request->name, 'description' => $request->description]); 

        //Generando .json:
        $this->jsonCriterions();

        $msg = trans('textos.criterio_updated');
        return redirect()->route('calendar-criterions.index')->with(compact('msg'));
    }

    /**
     * 4. Generar .json de criterios.
     */
    public function jsonCriterions(){
        $data = DayTypesCriterions::all();
        $jsondata = json_encode($data);
        file_put_contents(storage_path()."/json/calendar-criterions.json", $jsondata);
    }
}
