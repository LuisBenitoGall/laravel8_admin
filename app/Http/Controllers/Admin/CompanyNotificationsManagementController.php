<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\Company;
use App\Models\CompanyNotificationsManagement;
use App\Models\Employee;
use App\Models\Module;

class CompanyNotificationsManagementController extends Controller{
    /**
     * 1. Gestión de notificaciones por módulo y empresa.
     * 2. Guardar gestores de notificaciones.
     */
    
    private $option = 'empresas';  
    private $module = 'companies';

    /**
     * 1. Gestión de notificaciones por módulo y empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.notificaciones_gestion',
            'module' => $this->module,
            'slug' => 'companies'
        ]; 
        
        $modules = Module::orderBy('label', 'ASC')->get();

        $employees_ = Employee::companyEmployees(session('currentCompany'));
        $employees = [];
        foreach($employees_ as $row){
            $employees[$row->id] = $row->name.' '.$row->surname;  
        }

        return view('admin.company_notifications_management.index')->with(compact('header', 'modules', 'employees'));    
    }

    /**
     * 2. Guardar gestores de notificaciones.
     */
    public function store(Request $request){
        //Eliminamos los registros anteriores de la empresa.
        CompanyNotificationsManagement::where('company_id', session('currentCompany'))
        ->delete();

        $modules = Module::select('id', 'slug')->get();

        foreach($modules as $module){
            if($request->input($module->slug)){
                foreach($request->input($module->slug) as $row){
                    $cnm = new CompanyNotificationsManagement();
                    $cnm->company_id = session('currentCompany');
                    $cnm->user_id = $row;
                    $cnm->module_id = $module->id;
                    $cnm->save();
                }
            }
        }

        $msg = trans('textos.notificaciones_gestion_updated');
        return redirect()->route('company-notifications-management.index')->with(compact('msg'));
    }
}
