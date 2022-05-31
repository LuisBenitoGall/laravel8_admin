<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\File_;

class FileController extends Controller{
    /**
     * 1. Archivos.
     * 2. Guardar archivos.
     */
    
    private $option = 'empresas';
    private $module = 'companies';
    private $languages;

    /**
     * 1. Archivos.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.archivos',
            'module' => $this->module,
            'slug' => 'files'
        ];

        return view('admin.files.index')->with(compact('header'));
    }

    /**
     * 2. Guardar archivos.
     */
    public function store(Request $request){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                //Directorio del año:
                $year_dir = public_path().'/files/'.date('Y').'/';
                if(!file_exists($year_dir)){
                    mkdir($year_dir, 0777, true);
                }

                //Directorio del mes:
                $month_dir = $year_dir.date('m').'/';
                if(!file_exists($month_dir)){
                    mkdir($month_dir, 0777, true);
                }

                $path = $month_dir;
                $directory = '/files/'.date('Y').'/'.date('m').'/';

                $file = $request->file('file');

                //Size:
                $size = $file->getSize();

                //MimeType:
                $mime = $file->getClientMimeType();

                //Extensión del archivo:
                $extension = $file->getClientOriginalExtension();

                $fileName = helpers::slugFiles(uniqid().$file->getClientOriginalName());
                $moved = $file->move($path, $fileName);

                if($moved){
                    $row = new File_;
                    $row->file = $fileName;      
                    $row->path = $directory;
                    $row->company_id = session('currentCompany');
                    $row->ext = $extension;
                    $row->size = $size;
                    $row->mime = $mime;
                    $row->created_by = Auth::user()->id;
                    $row->save();

                    return $row;
                }else{
                    return false;
                }
            }
        }
    }

}
