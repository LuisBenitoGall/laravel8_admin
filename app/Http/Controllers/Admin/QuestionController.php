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
use App\Models\Question;
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
                'edition_id' => 'nullable|integer'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'question' => 'required|string'
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

        return view('admin.questions.index')->with(compact('header'));  
    }

    /**
     * 1.1. Listado de cuestiones para DataTable.
     */
    public function getQuestionsDT(){
        $questions = Question::where('company_id', session('currentCompany'))->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('questions.edit');
        $eliminar = Auth::user()->can('questions.destroy');   
        
        //Ampliación de información de los objetos:
        foreach($questions as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 

            $row->tipo = Helpers::questionTypes($row->type);
        }

        return datatables($questions)->toJson(); 
    }

    /**
     * 2. Formulario nueva cuestión.
     */
    public function create($edition = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.cuestion_nueva',
            'module' => $this->module,
            'slug' => 'questions'
        ];   

        return view('admin.questions.create')->with(compact('header', 'edition'));      
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
            'name' => $request->name,
            'slug' => $slug,
            'type' => $request->type,
            'answer_type' => $request->answer_type,
            'status' => $status
        ]);

        //Si viene declarado edition_id, vinculamos la cuestión con la edición.
        if($request->edition_id){
            //Valor del campo order:
            $num = QuizEditionQuestions::where('quiz_edition_id', $request->edition_id)->count();

            $qeq = new QuizEditionQuestions();
            $qeq->quiz_edition_id = $request->edition_id;
            $qeq->question_id = $question->id;
            $qeq->order = $num+1;
            $qeq->save();
        }

        $msg = trans('textos.cuestion_creada_ok');
        if($request->edition_id){
            return redirect()->route('quiz-editions.edit', [$request->edition_id, 'questions'])->with(compact('msg'));
        }else{
            return redirect()->route('questions.edit', $question)->with(compact('msg'));
        }
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

        $languages = $this->languages;

        return view('admin.questions.edit')->with(compact('header', 'question', 'tab', 'languages'));
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

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        $keywords = isset($request->keywords)? $request->keywords:NULL;

        //Convertimos el time (en minutos) a segundos:
        $time = $request->time? $request->time * 60:NULL;

        $question->name = $request->name;
        $question->slug = $slug;
        $question->question = $request->question;
        $question->videos = $request->videos;
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

}
