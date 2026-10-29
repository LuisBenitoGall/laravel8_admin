<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\IvaType;

class IvaTypeController extends Controller{
    /**
     * 1. Relación de tipos de IVA.
     * 2. Guardar nuevo tipo.
     * 3. Actualizar tipo.
     * 4. Generar .json de tipos de IVA.
     * 5. Eliminar tipo de IVA.
     * 6. Modificar status de tipo de IVA.
     */
    
    private $option = 'iva_tipos';  
    private $module = 'settings';
    private $iva_types;

    public function __construct(){
        $this->ivaTypes = Cache::get('iva_types');

        // Si los datos no están en caché, realizar la consulta y almacenar en caché
        if ($this->ivaTypes === null) {
            $this->ivaTypes = IvaType::select('id', 'name', 'iva', 'equivalence_surcharge')
            ->where('status', 1)
            ->orderBy('iva', 'ASC')
            ->get();
            Cache::put('iva_types', $this->ivaTypes, now()->addMinutes(10));
        }
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'iva' => 'required|numeric',
                'equivalence_surcharge' => 'nullable|numeric'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'name' => 'required|max:255',
                'iva' => 'required|numeric',
                'equivalence_surcharge' => 'nullable|numeric'
            ]);
        }
    } 

    /**
     * 1. Relación de tipos de IVA.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.listado'),
            'module' => $this->module,
            'slug' => 'iva-types'
        ];

        //Importación de datos desde .json. Se ejecuta 1 vez.
        IvaType::import();

        $types = IvaType::all();

        return view('admin.iva_types.index')->with(compact('header', 'types'));    
    }

    /**
     * 2. Guardar nuevo tipo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-add-type');

            return redirect('admin/iva-types')
            ->withErrors($validator)
            ->withInput();    
        }

        $row = new IvaType();
        $row->name = $request->name;
        $row->iva = $request->iva;
        $row->equivalence_surcharge = $request->equivalence_surcharge? $request->equivalence_surcharge:'0';
        $row->save();

        //Generar .json de datos:
        $this->jsonTypes();

        $msg = trans('textos.tipo_creado');
        return redirect()->route('iva-types.index')->with(compact('msg'));
    }

    /**
     * 3. Actualizar tipo.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            $validator->getMessageBag()->add('accion', 'modal-edit-type');

            return redirect('admin/iva-types')
            ->withErrors($validator)
            ->withInput();    
        }

        $row = IvaType::find($request->id);
        $row->name = $request->name;
        $row->iva = $request->iva;
        $row->equivalence_surcharge = $request->equivalence_surcharge? $request->equivalence_surcharge:'0';
        $row->save();

        //Generar .json de datos:
        $this->jsonTypes();

        $msg = trans('textos.tipo_updated');
        return redirect()->route('iva-types.index')->with(compact('msg'));
    }

    /**
     * 4. Generar .json de tipos de iva.
     */
    public function jsonTypes(){
        $data = IvaType::orderBy('name', 'ASC')->get();
        $jsondata = json_encode($data);
        file_put_contents(storage_path()."/json/iva-types.json", $jsondata);
    }

    /**
     * 5. Eliminar tipo de IVA.
     */
    public function destroy(IvaType $type){
        $type->delete();   

        $msg = trans('textos.tipo_deleted');
        return redirect()->route('iva-types.index')->with(compact('msg')); 
    }

    /**
     * 6. Modificar status de tipo de IVA.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){
            $row = IvaType::where('id', $id)
            ->update(['status' => $status]);

            //Generar .json de datos:
            $this->jsonTypes();

            $resp = ($row)? true:false;
            return response()->json($resp);
        }
    }

}
