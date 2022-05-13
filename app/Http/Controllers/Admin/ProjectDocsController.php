<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Project;
use App\Models\ProjectDocs;

class ProjectDocsController extends Controller{
    /**
     * 1. Guardar documentos del proyecto.
     * 2. Eliminar documento.
     * 3. Destacar imagen.
     * 4. Imagen thumbnail.
     */
    
    public function __construct(){
        $this->middleware('module_setted:projects');
        $this->middleware('project_get', ['only' => ['store']]);
    }

    /**
     * 1. Guardar documentos del proyecto.
     *
     * @param $request, $project
     */
    public function store(Request $request, $project){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/projects';
                $fileName = uniqid().$file->getClientOriginalName();
                $moved = $file->move($path, $fileName);
                $type = $file->getClientMimeType();

                //Comprobamos si existe imagen destacada:
                $row = ProjectDocs::select('id')
                ->where('project_id', $project)
                ->where('featured', 1)
                ->first();
                $featured = $row? false:true;

                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $doc = new ProjectDocs;
                    $doc->doc = $fileName;
                    $doc->project_id = $project;
                    $doc->type = $type;
                    $doc->featured = $featured;
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
    public function destroy(ProjectDocs $doc){
        if($doc != ''){
            File::delete(public_path().'/img/projects/'.$doc->doc);

            $doc->delete();

            $msg = trans('textos.archivo_deleted');
            return redirect()->route('projects.edit', [$doc->project_id, 'docs'])->with(compact('msg')); 
        
        }else{
            return redirect('/');
        } 
    }

    /**
     * 3. Destacar imagen.
     *
     * @param  request $project $image
     */
    public function setFeatured(Request $request, $project, $image){
        if($request->ajax()){ 
            if($project && $image != ''){   
                ProjectDocs::where('project_id', $project)
                ->update(['featured' => 0]);

                $img = ProjectDocs::find($image);
                $img->featured = 1;
                $img->save();

            }else{
                return redirect('/');
            }
        }  
    }

    /**
     * 4. Imagen thumbnail.
     */
    public function setThumb(Request $request, $project, $image){
        if($project && $image != ''){   
            DB::table('project_docs')
            ->where('project_id', $project)
            ->update(['thumbnail' => 0]);

            $img = ProjectDocs::find($image);
            $img->thumbnail = 1;
            $img->save();

        }else{
            return redirect('/');
        }  
    }
}
