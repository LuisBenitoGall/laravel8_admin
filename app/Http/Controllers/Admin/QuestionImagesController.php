<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use File;

//Models:
use App\Models\Question;
use App\Models\QuestionImages;

class QuestionImagesController extends Controller{
    /**
     * 1. Guardar imagen de la cuestión.
     * 2. Eliminar imagen de la cuestión.
     * 3. Destacar imagen.
     * 4. Imagen thumbnail.
     */
    
    public function __construct(){
        $this->middleware('module_setted:quizzes');
    }
    
    /**
     * 1. Guardar imagen de la cuestión.
     *
     * @param \Illuminate\Http\Request $request $id
     */
    public function store(Request $request, $question){
        if($request->ajax()){ 
            if($request->hasFile('file')){
                $file = $request->file('file');
                $path = public_path().'/img/questions';
                $fileName = Helpers::slugFiles(uniqid().$file->getClientOriginalName());
                $moved = $file->move($path, $fileName);

                //si el archivo ha sido guardado correctamente:
                if($moved){
                    $img = new QuestionImages;
                    $img->image = $fileName;
                    $img->question_id = $question;
                    $img->save();
                }else{
                    return false;
                }
            }   
        }
    }

    /**
     * 2. Eliminar imagen de la cuestión.
     *
     * @param  obj $request string $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $question, $image){
        if($image != ''){
            $foto = QuestionImages::select('image')
            ->where('id', $image)
            ->first();

            File::delete(public_path().'/img/questions/'.$foto->image);
            QuestionImages::where('id', $image)->delete();

            $msg = trans('textos.imagen_deleted');
            return redirect()->route('questions.edit', $question)->with(compact('msg'));  
        
        }else{
            return redirect('/');
        } 
    } 

    /**
     * 3. Destacar imagen.
     */
    public function setFeatured(Request $request, $question, $image){
        if($question && $image != ''){   
            DB::table('question_images')
            ->where('question_id', $question)
            ->update(['featured' => 0]);

            $img = QuestionImages::find($image);
            $img->featured = 1;
            $img->save();

        }else{
            return redirect('/');
        }  
    }

    /**
     * 4. Imagen thumbnail.
     */
    public function setThumb(Request $request, $question, $image){
        if($question && $image != ''){   
            DB::table('question_images')
            ->where('question_id', $question)
            ->update(['thumbnail' => 0]);

            $img = QuestionImages::find($image);
            $img->thumbnail = 1;
            $img->save();

        }else{
            return redirect('/');
        }  
    }
}
