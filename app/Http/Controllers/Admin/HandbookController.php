<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

//Models:
use App\Models\Content;
use App\Models\Handbook;

class HandbookController extends Controller{
    /**
     * 1. Índice de contenidos.
     * 2. Obtener contenido.
     */
    
    private $option = 'manual';
    private $module = 'handbook';

    /**
     * 1. Índice de contenidos.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'manual',
            'module' => $this->module
        ]; 
        
        return view('admin.handbook.index')->with(compact('header'));   
    }

    /**
     * 2. Obtener contenido.
     */
    public function show(Request $request, $code = false){
        if($request->ajax() && $code){
            $content = Content::where('code', $code)
            ->orderBy('created_at', 'DESC')
            ->first();

            if($content){
                $idm = (session()->get('locale') != NULL)? session()->get('locale'):App::getLocale();

                $titulo = unserialize($content->title);
                $destacado = unserialize($content->excerpt);
                $contenido = unserialize($content->content);

                $data['titulo'] = $titulo[$idm];
                $data['destacado'] = $destacado[$idm];
                $data['contenido'] = $contenido[$idm];

                return response()->json($data);

            }else{
                return response()->json(false);
            }

        }else{
            return redirect('/');
        } 
    }
}
