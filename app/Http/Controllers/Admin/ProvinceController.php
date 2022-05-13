<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Country;
use App\Models\Province;

class ProvinceController extends Controller{
    /**
     * 1. Listado de provincias por país.
     * 1.1. Listado de provincias para DataTable.
     * 2. Guardar provincia.
     * 3. Actualizar estado.
     * 4. Actualizar provincia.
     * 5. Eliminar provincia.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'country_id' => 'required|integer',
                'name' => 'required|string|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'name' => "required|string|max:255"
            ]);
        }
    } 

    /**
     * 1. Listado de provincias por país.
     */
    public function index(Country $country){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.provincias',
            'module' => $this->module,
            'slug' => 'countries'
        ];

        return view('admin.provinces.index')->with(compact('header', 'country'));    
    }

    /**
     * 1.1. Listado de provincias para DataTable.
     */
    public function getProvincesDT($country_id){
        $provinces = Province::where('country_id', $country_id)
        ->orderBy('name', 'ASC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('countries.edit');
        $ver = Auth::user()->can('countries.index');

        foreach($provinces as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->ver = $ver;
        }

        return datatables($provinces)->toJson();
    }

    /**
     * 2. Guardar provincia.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-province');

            return redirect('/admin/provinces/'.$request->country_id)
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        //Status:
        $status = $request->status? 1:0;

        Province::create([
            'name' => strtolower($request->name),
            'slug' => $slug,
            'country_id' => $request->country_id,
            'status' => $status
        ]);

        $msg = trans('textos.provincia_creada_ok');
        return redirect()->route('provinces.index', $request->country_id)->with(compact('msg'));
    }

    /**
     * 3. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $province = Province::find($id);
            $province->status = $status;
            $province->save();

            $resp = ($province)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 4. Actualizar provincia.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-update-province');

            return redirect('/admin/provinces/'.$request->country_id)
                ->withErrors($validator)
                ->withInput();
        }   

        //Slug:
        $slug = Str::slug($request->name);
        //Status:
        $status = $request->status? 1:0; 

        $province = Province::find($request->id);
        $province->name = $request->name;
        $province->slug = $slug;
        $province->status = $status;
        $province->save();

        $msg = trans('textos.provincia_updated');
        return redirect()->route('provinces.index', $request->country_id)->with(compact('msg'));
    }

    /**
     * 5. Eliminar provincia.
     *
     * @param  str $province
     * @return \Illuminate\Http\Response
     */
    public function destroy(Province $province){
        $country_id = $province->country_id;

        $province->delete();

        $msg = trans('textos.provincia_deleted');
        return redirect()->route('provinces.index', $country_id)->with(compact('msg'));
    }
}
