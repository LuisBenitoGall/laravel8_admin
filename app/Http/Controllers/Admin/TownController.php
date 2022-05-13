<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Province;
use App\Models\Town;


class TownController extends Controller{
    /**
     * 1. Listado de poblaciones por provincia.
     * 1.1. Listado de poblaciones para DataTable.
     * 2. Guardar población.
     * 3. Actualizar estado.
     * 4. Actualizar población.
     * 5. Eliminar población.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'province_id' => 'required|integer',
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
     * 1. Listado de poblaciones por provincia.
     */
    public function index(Province $province){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.poblaciones',
            'module' => $this->module,
            'slug' => 'countries'
        ];

        return view('admin.towns.index')->with(compact('header', 'province'));    
    }

    /**
     * 1.1. Listado de poblaciones para DataTable.
     */
    public function getTownsDT($province_id){
        $towns = Town::where('province_id', $province_id)
        ->orderBy('name', 'ASC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('countries.edit');

        foreach($towns as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
        }

        return datatables($towns)->toJson();
    }

    /**
     * 2. Guardar población.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-town');

            return redirect('/admin/towns/'.$request->province_id)
                ->withErrors($validator)
                ->withInput();
        }

        //Slug:
        $slug = Str::slug($request->name);
        //Status:
        $status = $request->status? 1:0;

        Town::create([
            'name' => strtolower($request->name),
            'slug' => $slug,
            'province_id' => $request->province_id,
            'status' => $status
        ]);

        $msg = trans('textos.poblacion_creada_ok');
        return redirect()->route('towns.index', $request->province_id)->with(compact('msg'));
    }

    /**
     * 3. Actualizar estado.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $town = Town::find($id);
            $town->status = $status;
            $town->save();

            $resp = ($town)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 4. Actualizar población.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-update-town');

            return redirect('/admin/towns/'.$request->country_id)
                ->withErrors($validator)
                ->withInput();
        }   

        //Slug:
        $slug = Str::slug($request->name);
        //Status:
        $status = $request->status? 1:0; 

        $town = Town::find($request->id);
        $town->name = $request->name;
        $town->slug = $slug;
        $town->status = $status;
        $town->save();

        $msg = trans('textos.poblacion_updated');
        return redirect()->route('towns.index', $request->province_id)->with(compact('msg'));
    }

    /**
     * 5. Eliminar población.
     *
     * @param  str $town
     * @return \Illuminate\Http\Response
     */
    public function destroy(Town $town){
        $province_id = $town->province_id;

        $town->delete();

        $msg = trans('textos.poblacion_deleted');
        return redirect()->route('towns.index', $province_id)->with(compact('msg'));
    }
    
}
