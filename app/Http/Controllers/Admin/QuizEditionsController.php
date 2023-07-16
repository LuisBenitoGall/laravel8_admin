<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use File;

//Models:
use App\Models\Company;
use App\Models\Quiz;
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
     */
    
    private $option = 'concursos_ediciones';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:quizzes');
        $this->middleware('quiz_get', ['only' => ['create']]);
        $this->middleware('quiz_edition_get', ['only' => ['edit', 'update', 'destroy', 'status', 'deleteLogo']]);
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
        $editions = QuizEditions::select('quizzes.name AS quiz', 'quiz_editions.*')
        ->join('quizzes', 'quiz_editions.quiz_id', '=', 'quizzes.id')
        ->where('quizzes.company_id', session('currentCompany'))
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('quiz-editions.edit');
        $eliminar = Auth::user()->can('quiz-editions.destroy');

        //Ampliación de información de los objetos:
        foreach($editions as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);
            $row->start_ = $row->start? Carbon::parse($row->start)->format($this->languages[session()->get('locale')][4]):'';
            $row->finish_ = $row->finish? Carbon::parse($row->finish)->format($this->languages[session()->get('locale')][4]):'';

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($editions)->toJson();
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

        $languages = $this->languages;

        return view('admin.quiz_editions.edit')->with(compact('header', 'edition', 'quiz', 'tab', 'teams', 'companies', 'languages'));
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

        $start = $request->input('start') != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->input('start')):$request->input('start'):NULL;
        $finish = $request->input('finish') != ''? (session()->get('locale') != 'en')? Helpers::convertFecha($request->input('finish')):$request->input('finish'):NULL;

        //Convertimos el time (en minutos) a segundos:
        $time = $request->time? $request->time * 60:NULL;

        //Subir logo:
        $fileName = false;
        if($request->hasFile('logo')){
            $fileName = $this->loadLogo($request, 'quizzes');
        }

        $edition->name = $request->name;
        $edition->slug = $slug;
        $edition->description = $request->description;
        if($fileName){
            $edition->logo = $fileName;
        }
        $edition->start = $start;
        $edition->finish = $finish;
        $edition->time = $time;
        $edition->status = $status;
        $edition->save();

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
            'option' => $this->option, 
            'suboption' => 'textos.concurso_ediciones_editar',
            'module' => $this->module,
            'slug' => 'quiz-editions'
        ];   
    
        // debe incluir middleware para garantizar que el usuario está invitado.


        return view('admin.quiz_editions.show')->with(compact('header', 'edition'));    
    }
}
