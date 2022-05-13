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
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TeamMembersController extends Controller{
    /**
     * 1. Formulario nuevo miembro.
     * 2. Guardar miembro de equipo.
     * 3. Marcar líder del equipo.
     * 4. Editar miembro del equipo.
     * 5. Actualizar miembro del equipo.
     * 6. Eliminar miembro.
     */
    
    private $option = 'equipos';  
    private $module = 'teams';

    public function __construct(){
        $this->middleware('module_setted:teams');

        $this->middleware('team_get', ['only' => ['create']]);
        $this->middleware('team_post', ['only' => ['store']]);
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'team_id' => 'required|integer',
                'name' => 'required|string|max:75',
                'surname' => 'required|string|max:100',
                'email' => 'required|string|email|max:191|'
            ]);

        //Validación update:
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'user_id' => 'required|integer',
                'name' => 'required|string|max:75',
                'surname' => 'nullable|string|max:100',
                'email' => "nullable|string|email|max:191|unique:users,email,".$data['user_id'].",id,deleted_at,NULL"
            ],[
                'email.unique' => trans('textos.email_existe'),
            ]); 
        }
    }

    /**
     * 1. Formulario nuevo miembro.
     */
    public function create(Team $team){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.miembro_nuevo',
            'module' => $this->module,
            'slug' => 'teams'
        ];  

        $leader = TeamMembers::select('id')
        ->where('team_id', $team->id)
        ->where('leader', 1)
        ->first();
        
        return view('admin.team_members.create')->with(compact('header', 'team', 'leader')); 
    }

    /**
     * 2. Guardar miembro de equipo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/team_members/create')
            ->withErrors($validator)
            ->withInput();    
        }

        //Comprobamos si el usuario ya existe:
        $user = User::select('id', 'email', 'status')
        ->where('email', $request->email)
        ->first();

        if(!$user){
            $random_password = Str::random(8);

            $user = new User();
            $user->name = $request->name;
            $user->surname = $request->surname;
            $user->email = $request->email;
            $user->password = bcrypt($random_password);
            $user->isAdmin = 0;
            $user->status = 1;
            $user->remember_token = $request->input('_token');
            $user->save();

            //Guardamos rol:
            $user->assignRole(config('constants.ROLE_ID_'));
        }

        //Creando el miembro:
        $member = new TeamMembers();
        $member->team_id = $request->team_id;
        $member->user_id = $user->id;
        $member->leader = $request->leader? 1:0;
        $member->save();

        $msg = trans('textos.miembro_creado');
        return redirect()->route('teams.edit', $request->team_id)->with(compact('msg'));
    }

    /**
     * 3. Marcar líder del equipo.
     */
    public function setLeader(TeamMembers $member){
        TeamMembers::where('team_id', $member->team_id)
        ->update(['leader' => 0]);

        $member->leader = 1;
        $member->save();

        $msg = trans('textos.equipo_actualizado_ok');
        return redirect()->route('teams.edit', $member->team_id)->with(compact('msg'));
    }

    /**
     * 4. Editar miembro del equipo.
     */
    public function edit(TeamMembers $member){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.miembro_editar',
            'module' => $this->module,
            'slug' => 'teams'
        ];  

        $user = User::select('id', 'name', 'surname', 'email')->find($member->user_id);

        $team = Team::find($member->team_id);
        
        return view('admin.team_members.edit')->with(compact('header', 'member', 'user', 'team'));    
    }

    /**
     * 5. Actualizar miembro del equipo.
     */
    public function update(Request $request, TeamMembers $member){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/team-members/'.$member->id.'/edit')
            ->withErrors($validator)
            ->withInput();    
        }   

        $user = User::find($member->user_id); 
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->save();

        $msg = trans('textos.miembro_updated');
        return redirect()->route('team-members.edit', $member->id)->with(compact('msg'));
    }

    /**
     * 6. Eliminar miembro.
     */
    public function destroy(TeamMembers $member){
        $member->delete();

        $msg = trans('textos.miembro_deleted');
        return redirect()->route('teams.edit', $member->team_id)->with(compact('msg'));    
    }
}
