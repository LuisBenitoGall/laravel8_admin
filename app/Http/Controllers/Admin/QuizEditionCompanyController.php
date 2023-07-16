<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

//Models:
use App\Models\Quiz;
use App\Models\QuizEditionCompany;
use App\Models\QuizEditions;

class QuizEditionCompanyController extends Controller{
    /**
     * 1. Listado de empresas de la edición para DataTable.
     * 2. Agregar empresas.
     * 3. Eliminar empresas.
     * 4. Actualizar estado de empresa en la edición.
     */
    
    private $option = 'concurso_ediciones_empresas';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = helpers::languages();

        $this->middleware('module_setted:quizzes');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'quiz_edition_id' => 'required|numeric',
                'companies' => 'required'
            ]);
        }
    } 

    /**
     * 1. Listado de empresas de la edición para DataTable.
     */
    public function getQuizEditionCompaniesDT(QuizEditions $edition){
        $data = QuizEditionCompany::select('quiz_edition_companies.*', 'companies.name', 'companies.tradename')
        ->join('companies', 'quiz_edition_companies.company_id', '=', 'companies.id')
        ->where('quiz_edition_companies.quiz_edition_id', $edition->id)
        ->orderBy('companies.name', 'ASC');

        return DataTables::of($data)
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4]); 
        })
        ->addColumn('competitors', function($row){
            return 1;
        })
        ->addColumn('acciones', function($row){
            return view('admin.quiz_edition_companies.partials.dt-actions', compact('row'));    
        })
        ->make(true);
    }

    /**
     * 2. Agregar empresas.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-company');

            return redirect('admin/quiz-editions/'.$request->quiz_edition_id.'/edit/companies')
            ->withErrors($validator)
            ->withInput();  
        }

        foreach($request->companies as $co){
            $qec = QuizEditionCompany::firstOrCreate(
                ['quiz_edition_id' => $request->quiz_edition_id, 'company_id' => $co],
                ['quiz_edition_id' => $request->quiz_edition_id, 'company_id' => $co]
            );
        }  

        $msg = trans('textos.empresas_agregadas');
        return redirect()->route('quiz-editions.edit', [$request->quiz_edition_id, 'companies'])->with(compact('msg'));
    }

    /**
     * 3. Eliminar empresas.
     */
    public function destroy(QuizEditionCompany $company){
        $company->forceDelete();

        $msg = trans('textos.empresa_desvinculada');
        return redirect()->route('quiz-editions.edit', [$company->quiz_edition_id, 'companies'])->with(compact('msg'));
    }

    /**
     * 4. Actualizar estado de empresa en la edición.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $qec = QuizEditionCompany::find($id);
            $qec->status = $status;
            $qec->save();

            $resp = ($qec)? true:false;
            return response()->json($resp);
        }    
    }
}
