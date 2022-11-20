<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

//Models:
use App\Models\Quiz;
use App\Models\QuizEditions;
use App\Models\User;

class QuizController extends Controller{
    /**
     * 1. Listado de quizzes.
     * 1.1. Listado de quizzes para DataTable.
     * 2. Formulario nuevo quiz.
     * 3. Guardar nuevo quiz.
     * 4. Editar quiz.
     * 5. Actualizar quiz.
     * 6. Eliminar quiz.
     * 7. Actualizar estado de quiz.
     */
    
    private $option = 'concursos';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:quizzes');
        $this->middleware('quiz_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => "required|string|max:255"
            ]);
        }
    } 

    /**
     * 1. Listado de quizzes.
     * 1.1. Listado de quizzes para DataTable.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.concursos_mis',
            'module' => $this->module,
            'slug' => 'quizzes'
        ];   

        return view('admin.quizzes.index')->with(compact('header'));    
    }

    /**
     * 1.1. Listado de quizzes para DataTable.
     */
    public function getQuizzesDT(){
        $data = Quiz::select('quizzes.id', 'quizzes.name', 'quizzes.description', 'quizzes.owner', 'quizzes.status', 'quizzes.created_at', 'users.name AS nombre', 'users.surname')
        ->where('company_id', session('currentCompany'))
        ->leftJoin('users', 'quizzes.owner', '=', 'users.id')
        ->orderBy('quizzes.name', 'ASC');

        return DataTables::of($data)
        ->addColumn('name', function($row){
            return $row->name; 
        })
        ->addColumn('alta', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]);
        })
        ->addColumn('description', function($row){
            return $row->description; 
        })
        ->addColumn('owner', function($row){
            if($row->nombre){
                return $row->nombre.' '.$row->surname;
            }else{
                return '';    
            }             
        })
        ->addColumn('acciones', function($row){
            return view('admin.quizzes.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['description'])
        ->make(true);
    }

    /**
     * 2. Formulario nuevo quiz.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.concurso_nuevo'), 
            'module' => $this->module,
            'slug' => 'quizzes'
        ];

        return view('admin.quizzes.create')->with(compact('header')); 
    }

    /**
     * 3. Guardar nuevo quiz.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/quizzes/create')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        $quiz = new Quiz();
        $quiz->company_id = session('currentCompany');
        $quiz->name = $request->name;
        $quiz->slug = $slug;
        $quiz->description = $request->description;
        $quiz->owner = Auth::user()->id;
        $quiz->status = $status;
        $quiz->save();

        $msg = trans('textos.concurso_creado_ok');
        return redirect()->route('quizzes.index')->with(compact('msg'));
    }

    /**
     * 4. Editar quiz.
     */
    public function edit(Quiz $quiz, $tab = false){
        $header = [
            'option' => $this->option, 
            'suboption' => trans('textos.concurso_editar'), 
            'module' => $this->module,
            'slug' => 'quizzes'
        ];

        $editions = QuizEditions::where('quiz_id', $quiz->id)
        ->orderBy('status', 'DESC')
        ->orderBy('name', 'ASC')
        ->get();

        $owner = User::select('id', 'name', 'surname')->find($quiz->owner);

        $languages = $this->languages;

        return view('admin.quizzes.edit')->with(compact('header', 'quiz', 'tab', 'editions', 'owner', 'languages')); 
    }

    /**
     * 5. Actualizar quiz.
     */
    public function update(Request $request, Quiz $quiz){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/quizzes/'.$quiz->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        $quiz->name = $request->name;
        $quiz->slug = $slug;
        $quiz->description = $request->description;
        $quiz->status = $status;
        $quiz->save();

        $msg = trans('textos.concurso_updated');
        return redirect()->route('quizzes.edit', $quiz)->with(compact('msg'));
    }

    /**
     * 6. Eliminar quiz.
     */
    public function destroy(Quiz $quiz){
        $quiz->delete();

        $msg = trans('textos.concurso_deleted');
        return redirect()->route('quizzes.index')->with(compact('msg')); 
    }

    /**
     * 7. Actualizar estado de quiz.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $quiz = Quiz::find($id);
            $quiz->status = $status;
            $quiz->save();

            $resp = ($quiz)? true:false;
            return response()->json($resp);
        }    
    }
}
