<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

//Models:
use App\Models\Module;
use App\Models\Preference;

class PreferenceController extends Controller{
	/**
	 * 1. Guardar preferencia. 
	 * 2. Eliminar preferencia.
	 */
	
	/**
     * 1. Guardar preferencia.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        if($request->ajax()){	
        	if($request->input('name') != ''){
        		$segments = explode('/', $request->input('url'));
        		$module = Module::select('id')->where('slug', $segments[4])->first();
        		$module_id = $module? $module->id:NULL;

	        	$pref = new Preference();
	            $pref->name = $request->input('name');
	            $pref->url = $request->input('url');
	            $pref->user_id = Auth::user()->id;
	            $pref->module_id = $module_id;
	            $pref->save(); 

	        	$msg = trans('textos.preferencia_creada');
	            return response()->json($msg);
	        }
        }	
    }

    /**
     * 2. Eliminar preferencia.
     *
     * @param  \App\preference $preference
     * @return \Illuminate\Http\Response
     */
    public function destroy(Preference $preference){
    	$preference->delete();

    	$msg = '';

    	return redirect()->route('dashboard')->with(compact('msg'));    
    }    
}
