<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Unit;

class UnitController extends Controller{
    /**
     * 1. Listado de unidades de venta.
     * 1.1. Listado de unidades para DataTable.
     * 2. Formulario nueva unidad.
     * 3. Guardar nueva unidad.
     * 4. Editar unidad.
     * 5. Actualizar unidad.
     * 6. Generar .json de unidades.
     * 7. Activar / desactivar unidad.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255|unique:units,name,NULL,id,deleted_at,NULL',
                'symbol' => 'nullable|max:4'
            ],[
                'name.unique' => trans('textos.unidad_existe')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255|unique:units,name,'.$data['id'].',id,deleted_at,NULL',
                'symbol' => 'nullable|max:4'
            ],[
                'name.unique' => trans('textos.unidad_existe')
            ]);
        }
    } 

    /**
     * 1. Listado de unidades de venta.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.unidades_venta',
            'module' => $this->module,
            'slug' => 'units'
        ];

        //Importación de unidades desde .json. Se ejecuta 1 vez.
        Unit::import();

        return view('admin.units.index')->with(compact('header'));
    } 

    /**
     * 1.1. Listado de unidades para DataTable.
     */
    public function getUnitsDT(){
        $units = Unit::orderBy('name', 'ASC')->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('units.edit');
        $eliminar = Auth::user()->can('units.destroy');

        foreach($units as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;
        }

        return datatables($units)->toJson();
    }

    /**
     * 2. Formulario nueva unidad.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.unidad_venta_nueva',
            'module' => $this->module,
            'slug' => 'units'
        ]; 
        
        return view('admin.units.create')->with(compact('header'));   
    }

    /**
     * 3. Guardar nueva unidad.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/units/create')
            ->withErrors($validator)
            ->withInput();    
        }

        $slug = Str::slug($request->name);
        $status = $request->input('status')? 1:0;

        $unit = Unit::create([
            'name' => $request->name,
            'slug' => $slug,
            'symbol' => $request->symbol,
            'translations' => NULL,
            'status' => $status
        ]);

        //Generar .json de unidades:
        $this->jsonUnits();

        $msg = trans('textos.unidad_venta_creada');
        return redirect()->route('units.edit', $unit)->with(compact('msg'));
    }

    /**
     * 4. Editar unidad.
     */
    public function edit(Unit $unit){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.unidad_venta_editar',
            'module' => $this->module,
            'slug' => 'units'
        ]; 
        
        return view('admin.units.edit')->with(compact('header', 'unit')); 
    }

    /**
     * 5. Actualizar unidad.
     */
    public function update(Request $request, Unit $unit){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/units/'.$unit->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        $slug = Str::slug($request->name);
        $status = $request->input('status')? 1:0;

        $unit->name = $request->name;
        $unit->slug = $slug;
        $unit->symbol = $request->symbol;
        $unit->status = $status;
        $unit->save(); 

        //Generar .json de unidades:
        $this->jsonUnits();

        $msg = trans('textos.unidad_venta_updated');
        return redirect()->route('units.edit', $unit)->with(compact('msg'));
    }
    
    /**
     * 6. Generar .json de unidades.
     *
     * @return .json
     */
    public function jsonUnits(){
        $units = Unit::all();
        $jsondata = json_encode($units);
        file_put_contents(storage_path()."/json/units.json", $jsondata);
    }

    /*
     * 7. Activar / desactivar unidad.
     *
     * @param int $id @state
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $unit = Unit::find($id);
            $unit->status = $status;
            $unit->save();

            //Generar .json de unidades:
            $this->jsonUnits();

            $resp = ($unit)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 8. Eliminar unidad.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit){
        $unit->delete();  

        //Generar .json de unidades:
        $this->jsonUnits();

        $msg = trans('textos.unidad_venta_deleted');
        return redirect()->route('units.index')->with(compact('msg'));   
    }
}
