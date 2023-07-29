<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Question;
use App\Models\QuizEditionQuestions;
use App\Models\QuizEditions;

class QuizEditionQuestionsController extends Controller{
    /**
     * 1. Listado de cuestiones de la edición para DataTable. 
     * 2. Eliminar cuestión. 
     * 3. Activar - desactivar pregunta en la edición.
     * 4. Activar - desactivar todas las preguntas en la edición.
     * 5. Setear puntos por pregunta.
     * 6. Setear puntos para todas las preguntas.
     */
    
    private $option = 'concurso_ediciones_cuestiones';
    private $module = 'quizzes';
    private $languages;  

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:quizzes');
    }

    /**
     * 1. Listado de equipos de la edición para DataTable.
     */
    public function getQuizEditionQuestionsDT(QuizEditions $edition, $disabled){
        $data = Question::select('questions.*',
            DB::raw('(SELECT id FROM quiz_edition_questions WHERE question_id = questions.id AND quiz_edition_id = '.$edition->id.') AS qeq_id'),
            DB::raw('(SELECT sort FROM quiz_edition_questions WHERE question_id = questions.id AND quiz_edition_id = '.$edition->id.') AS sort'),
            DB::raw('(SELECT points FROM quiz_edition_questions WHERE question_id = questions.id AND quiz_edition_id = '.$edition->id.') AS points')
        )
        ->where('questions.quiz_id', $edition->quiz_id);

        $disabled = $disabled == 1? 'disabled':'';

        return DataTables::of($data)
        ->addColumn('sort', function($row){
            return $row->sort;
        })
        ->addColumn('name', function($row){
            return $row->name;
        })
        ->addColumn('question', function($row){
            $question = $row->question? unserialize($row->question):false;
            if($question){
                return $question[session()->get('locale')];
            }else{
                return '';
            }
        })
        ->addColumn('type', function($row){
            if($row->type){
                return helpers::questionTypes($row->type);
            }else{
                return '';
            }    
        })
        ->addColumn('points', function($row) use($disabled){
            if($row->points){

                return '<input type="text" class="form-control text-right setPoints" value="'.$row->points.'" data-id="'.$row->qeq_id.'" '.$disabled.'>';
            }else{
                return '';
            }
        })
        ->addColumn('acciones', function($row) use($disabled){
            return view('admin.quiz_edition_questions.partials.dt-actions', compact('row', 'disabled'));    
        })
        ->rawColumns(['question', 'points'])
        ->make(true);
    }

    /**
     * 2. Eliminar cuestión.
     */
    public function destroy(QuizEditionQuestions $edition_question){
        $edition_question->delete();

        $msg = trans('textos.cuestion_deleted');
        return redirect()->route('quiz-editions.edit', [$edition_question->quiz_edition_id, 'questions'])->with(compact('msg'));    
    }

    /**
     * 3. Activar - desactivar pregunta en la edición.
     */
    public function store(Request $request, $edition_id, $question_id){
        if($request->ajax()){ 
            $exist = QuizEditionQuestions::where('quiz_edition_id', $edition_id)
            ->where('question_id', $question_id)
            ->first();

            if($exist){
                $exist->forceDelete();
            }else{
                $row = new QuizEditionQuestions();
                $row->quiz_edition_id = $edition_id;
                $row->question_id = $question_id;
                $row->save();
            }

            return response()->json(true);
        }
    }

    /**
     * 4. Activar - desactivar todas las preguntas en la edición.
     */
    public function allQuestions(QuizEditions $edition, $status){
        if($status == 1){
            $questions = Question::select('id')
            ->where('quiz_id', $edition->quiz_id)
            ->get();

            foreach($questions as $q){
                QuizEditionQuestions::firstOrCreate(
                    ['quiz_edition_id' => $edition->id, 'question_id' => $q->id],
                    ['quiz_edition_id' => $edition->id, 'question_id' => $q->id]
                );
            }

        }else{
            QuizEditionQuestions::where('quiz_edition_id', $edition->id)->forceDelete();    
        }

        $msg = trans('textos.preguntas_actualizadas');
        return redirect()->route('quiz-editions.edit', [$edition->id, 'questions'])->with(compact('msg'));  
    }

    /**
     * 5. Setear puntos por pregunta.
     */
    public function setPoints(QuizEditionQuestions $edition_question, $points){
        $edition_question->points = $points;
        $edition_question->save();

        return response()->json(true);
    }

    /**
     * 6. Setear puntos para todas las preguntas.
     */
    public function setPointsAll($edition_id, $points){
        QuizEditionQuestions::where('quiz_edition_id', $edition_id)
        ->update(['points' => $points]);

        return response()->json(true);    
    }
}
