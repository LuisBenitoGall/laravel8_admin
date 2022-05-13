<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use File;

//Models:
use App\CompanyModules;
use App\Module;
use App\Preference;

class AdminController extends Controller{
	/**
     * 1. Admin dashboard.
     * 2. Obtener provincias por país.
     * 3. Obtener poblaciones por provincia.
     */
   
    private $option = 'dashboard';
    private $module = 'dashboard';
   
	/**
     * 1. Admin dashboard.
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function index(Request $request){
    	$header = ['option' => $this->option, 'suboption' => 'dashboard', 'module' => $this->module];

        // $preferences = Preference::select('preferences.id', 'preferences.name', 'preferences.url', 'modules.color', 'modules.icon')
        // ->leftJoin('modules', 'preferences.module_id', '=', 'modules.id')
        // ->where('preferences.user_id', Auth::user()->id)
        // ->orderBy('modules.level', 'ASC')
        // ->get();

    	//return view('admin.dashboard', compact('header', 'preferences'));
        return view('admin.dashboard', compact('header'));
    }

    /**
     * 2. Obtener provincias por país.
     * 
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function getCountryProvinces(Request $request, $id){
        //Validamos la llamada Ajax:
        if($request->ajax()){
            $data = $this->countryProvinces($id, 1);
            return response()->json($data);  
        }
    }

    /**
     * 3. Obtener poblaciones por provincia.
     * 
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function getProvinceTowns(Request $request, $id){
        //Validamos la llamada Ajax:
        if($request->ajax()){
            $data = $this->provinceTowns($id, 1);
            return response()->json($data);  
        }
        return $data;
    }
}
