<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use File;

//Models:
use App\Models\Company;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizEditionAnswers;
use App\Models\QuizEditionQuestions;
use App\Models\QuizEditions;
use App\Models\QuizEditionTeams;
use App\Models\Team;

class QuizEditionsController extends Controller{
    /**
     * 1. Listado de ediciones.
     * 1.1. Listado de ediciones para DataTable.
     * 2. Formulario y creación nueva edición.
     * 3. Edición.
     * 4. Actualizar edición.
     * 5. Eliminar edición.
     * 6. Activar - desactivar edición.
     * 7. Eliminar logo.
     * 8. Interfaz del concurso para el usuario.
     * 9. Ranking por edición.
     */
    
    private $option = 'concursos_ediciones';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:quizzes');
        $this->middleware('quiz_get', ['only' => ['create']]);
        $this->middleware('quiz_edition_get', ['only' => ['edit', 'update', 'destroy', 'status', 'deleteLogo']]);
        $this->middleware('quiz_edition_show', ['only' => ['show']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'
            ]);
        }
    } 

    /**
     * 1. Listado de ediciones.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.ediciones',
            'module' => $this->module,
            'slug' => 'quiz-editions'
        ];   

        return view('admin.quiz_editions.index')->with(compact('header'));    
    }

    /**
     * 1.1. Listado de ediciones para DataTable.
     */
    public function getQuizEditionsDT(){
        $data = QuizEditions::select('quizzes.name AS quiz', 'quiz_editions.*')
        ->join('quizzes', 'quiz_editions.quiz_id', '=', 'quizzes.id')
        ->where('quizzes.company_id', session('currentCompany'));


    }

    /**
     * 2. Formulario y creación nueva edición.
     */
    public function create(Quiz $quiz){
        if($quiz){
            //Creamos el nombre de la edición a partir del nombre del quiz más el numeral de la edición:
            $editions = QuizEditions::where('quiz_id', $quiz->id)->count();

            $edition_name = $quiz->name.' '.($editions+1);
            $slug = Str::slug($edition_name);

            $edition = new QuizEditions();
            $edition->quiz_id = $quiz->id;
            $edition->name = $edition_name;
            $edition->slug = $slug;
            $edition->save();

            $msg = trans('textos.edicion_creada_ok');
            return redirect()->route('quiz-editions.edit', $edition)->with(compact('msg'));
        
        }else{
            return redirect('/');
        }
    }

    /**
     * 3. Edición.
     */
    public function edit(QuizEditions $edition, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.concurso_ediciones_editar',
            'module' => $this->module,
            'slug' => 'quiz-editions'
        ];   

        $quiz = Quiz::find($edition->quiz_id);

        //Equipos de la edición.
        $teams_in = QuizEditionTeams::where('quiz_edition_id', $edition->id)
        ->pluck('team_id')->toArray();

        //Obtenemos sólo los equipos que no se encuentren ya inscritos en la edición.
        $teams = Team::select('id', 'name')
        ->where('company_id', session('currentCompany'))
        ->whereNotIn('id', $teams_in)
        ->orderBy('name', 'ASC')
        ->get();

        //Empresas:
        $companies = Company::where('status', 1)
        ->where('is_user', 0)
        ->where('is_ute', 0)
        ->orderBy('name', 'ASC')
        ->pluck('name', 'id')
        ->toArray();

        //Preguntas activas:
        $questions = Question::select('id')->where('quiz_id', $quiz->id)->count();
        $edition_questions = QuizEditionQuestions::select('id')->where('quiz_edition_id', $edition->id)->count();
        $active_questions = $questions == $edition_questions? true:false;

        $languages = $this->languages;

        return view('admin.quiz_editions.edit')->with(compact('header', 'edition', 'quiz', 'tab', 'teams', 'companies', 'active_questions', 'languages'));
    }

    /**
     * 4. Actualizar edición.
     */
    public function update(Request $request, QuizEditions $edition){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/quiz_editions/'.$edition->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;
        if($status == 1){
            QuizEditions::where('quiz_id', $edition->quiz_id)
            ->where('id', '!=', $edition->id)
            ->update(['status' => 0]);
        }

        $start = $request->input('start') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('start')):$request->input('start'):$edition->start;
        $finish = $request->input('finish') != ''? (session()->get('locale') != 'en')? helpers::convertFecha($request->input('finish')):$request->input('finish'):$edition->finish;

        //Convertimos el time (en minutos) a segundos:
        $time = $request->time? $request->time * 60:NULL;

        //Subir logo:
        $fileName = false;
        if($request->hasFile('logo')){
            $fileName = $this->loadLogo($request, 'quizzes');
        }

        //Se actualiza la secuencia de preguntas, si los valores de num_questions y num_days han variado.
        $update_sequence = ($request->num_questions && $request->num_questions != $edition->num_questions) || ($request->num_days && $request->num_days != $edition->num_days)? true:false;

        $edition->name = $request->name;
        $edition->slug = $slug;
        $edition->description = $request->description;
        if($fileName){
            $edition->logo = $fileName;
        }
        $edition->start = $start;
        $edition->finish = $finish;
        $edition->time = $time;
        if($request->num_questions){
            $edition->num_questions = $request->num_questions;
        }
        if($request->num_days){
            $edition->num_days = $request->num_days;
        }
        $edition->status = $status;
        $edition->save();

        //Secuencia de preguntas en el concurso:
        if($update_sequence && $edition->num_questions > 0 && $edition->num_days > 0){
            $questions = QuizEditionQuestions::select('id', 'points', 'date_from', 'date_to')
            ->where('quiz_edition_id', $edition->id)
            ->inRandomOrder()
            ->get();

            //Fecha inicio:
            $start_date = $edition->start?? Carbon::today()->addDay()->format('Y-m-d');
            //Reservamos la fecha de inicio de la edición para guardar posteriormente.
            $edition->start = $start_date;
            $end_date = Carbon::parse($start_date)->addDays($edition->num_days-1)->format('Y-m-d 23:59:59');
            $i = 1;

            foreach($questions as $q){
                if($i > $edition->num_questions){
                    //Fecha inicio:
                    $start_date = Carbon::parse($end_date)->addDay()->format('Y-m-d');
                    //Fecha fin:
                    $end_date = Carbon::parse($start_date)->addDays($edition->num_days-1)->format('Y-m-d 23:59:59');
                    $i = 1;    
                } 

                $i++;                

                $q->date_from = $start_date;  
                $q->date_to = $end_date;
                $q->save(); 
            }

            //Se actualizan las fechas de inicio y fin de la edición:
            $edition->finish = $end_date;
            $edition->save();
        }

        $msg = trans('textos.edicion_updated');
        return redirect()->route('quiz-editions.edit', $edition)->with(compact('msg'));
    }

    /**
     * 5. Eliminar edición.
     */
    public function destroy(QuizEditions $edition){
        $edition->delete();

        $msg = trans('textos.edicion_deleted');
        return redirect()->route('quizzes.edit', [$edition->quiz_id, 'editions'])->with(compact('msg'));    
    }

    /**
     * 6. Activar - desactivar edición.
     */
    public function status(QuizEditions $edition){
        QuizEditions::where('quiz_id', $edition->quiz_id)
        ->update(['status' => 0]);

        $edition->status = 1;
        $edition->save();

        $msg = trans('textos.edicion_deleted');
        return redirect()->route('quizzes.edit', [$edition->quiz_id, 'editions'])->with(compact('msg'));    
    }

    /**
     * 7. Eliminar logo.
     */
    public function deleteLogo(QuizEditions $edition){
        if($edition->logo){
            File::delete(public_path().'/img/editions/'.$edition->logo);

            $edition->logo = NULL;
            $edition->save();
        }

        $msg = trans('textos.imagen_deleted');
        return redirect()->route('quiz-editions.edit', $edition)->with(compact('msg'));
    }

    /**
     * 8. Interfaz del concurso para el usuario.
     */
    public function show(QuizEditions $edition){
        $header = [
            'option' => 'juega', 
            'suboption' => $edition->name,
            'module' => $this->module,
            'slug' => 'quiz-editions'
        ];   
    
        // debe incluir middleware para garantizar que el usuario está invitado.
        
        //Preguntas ya respondidas por el usuario:
        $arr_answered = QuizEditionAnswers::where('user_id', Auth::user()->id)
        ->where('quiz_edition_id', $edition->id)
        ->pluck('quiz_edition_question_id')
        ->toArray();

        $questions = QuizEditionQuestions::select('quiz_edition_questions.id', 'quiz_edition_questions.points', 'questions.id AS questionId', 'questions.name', 'questions.question', 'questions.options', 'questions.videos',
            DB::raw('(SELECT image FROM question_images WHERE question_id = questions.id AND featured = 1) AS image'))
        ->join('questions', 'quiz_edition_questions.question_id', '=', 'questions.id')
        ->where('quiz_edition_questions.quiz_edition_id', $edition->id)
        ->where('quiz_edition_questions.date_from', '<=', Carbon::today())
        ->where('questions.status', 1)
        ->whereNotIn('id', $arr_answered)
        ->inRandomOrder()
        ->get();

        //Deshacemos la serializaciones:
        foreach($questions as $q){
            $arr_q = $q->question? unserialize($q->question):[];
            $q->question_ = !empty($arr_q)? $arr_q[session()->get('locale')]:false;

            $arr_o = $q->options? unserialize($q->options):[];
            $q->options_ = !empty($arr_o)? $arr_o[session()->get('locale')]:false;
        }

        return view('admin.quiz_editions.show')->with(compact('header', 'edition', 'questions'));    
    }

    /**
     * 9. Ranking por edición.
     */
    public function ranking(QuizEditions $edition){
        $header = [
            'option' => 'ranking', 
            'suboption' => $edition->name,
            'module' => $this->module,
            'slug' => 'quiz-editions'
        ];  

        return view('admin.quiz_editions.ranking')->with(compact('header', 'edition'));     
    }

}
