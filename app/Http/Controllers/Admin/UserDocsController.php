<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;

//Models:
use App\Models\UserDoc;

class UserDocsController extends Controller{
    /**
     * 1. Eliminar documento.
     */
    

    /**
     * 1. Eliminar documento.
     */
    public function destroy(UserDoc $doc, $return = false){
        File::delete(public_path().'/docs/users/'.$doc->doc);

        $doc->delete();

        $msg = trans('textos.doc_deleted');

        if($return){
            $p = explode('|', $return);
            return redirect()->route($p[0], $p[1])->with(compact('msg'));   
        }else{
            
        } 
    }
}
