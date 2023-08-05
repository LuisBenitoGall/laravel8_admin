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
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Question;
use App\Models\QuestionImages;
use App\Models\Quiz;
use App\Models\QuizEditionAnswers;
use App\Models\QuizEditionQuestions;

class QuestionController extends Controller{
    /**
     * 1. Listado de cuestiones.
     * 1.1. Listado de cuestiones para DataTable.
     * 2. Formulario nueva cuestión.
     * 3. Guardar cuestión.
     * 4. Editar cuestión.
     * 5. Actualizar cuestión.
     * 6. Eliminar cuestión.
     * 7. Estado de la cuestión.
     * 8. Importar Mucibo Quiz.
     */
    
    private $option = 'concursos';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:quizzes');
        $this->middleware('question_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'type' => 'required|integer',
                'answer_type' => 'required_if:type,==,'.config('constants.QUESTION_TEST_'),
                'quiz_id' => 'required|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);
        }
    } 

    /**
     * 1. Listado de cuestiones.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuestiones_mis',
            'module' => $this->module,
            'slug' => 'questions'
        ];   

        //Importación Mucibo DB:
        //$this->importMuciboQuiz();

        return view('admin.questions.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de cuestiones para DataTable.
     */
    public function getQuestionsDT($quiz_id){
        $data = Question::where('quiz_id', $quiz_id);

        return DataTables::of($data)
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
        ->addColumn('acciones', function($row){
            return view('admin.questions.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['question'])
        ->make(true);
    }

    /**
     * 2. Formulario nueva cuestión.
     */
    public function create($quiz = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuestion_nueva',
            'module' => $this->module,
            'slug' => 'questions'
        ];   

        return view('admin.questions.create')->with(compact('header', 'quiz'));      
    }

    /**
     * 3. Guardar cuestión.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/questions/create')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        $question = Question::create([
            'company_id' => session('currentCompany'),
            'quiz_id' => $request->quiz_id,
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
            'answer_type' => $request->answer_type,
            'status' => $status
        ]);

        $msg = trans('textos.cuestion_creada_ok');
        return redirect()->route('questions.edit', $question)->with(compact('msg'));
    }

    /**
     * 4. Editar cuestión.
     */
    public function edit(Question $question, $tab=false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuestion_editar',
            'module' => $this->module,
            'slug' => 'questions'
        ];   

        $quiz = Quiz::find($question->quiz_id);

        $images = $question->images()->get();

        $languages = $this->languages;

        return view('admin.questions.edit')->with(compact('header', 'question', 'tab', 'quiz', 'images', 'languages'));
    }

    /**
     * 5. Actualizar cuestión.
     */
    public function update(Request $request, Question $question){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/questions/'.$question->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //dd($question, $request->all());

        $status = $request->status? 1:0;

        $arrQuestions = [];
        $strQuestions = NULL;
        foreach($this->languages as $lang){
            $q = 'question_'.$lang[0];
            if($request->input($q)){
                $arrQuestions[$lang[0]] = $request->input($q);
            }
        }
        $strQuestions = serialize($arrQuestions);

        $arrOptions = [];
        $strOptions = NULL;
        $option = 'options_'.config('constants.IDM_');
        if($request->input($option)){
            $arrOptions[config('constants.IDM_')] = $request->input($option);

            $strOptions = serialize($arrOptions);
        }

        $keywords = isset($request->keywords)? $request->keywords:NULL;

        //Convertimos el time (en minutos) a segundos:
        $time = $request->time? $request->time * 60:NULL;

        $question->name = $request->name;
        $question->slug = Str::slug($request->name);
        $question->question = $strQuestions;
        $question->options = $strOptions;
        $question->right_option = $request->right_option;
        $question->videos = $request->videos;
        $question->url = $request->url;
        $question->keywords = $keywords;
        $question->keywords_min = $request->keywords_min;
        $question->time = $time;
        $question->status = $status;
        $question->save();

        $msg = trans('textos.cuestion_updated');
        return redirect()->route('questions.edit', $question)->with(compact('msg'));
    }

    /**
     * 6. Eliminar cuestión.
     */
    public function destroy(Question $question){
        $question->delete();

        $msg = trans('textos.cuestion_deleted');
        return redirect()->route('questions.index')->with(compact('msg'));
    }

    /**
     * 7. Estado de la cuestión.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $question = Question::find($id);
            $question->status = $status;
            $question->save();

            $resp = ($question)? true:false;
            return response()->json($resp);
        }    
    }

    /**
     * 8. Importar Mucibo Quiz.
     */
    public function importMuciboQuiz(){
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('question_images')->truncate();
        DB::table('questions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = DB::connection('mysql_mucibo')->table('questions')->get();   

        $quiz = Quiz::select('id')->where('slug', 'test-quiz')->first();

        if($quiz){
            foreach($data as $r){
                $translations = $r->translations? unserialize($r->translations):false;
                $description = $r->description? unserialize($r->description):false;
                $answers = $r->answers? unserialize($r->answers):false;

                $q = new Question();
                $q->company_id = session('currentCompany');
                $q->quiz_id = $quiz->id;
                $q->name = $r->name;
                $q->slug = Str::slug($r->name);
                $q->translations = $translations? serialize($translations):NULL;
                $q->question = $description? serialize($description):NULL;
                $q->options = $answers? serialize($answers):NULL;
                $q->right_option = $r->correct_answer;
                $q->videos = $r->video;
                $q->type = 10;
                //$q->answer_type = 
                //$q->keywords = 
                //$q->keywords_min = 
                //$q->time = 
                $q->status = 1;
                $q->deleted_at = $r->deleted_at;
                $q->save();

                //Imágenes:
                $i = new QuestionImages();
                $i->image = $r->image;
                $i->question_id = $q->id;
                $i->featured = 1;
                $i->save();
            }
        }
    }

}
