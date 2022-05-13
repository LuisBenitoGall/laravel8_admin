<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\CompanyModules;
use App\Models\Functionality;
use App\Models\Module;
use Spatie\Permission\Models\Permission;

class ModuleController extends Controller{
    /**
     * 1. Listado de módulos.
     * 1.1. Listado de módulos para DataTable.
     * 2. Formulario nuevo módulo.
     * 3. Guardar nuevo módulo.
     * 4. Editar módulo.
     * 5. Actualizar módulo.
     * 6. Activar / desactivar módulo.
     * 7. Eliminar módulo.
     * 8. Mostrar información del módulo.
     * 9. Desvincular módulo de las empresas.
     */
    
    private $option = 'configuracion';
    private $module = 'settings';
    private $languages;
    
    public function __construct(){
        $this->languages = Helpers::languages();
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'level' => 'required|numeric'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer',
                'name' => 'required|string|max:255',
                'level' => 'required|numeric'
            ]);
        }
    }

    /**
     * 1. Listado de módulos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.modulos',
            'module' => $this->module,
            'slug' => 'modules'
        ]; 
         
        return view('admin.modules.index')->with(compact('header')); 
    }

    /**
     * 1.1. Listado de módulos para DataTable.
     */
    public function getModulesDT(){
        $modules = Module::all();

        //Ampliación de información de los objetos:
        if($modules){
            foreach($modules as $row){
                $translations = unserialize($row->translations);
                $lang = (session()->get('locale') != NULL)? session()->get('locale'):App::getLocale();
                $row->module = isset($translations[$lang])? $translations[$lang]:ucfirst(trans('textos.'.$row->label));

                //Pasamos el valor literal de level:
                $row->nivel = Helpers::levels($row->level);
            }
        }

        return datatables($modules)->toJson(); 
    }

    /**
     * 2. Formulario nuevo módulo.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.modulo_nuevo'),
            'module' => $this->module,
            'slug' => 'modules'
        ];

        return view('admin.modules.create')->with(compact('header'));    
    }

    /**
     * 3. Guardar nuevo módulo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/modules/create')
                ->withErrors($validator)
                ->withInput();
        }    

        $slug = Str::slug($request->name);

        //El valor debe ser único:
        if(Module::where('slug', $slug)->exists()){
            $alert = trans('textos.valor_repetido');
            return redirect()->route('modules.create')->with(compact('alert'));
        }

        $module = Module::create([
            'name' => $request->name,
            'slug' => $slug,
            'color' => $request->color,
            'icon' => $request->icon,
            'level' => $request->level   
        ]);

        //Creando permisos del módulo:
        Permission::create(['name' => 'module_'.$module->slug]);

        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Generar .json de módulos:
        $this->jsonModules();
    
        $msg = trans('textos.modulo_creado_ok');
        return redirect()->route('modules.edit', $module->id)->with(compact('msg'));
    }
    
    /**
     * 4. Editar módulo.
     */
    public function edit(Module $module, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.modulo_editar'),
            'module' => $this->module,
            'slug' => 'modules'
        ];

        $functionalities = Functionality::where('module_id', $module->id)->get();

        return view('admin.modules.edit')->with(compact('header', 'module', 'functionalities', 'tab')); 
    }

    /**
     * 5. Actualizar módulo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/modules/edit/'.$request->id)
                ->withErrors($validator)
                ->withInput();
        }

        $slug = Str::slug($request->name);

        //El valor debe ser único:
        if(Module::where('slug', $slug)->where('id', '!=', $request->id)->exists()){
            $alert = trans('textos.valor_repetido');
            return redirect()->route('modules.edit', $request->id)->with(compact('alert'));
        }
        
        $arrExplanations = [];
        foreach($this->languages as $lang){
            $explanation = 'explanation_'.$lang[0];
            $arrExplanations[$lang[0]] = $request->input($explanation);
        }
        $explanation = serialize($arrExplanations);

        $module = Module::find($request->id);
        $module->name = $request->name;
        $module->slug = $slug;
        $module->color = $request->color;
        $module->icon = $request->icon;
        $module->level = $request->level;
        $module->explanation = $explanation;
        $module->save();  

        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Generar .json de módulos:
        $this->jsonModules();

        $msg = trans('textos.modulo_updated');
        return redirect()->route('modules.edit', $module->id)->with(compact('msg'));
    }

    /**
     * 6. Activar / desactivar módulo.
     *
     * @param  int $id @status
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $module = Module::find($id);
            $module->status = $status;
            $module->save();

            //Si se desactiva el módulo, se debe deshabilitar también para todas las empresas que lo tienen activo:
            if($status == 0){
                $this->deleteCompanyModules($module->id);
            }

            $resp = ($module)? true:false;
            return response()->json($resp);
        }
    }

    /**
     * 7. Eliminar módulo.
     *
     * @param  str $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module){
        //Permisos asociados al módulo:
        Permission::where('name', 'module_'.$module->slug)->delete();

        //Empresas con el módulo activo:
        $this->deleteCompanyModules($module->id);

        $module->delete();

        //Guardar .json de módulos opcionales:
        $this->jsonOptionalModules();

        //Generar .json de módulos:
        $this->jsonModules();

        $msg = trans('textos.modulo_deleted');
        //session()->flash('msg', trans('textos.modulo_creado_ok'));
        return redirect()->route('modules.index')->with(compact('msg'));    
    }

    /**
     * 8. Mostrar información del módulo.
     *
     * @param  str $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug = false){
        if($request->ajax() && $slug){ 
            $data = Module::select('explanation')
            ->where('slug', $slug)
            ->first();

            $explanationArr = $data->explanation? unserialize($data->explanation):false;
            $explanation = $explanationArr? $explanationArr[session()->get('locale')]:'<p class="text-center">'.trans('textos.modulo_sin_info').'</p>';

            return response()->json($explanation);

        }else{
            return response()->json(false);    
        }
    }

    /**
     * 9. Desvincular módulo de las empresas.
     */
    public function deleteCompanyModules($module_id){
        CompanyModules::where('module_id', $module_id)->delete();
    }
    
}
