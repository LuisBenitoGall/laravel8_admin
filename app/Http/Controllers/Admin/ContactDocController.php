<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Contact;
use App\Models\ContactDoc;

class ContactDocController extends Controller{
    /**
     * 1. Guardar documento.
     * 2. Eliminar documento.
     */
    
    public function __construct(){
        $this->middleware('module_setted:crm');
        //$this->middleware('contact_get', ['only' => ['store']]);
    }

    /**
     * 1. Guardar documento.
     */
    public function store(Request $request, $contact){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/contacts';
                $fileName = uniqid().$file->getClientOriginalName();
                $moved = $file->move($path, $fileName);
                $type = $file->getClientMimeType();

                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $doc = new ContactDoc;
                    $doc->doc = $fileName;
                    $doc->contact_id = $contact;
                    $doc->type = $type;
                    $doc->save();
                }else{
                    return false;
                }
            }    
        }
    }

    /**
     * 2. Eliminar documento.
     *
     * @param  obj $doc
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactDoc $doc){
        if($doc != ''){
            File::delete(public_path().'/img/contacts/'.$doc->doc);

            $doc->delete();

            $msg = trans('textos.archivo_deleted');
            return redirect()->route('contacts.edit', [$doc->contact_id, 'docs'])->with(compact('msg')); 
        
        }else{
            return redirect('/');
        } 
    }
}
