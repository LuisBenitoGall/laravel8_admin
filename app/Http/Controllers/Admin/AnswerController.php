<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\Answer;
use App\Models\Question;

class AnswerController extends Controller{
    /**
     * 1. Guardar nueva respuesta.
     * 2. Listado de respuestas por cuestión para DataTable.
     * 3. Actualizar respuesta.
     * 4. Eliminar respuesta.
     * 5. Estado de la respuesta.
     */
    
    public function __construct(){
        $this->middleware('module_setted:quizzes');
        $this->middleware('answer_get', ['only' => ['destroy']]);
        $this->middleware('answer_post', ['only' => ['update']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'question_id' => 'required|integer',
                'name' => 'required|max:255',
                'is_true' => 'required_without:points',
                'points' => 'required_without:is_true',
                'answer' => 'required'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'id' => 'required|integer', 
                'question_id' => 'required|integer',
                'name' => 'required|max:255',
                'is_true' => 'required_without:points',
                'points' => 'required_without:is_true',
                'answer' => 'required'
            ]);   
        }
    } 

    /**
     * 1. Guardar nueva respuesta.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-answer');

            return redirect('admin/questions/'.$request->question_id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $points = $request->points? $request->points:NULL;
        $is_true = isset($request->is_true)? $request->is_true == 1? 1:'0':NULL;
        $status = $request->status? 1:0;

        $answer = new Answer();
        $answer->question_id = $request->question_id;
        $answer->name = $request->name;
        $answer->slug = $slug;
        $answer->answer = $request->answer;
        $answer->points = $points;
        $answer->is_true = $is_true;
        $answer->status = $status;
        $answer->save();

        $msg = trans('textos.respuesta_creada');
        return redirect()->route('questions.edit', [$request->question_id, 'answers'])->with(compact('msg'));
    }

    /**
     * 2. Listado de respuestas por cuestión para DataTable.
     */
    public function getQuestionAnswersDT(Question $question){
        $answers = Answer::where('question_id', $question->id)->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('questions.edit');
        
        //Ampliación de información de los objetos:
        foreach($answers as $row){
            if($question->answer_type == config('constants.ANSWER_TYPE_BOOLEAN_')){
                $row->value = $row->is_true == 1? ucfirst(trans('textos.verdadero')):ucfirst(trans('textos.falso'));
            }else{
                $row->value = $row->points;
            }

            //Permisos para las acciones:
            $row->editar = $editar;
        }

        return datatables($answers)->toJson();
    }

    /**
     * 3. Actualizar respuesta.
     */
    public function update(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-edit-answer');

            return redirect('admin/questions/'.$request->question_id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $points = $request->points? $request->points:NULL;
        $is_true = isset($request->is_true)? $request->is_true == 1? 1:'0':NULL;
        $status = $request->status? 1:0;

        $answer = Answer::find($request->id);
        $answer->name = $request->name;
        $answer->slug = $slug;
        $answer->answer = $request->answer;
        $answer->points = $points;
        $answer->is_true = $is_true;
        $answer->status = $status;
        $answer->save();

        $msg = trans('textos.respuesta_updated');
        return redirect()->route('questions.edit', [$request->question_id, 'answers'])->with(compact('msg'));
    }

    /**
     * 4. Eliminar respuesta.
     */
    public function destroy(Answer $answer){
        $answer->delete();

        $msg = trans('textos.respuesta_deleted');
        return redirect()->route('questions.edit', [$answer->question_id, 'answers'])->with(compact('msg'));
    }

    /**
     * 5. Estado de la respuesta.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $answer = Answer::find($id);
            $answer->status = $status;
            $answer->save();

            $resp = ($answer)? true:false;
            return response()->json($resp);
        }        
    }
}
