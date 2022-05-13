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

class CountryController extends Controller{
    /**
     * 1. Listado de países.
     * 1.1. Listado de países para DataTable.
     * 2. Guardar país.
     * 3. Actualizar estado.
     * 4. Actualizar país.
     * 5. Eliminar país.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255|unique:countries,name,NULL,id,deleted_at,NULL'
            ],[
                'name.unique' => trans('textos.pais_existe_aviso')
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => "required|string|max:255|unique:countries,name,".$data['id'].",id,deleted_at,NULL"
            ],[
                'name.unique' => trans('textos.pais_existe_aviso')
            ]);
        }
    } 

    /**
     * 1. Listado de países.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.paises',
            'module' => $this->module,
            'slug' => 'countries'
        ];

        return view('admin.countries.index')->with(compact('header'));    
    }

    /**
     * 1.1. Listado de países para DataTable.
     */
    public function getCountriesDT(){
        $countries = Country::all();

        //Permisos del usuario:
        $editar = Auth::user()->can('countries.edit');
        $eliminar = Auth::user()->can('countries.destroy');

        foreach($countries as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar;     
        }

        return datatables($countries)->toJson();
    }

    /**
     * 2. Guardar país.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-country');

            return redirect('/admin/countries')
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        //Status:
        $status = $request->status? 1:0;

        Country::create([
            'name' => strtolower($request->name),
            'slug' => $slug,
            'code' => $request->code,
            'alfa2' => $request->alfa2,
            'alfa3' => $request->alfa3,
            'flag' => $request->flag,
            'status' => $status
        ]);

        $msg = trans('textos.pais_creado_ok');
        return redirect()->route('countries.index')->with(compact('msg'));
    }

    /**
     * 3. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $country = Country::find($id);
            $country->status = $status;
            $country->save();

            $resp = ($country)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 4. Actualizar país.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-update-country');

            return redirect('/admin/countries')
                ->withErrors($validator)
                ->withInput();
        }   

        //Slug:
        $slug = Str::slug($request->name);
        //Status:
        $status = $request->status? 1:0; 

        $country = Country::find($request->id);
        $country->name = $request->name;
        $country->slug = $slug;
        $country->code = $request->code;
        $country->alfa2 = $request->alfa2;
        $country->alfa3 = $request->alfa3;
        $country->flag = $request->flag;
        $country->status = $status;
        $country->save();

        $msg = trans('textos.pais_updated');
        return redirect()->route('countries.index')->with(compact('msg'));
    }

    /**
     * 5. Eliminar país.
     *
     * @param  str $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country){
        $country->delete();

        $msg = trans('textos.pais_deleted');
        return redirect()->route('countries.index')->with(compact('msg'));
    }
}
