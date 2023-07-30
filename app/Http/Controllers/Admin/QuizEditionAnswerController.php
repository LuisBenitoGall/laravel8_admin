<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\QuizEditionAnswers;
use App\Models\QuizEditionQuestions;

class QuizEditionAnswerController extends Controller{
    /**
     * 1. Guardar respuesta de pregunta.
     */
    
    private $option = 'concursos_ediciones';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:quizzes');
    }

    /**
     * 1. Guardar respuesta de pregunta.
     */
    public function store(Request $request){
        if($request->ajax()){ 
            $qeq = QuizEditionQuestions::select('quiz_edition_questions.points', 'questions.right_option')
            ->join('questions', 'quiz_edition_questions.question_id', '=', 'questions.id')
            ->where('quiz_edition_questions.id', $request->question)
            ->first();

            $points = (int)$qeq->right_option == (int)$request->answer? $qeq->points:'0';
            $answer = $request->answer == ''? '0':$request->answer;

            $qea = QuizEditionAnswers::firstOrCreate(
                ['quiz_edition_id' => $request->edition,   
                'quiz_edition_question_id' => $request->question,
                'user_id' => Auth::user()->id
                ],
                ['quiz_edition_id' => $request->edition,
                'quiz_edition_question_id' => $request->question,
                'user_id' => Auth::user()->id, 
                'answer' => $answer, 
                'answer_value' => $points
                ]);
            return $qea;
        }   
    }
}
