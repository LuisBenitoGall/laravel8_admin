<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Functionality;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FunctionalityController extends Controller{
    /**
     * 1. Guardar nueva funcionalidad.
     * 2. Actualizar funcionalidad.
     * 3. Generar permisos de la funcionalidad.
     * 4. Eliminar funcionalidad.
     */
    
    private $option = 'configuracion';
    
    public function __construct(){
        
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'functionality' => 'required|string|max:255',
                'label' => 'required|string|max:255'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'label' => 'required|string|max:255'
            ]);
        }
    }

    /**
     * 1. Guardar nueva funcionalidad.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-functionality');
            
            return redirect('admin/modules/'.$request->module_id.'/edit')
                ->withErrors($validator)
                ->withInput();
        }    

        $slug = Str::slug($request->functionality);

        //El valor debe ser único:
        if(Functionality::where('slug', $slug)->exists()){
            $alert = trans('textos.funcionalidad_repetida');
            return redirect()->route('modules.edit', $request->module_id)->with(compact('alert'));
        }

        $label = Str::slug($request->label);
        //Label se genera con _ como separador, por lo que lo sustituimos:
        $label = str_replace('-', '_', $label);

        $f = new Functionality;
        $f->name = strtolower($request->functionality);
        $f->slug = $slug;
        $f->label = $label;
        $f->module_id = $request->module_id;
        $f->save();

        //Creando permisos de la funcionalidad:
        $this->setPermissions($f);

        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Generar .json de funcionalidades:
        $this->jsonFunctionalities();

        $msg = trans('textos.funcionalidad_creada_ok');
        return redirect()->route('modules.edit', [$request->module_id, 'functionalities'])->with(compact('msg'));
    }

    /**
     * 2. Actualizar funcionalidad.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'update');
            
            return redirect('admin/modules/'.$request->module_id.'/edit/functionalities')
                ->withErrors($validator)
                ->withInput();
        }    

        $label = Str::slug($request->label);
        //Label se genera con _ como separador, por lo que lo sustituimos:
        $label = str_replace('-', '_', $label);

        $f = Functionality::find($request->id);
        $f->label = $label;
        $f->save();

        //Comprobamos si la funcionalidad tiene creados sus permisos:
        $exist_permissions = Permission::where('name', 'LIKE', $f->slug.'.%')->get();
        if($exist_permissions->count() == 0){
            $this->setPermissions($f);
        }

        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Generar .json de funcionalidades:
        $this->jsonFunctionalities();

        $msg = trans('textos.funcionalidad_updated');
        return redirect()->route('modules.edit', [$f->module_id, 'functionalities'])->with(compact('msg'));
    }

    /**
     * 3. Generar permisos de la funcionalidad.
     */
    public function setPermissions($functionality){
        $methods = config('constants.METHODS_');

        foreach($methods as $method){
            Permission::create(['name' => strtolower($functionality->slug).'.'.$method[0]]);
        }
    }

    /**
     *  4. Eliminar funcionalidad.
     *
     * @param  str $funcionality
     * @return \Illuminate\Http\Response
     */
    public function destroy(Functionality $functionality){
        $module_id = $functionality->module_id;

        $functionality->delete();

        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Generar .json de funcionalidades:
        $this->jsonFunctionalities();

        $msg = trans('textos.funcionalidad_deleted');
        return redirect()->route('modules.edit', [$module_id, 'functionalities'])->with(compact('msg'));
    }
}
