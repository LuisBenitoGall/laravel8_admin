<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;

//Models:
use App\Models\Team;
use App\Models\TeamMembers;

class TeamController extends Controller{
    /**
     * 1. Listado de equipos por empresa.
     * 1.1. Listado de equipos para DataTable.
     * 2. Crear equipo.
     * 3. Guardar equipo.
     * 4. Editar equipo.
     * 5. Actualizar equipo.
     * 6. Eliminar equipo.
     * 7. Actualizar estado.
     * 8. Eliminar logo.
     */
    
    private $option = 'equipos';  
    private $module = 'teams';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:teams');
        $this->middleware('team_get', ['only' => ['edit', 'update', 'destroy']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|max:255',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:500'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'name' => "required|string|max:255"
            ]);
        }
    } 

    /**
     * 1. Listado de equipos por empresa.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.equipos_mis',
            'module' => $this->module,
            'slug' => 'teams'
        ];   

        $teams = Team::where('company_id', session('currentCompany'));

        $view = 'list';

        return view('admin.teams.index')->with(compact('header', 'teams', 'view'));  
    }

    /**
     * 1.1. Listado de equipos para DataTable.
     */
    public function getTeamsDT(){
        $teams = Team::where('company_id', session('currentCompany'))->get();

        //Permisos del usuario:
        $editar = Auth::user()->can('teams.edit');
        $eliminar = Auth::user()->can('teams.destroy');

        //Ampliación de información de los objetos:
        foreach($teams as $row){
            //Enviamos las fechas ya formateadas:
            $row->alta = $row->created_at->format($this->languages[session()->get('locale')][4]);

            //Permisos para las acciones:
            $row->editar = $editar;
            $row->eliminar = $eliminar; 
        }

        return datatables($teams)->toJson();
    }

    /**
     * 2. Crear equipo.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.equipo_nuevo',
            'module' => $this->module,
            'slug' => 'teams'
        ];  
        
        return view('admin.teams.create')->with(compact('header'));     
    }

    /**
     * 3. Guardar equipo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/teams/create')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        //Subir logo:
        $fileName = $this->loadLogo($request, 'teams');

        $team = Team::create([
            'company_id' => session('currentCompany'),
            'name' => $request->name,
            'slug' => $slug,
            'logo' => $fileName,
            'status' => $status
        ]);

        $msg = trans('textos.equipo_creado_ok');
        return redirect()->route('teams.index')->with(compact('msg'));
    }

    /**
     * 4. Editar equipo.
     */
    public function edit(Team $team){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.equipo_editar',
            'module' => $this->module,
            'slug' => 'teams'
        ];  

        $members = TeamMembers::select('users.name', 'users.surname', 'team_members.id', 'team_members.leader', 'team_members.created_at')
        ->join('users', 'team_members.user_id', '=', 'users.id')
        ->where('team_members.team_id', $team->id)
        ->orderBy('team_members.leader', 'DESC')
        ->orderBy('users.name')
        ->get();

        return view('admin.teams.edit')->with(compact('header', 'team', 'members'));  
    }

    /**
     * 5. Actualizar equipo.
     */
    public function update(Request $request, Team $team){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/teams/'.$team->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }

        //Slug:
        $slug = Str::slug($request->name);

        $status = $request->status? 1:0;

        //Subir logo:
        $fileName = false;
        if($request->hasFile('logo')){
            $fileName = $this->loadLogo($request, 'teams');
        }

        $team->name = $request->name;
        $team->slug = $slug;
        if($fileName){
            $team->logo = $fileName;
        }
        $team->status = $status;
        $team->save();

        $msg = trans('textos.equipo_actualizado_ok');
        return redirect()->route('teams.edit', $team)->with(compact('msg'));            
    }

    /**
     * 6. Eliminar equipo.
     */
    public function destroy(Team $team){
        if($team->logo){
            File::delete(public_path().'/img/teams/'.$team->logo);
        }

        $team->delete();

        $msg = trans('textos.equipo_deleted');
        return redirect()->route('teams.index')->with(compact('msg'));    
    }

    /**
     * 7. Actualizar estado.
     */
    public function status(Request $request, $id, $status){
        if($request->ajax()){ 
            $team = Team::find($id);
            $team->status = $status;
            $team->save();

            $resp = ($team)? true:false;
            return response()->json($resp);
        }    
    }

    /**
     * 8. Eliminar logo.
     */
    public function deleteLogo(Team $team){
        if($team->logo){
            File::delete(public_path().'/img/teams/'.$team->logo);

            $team->logo = NULL;
            $team->save();
        }

        $msg = trans('textos.imagen_deleted');
        return redirect()->route('teams.edit', $team)->with(compact('msg'));
    }

}
