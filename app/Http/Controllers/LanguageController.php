<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller{
    /**
     * 1. Cambio de idioma.
     * 2. Obtener traducciones de un string.
     * 3. Guardar traducción de string.
     */
    
    /**
	 * 1. Cambio de idioma.
	 * 
	 * @param $lang
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function swap($lang){
	    session()->put('locale', $lang);
	    return redirect()->back();
	}    

	/**
	 * 2. Obtener traducciones de un string.
	 * 
	 * @param $id, $schema
	 * \Illuminate\Http\Response
	 */
	public function stringTranslation(Request $request, $id, $schema){
		if($request->ajax()){
			$data = DB::table($schema)
				->select('translations')
				->where('id', $id)
				->first();
			$response = unserialize($data->translations);

			return response()->json($response);
		}
	}

	/**
     * 3. Guardar traducción de string.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function storeString(Request $request){
    	if($request->ajax()){
    		$id = $request->input('objId');
    		$schema = $request->input('schema');
    		$trans = [];
    		
    		//Idiomas del sistema:
    		$languages = Helpers::languages();
    		foreach($languages as $lang){
    			$idm = $lang[0];
    			$$idm = $request->input($idm);
    			if($$idm != '') $trans[$lang[0]] = $$idm;
    		}

    		$translations = serialize($trans);
	        
    		DB::table($schema)
    			->where('id', $id)
    			->update(['translations' => $translations]);     
	        
    		$msg = trans('textos.traduccion_confirm_ok');
    		return response()->json($msg);
	    }
    }
	
}
