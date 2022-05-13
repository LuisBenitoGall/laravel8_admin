<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\QuizEditionQuestions;
use App\Models\QuizEditions;

class QuizEditionQuestionsController extends Controller{
    /**
     * 1. Listado de cuestiones de la edición para DataTable. 
     * 2. Eliminar cuestión. 
     */
    
    private $option = 'concurso_ediciones_cuestiones';
    private $module = 'quizzes';
    private $languages;  

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:quizzes');
    }

    /**
     * 1. Listado de equipos de la edición para DataTable.
     */
    public function getQuizEditionQuestionsDT(QuizEditions $edition){
        $questions = QuizEditionQuestions::select('quiz_edition_questions.id AS qeq_id', 'quiz_edition_questions.question_id', 'quiz_edition_questions.order', 'questions.*')
        ->join('questions', 'quiz_edition_questions.question_id', '=', 'questions.id')
        ->where('quiz_edition_questions.quiz_edition_id', $edition->id)
        ->orderBy('quiz_edition_questions.order', 'ASC')
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('quiz-editions.edit');

        //Ampliación de información de los objetos:
        foreach($questions as $row){
            $row->tipo = Helpers::questionTypes($row->type);
            $row->tipo_respuesta = Helpers::answerValueTypes($row->answer_type); 

            //Permisos para las acciones:
            $row->editar = $editar;
        }

        return datatables($questions)->toJson();
    }

    /**
     * 2. Eliminar cuestión.
     */
    public function destroy(QuizEditionQuestions $edition_question){
        $edition_question->delete();

        $msg = trans('textos.cuestion_deleted');
        return redirect()->route('quiz-editions.edit', [$edition_question->quiz_edition_id, 'questions'])->with(compact('msg'));    
    }
}
