<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;

//Models:
use App\Models\Content;
use App\Models\ContentImage;

class ContentImageController extends Controller{
    /**
     * 1. Guardar imagen de contenido.
     * 2. Eliminar imagen de contenido.
     * 3. Destacar imagen.
     * 4. Imagen thumbnail.
     */
    
    /**
     * 1. Guardar imagen de contenido.
     *
     * @param \Illuminate\Http\Request $request $id
     */
    public function store(Request $request, $content){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/contents';
                $fileName = Helpers::slugFiles(uniqid().$file->getClientOriginalName());
                $moved = $file->move($path, $fileName);

                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $img = new ContentImage;
                    $img->image = $fileName;
                    $img->content_id = $content;
                    $img->save();
                }else{
                    return false;
                }
            }   
        }
    }

    /**
     * 2. Eliminar imagen de contenido.
     *
     * @param  obj $request string $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $content, $image){
        if($image != ''){
            $foto = ContentImage::select('image')
            ->where('id', $image)
            ->first();

            File::delete(public_path().'/img/contents/'.$foto->image);
            ContentImage::where('id', $image)->delete();

            $msg = trans('textos.imagen_deleted');
            return redirect()->route('contents.edit', $content)->with(compact('msg'));  
        
        }else{
            return redirect('/');
        } 
    } 

    /**
     * 3. Destacar imagen.
     */
    public function setFeatured(Request $request, $content, $image){
        if($content && $image != ''){   
            DB::table('content_images')
            ->where('content_id', $content)
            ->update(['featured' => 0]);

            $img = ContentImage::find($image);
            $img->featured = 1;
            $img->save();

        }else{
            return redirect('/');
        }  
    }

    /**
     * 4. Imagen thumbnail.
     */
    public function setThumb(Request $request, $content, $image){
        if($content && $image != ''){   
            DB::table('content_images')
            ->where('content_id', $content)
            ->update(['thumbnail' => 0]);

            $img = ContentImage::find($image);
            $img->thumbnail = 1;
            $img->save();

            echo $img;

        }else{
            return redirect('/');
        }  
    }
}
