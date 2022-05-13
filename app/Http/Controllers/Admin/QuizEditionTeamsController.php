<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//Models:
use App\Models\QuizEditions;
use App\Models\QuizEditionTeams;

class QuizEditionTeamsController extends Controller{
    /**
     * 1. Listado de equipos de la edición para DataTable. 
     * 2. Agregar equipos.
     * 3. Eliminar equipo.
     */
    
    private $option = 'concurso_ediciones_equipos';
    private $module = 'quizzes';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:quizzes');
        $this->middleware('quiz_edition_post', ['only' => ['store']]);
        $this->middleware('quiz_edition_team_get', ['only' => ['destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'quiz_edition_id' => 'required|numeric',
                'teams' => 'required'
            ]);
        }
    } 

    /**
     * 1. Listado de equipos de la edición para DataTable.
     */
    public function getQuizEditionTeamsDT(QuizEditions $edition){
        $teams = QuizEditionTeams::select('quiz_edition_teams.*', 'teams.name', 'teams.logo')
        ->join('teams', 'quiz_edition_teams.team_id', '=', 'teams.id')
        ->where('quiz_edition_teams.quiz_edition_id', $edition->id)
        ->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('quiz-editions.edit');

        //Ampliación de información de los objetos:
        foreach($teams as $row){
            //Permisos para las acciones:
            $row->editar = $editar;
        }

        return datatables($teams)->toJson();
    }

    /**
     * 2. Agregar equipos.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-add-team');

            return redirect('admin/quiz-editions/'.$request->quiz_edition_id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        if(!empty($request->teams)){
            foreach($request->teams as $team_id){
                $team = new QuizEditionTeams();
                $team->quiz_edition_id = $request->quiz_edition_id;
                $team->team_id = $team_id;
                $team->status = 1;
                $team->save();
            }
        }

        $msg = trans('textos.equipos_agregados');
        return redirect()->route('quiz-editions.edit', [$request->quiz_edition_id, 'teams'])->with(compact('msg'));
    }

    /**
     * 3. Eliminar equipo.
     */
    public function destroy(QuizEditionTeams $edition_team){
        $edition_team->delete();

        $msg = trans('textos.edicion_deleted');
        return redirect()->route('quiz-editions.edit', [$edition_team->quiz_edition_id, 'teams'])->with(compact('msg'));    
    }
}
