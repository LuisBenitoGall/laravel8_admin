<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Company;
use App\Models\CompanyModules;
use App\Models\Module;

class CompanyModulesController extends Controller{
    /**
     * 1. Listado de módulos por empresa.
     * 2. Guardar módulo de empresa.
     * 3. Eliminar módulo de empresa.
     * 4. Comprobar si un módulo está activo o no para la empresa.
     */
    
    private $option = 'cuenta_mi';
    private $module = 'account';

    /**
     * 1. Listado de módulos por empresa. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.empresa_modulos', 
            'module' => $this->module,
            'slug' => 'company-modules'
        ];

        $modules = Module::where('status', 1)->get();
        $companyModulesObj = CompanyModules::select('module_id')
        ->where('company_id', session('currentCompany'))
        ->get();

        $companyModules = [];
        foreach($companyModulesObj as $row){
            array_push($companyModules, $row->module_id);
        }

        return view('admin.company_modules.index')->with(compact('header', 'modules', 'companyModules'));
    }

    /**
     * 2. Guardar módulo de empresa.
     */
    public function store($slug = false){
        if($slug){  
            $module = Module::select('id')->where('slug', $slug)->first();

            if($module){
                $cm = new CompanyModules();
                $cm->company_id = session('currentCompany');
                $cm->module_id = $module->id;
                $cm->save();

                //Añadimos módulo a la session:
                \Session::push('modules', $module->id);

                return redirect()->route('company-modules.index');  
            }else{
                return redirect('/');   
            }

        }else{
            return redirect('/'); 
        }
    }

    /**
     * 3. Eliminar módulo de empresa.
     */
    public function destroy($slug = false){
        if($slug){  
            $module = Module::select('id')->where('slug', $slug)->first();

            if($module){
                CompanyModules::where('company_id', session('currentCompany'))
                ->where('module_id', $module->id)
                ->delete();

                //Módulos de la empresa para session:
                $this->setCompanyModules(session('currentCompany'));

                return redirect()->route('company-modules.index');  
            }else{
                return redirect('/');   
            }

        }else{
            return redirect('/'); 
        }   
    }

    /**
     * 4. Comprobar si un módulo está activo o no para la empresa.
     */
    public function isSetted(Request $request, $slug){
        if($request->ajax()){
            $setted = CompanyModules::select('company_modules.id')
            ->join('modules', 'company_modules.module_id', '=', 'modules.id')
            ->where('company_modules.company_id', session('currentCompany'))
            ->where('modules.slug', $slug)
            ->first();

            $resp = ($setted)? true:false;
            return response()->json($resp);

        }else{
            return response()->json(false);
        }
    }
}
