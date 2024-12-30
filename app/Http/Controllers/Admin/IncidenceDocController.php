<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Incidence;
use App\Models\IncidenceDoc;

class IncidenceDocController extends Controller{
    /**
     * 1. Guardar documentos de la incidencia.
     * 2. Eliminar documento.
     */
    
    public function __construct(){
        $this->middleware('module_setted:quality');
        //$this->middleware('incidence_get', ['only' => ['store']]);
    }

    /**
     * 1. Guardar documentos de la incidencia.
     */
    public function store(Request $request, $incidence_id){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/incidences';
                $fileName = uniqid().$file->getClientOriginalName();
                $moved = $file->move($path, $fileName);
                $type = $file->getClientMimeType();
                
                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $doc = new IncidenceDoc;
                    $doc->doc = $fileName; 
                    $doc->incidence_id = $incidence_id;
                    $doc->type = $type;
                    $doc->save();

                    return response()->json($doc);
                }else{
                    return false;
                }
            }    
        }
    }   

    /**
     * 2. Eliminar documento.
     */
    public function destroy(IncidenceDoc $doc){
        File::delete(public_path().'/img/incidences/'.$doc->doc);

        $doc->delete();

        $msg = trans('textos.documento_deleted');
        return redirect()->route('incidences.edit', [$doc->incidence_id, 'docs'])->with(compact('msg')); 
    }
}
