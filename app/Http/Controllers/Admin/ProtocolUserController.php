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
use File;
use Carbon\Carbon;

//Models:
use App\Models\Group;
use App\Models\GroupMembers;
use App\Models\Protocol;
use App\Models\ProtocolUser;
use App\Models\User;

class ProtocolUserController extends Controller{
    /**
     * 1. Listado de usuarios por protocolo para DataTable.
     * 2. Guardar usuarios para protocolo.
     * 3. Desvincular usuario del protocolo.
     */
    
    private $option = 'protocolos';
    private $module = 'rrhh';
    private $languages;

    public function __construct(){
        //Idiomas:
        $this->languages = Helpers::languages();

        $this->middleware('module_setted:rrhh');
    }

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'protocol' => 'required|integer'
            ]);    

        //Validación update: 
        }elseif(isset($data['submit_update'])){
            return Validator::make($data, [
                'title' => 'required',
                'status' => 'required|integer'
            ]); 
        }
    }

    /**
     * 1. Listado de usuarios por protocolo para DataTable.
     */
    public function getProtocolUsersDT($protocol_id){
        $data = ProtocolUser::select('protocol_users.id', 'protocol_users.signed_at', 'protocol_users.created_at', 'users.id AS user_id', 'users.name', 'users.surname')
        ->join('users', 'protocol_users.user_id', '=', 'users.id')
        ->orderBy('users.name', 'ASC');

        return DataTables::of($data)
        ->addColumn('user', function($row){
            return $row->name.' '.$row->surname;
        })
        ->addColumn('created_at', function($row){
            return $row->created_at->format($this->languages[session()->get('locale')][4].' H:i:s');
        })
        ->addColumn('signed_at', function($row){
            if($row->signed_at){
                return Carbon::parse($row->signed_at)->format($this->languages[session()->get('locale')][4]);
            }else{
                return '<span class="text-danger">'.ucfirst(trans('textos.pendiente')).'</span>';
            }
        })
        ->addColumn('acciones', function($row){
            return view('admin.protocol_users.partials.dt-actions', compact('row'));    
        })
        ->rawColumns(['signed_at'])
        ->make(true);
    }

    /**
     * 2. Guardar usuarios para protocolo.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/protocols/'.$request->protocol.'/edit')
                ->withErrors($validator)
                ->withInput();
        }

        $users = [];

        //Todos los usuarios:
        if($request->all_users){
            $users = User::join('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->where('users.status', 1)
            ->where('users.isAdmin', 1)
            ->where('user_companies.company_id', session('currentCompany'))
            ->pluck('users.id')
            ->toArray();

        //Por nombres o grupos:
        }else{
            //Por nombres:
            if($request->users){
                $users = array_merge($users, $request->users);
            }

            //Por grupos:
            if($request->groups){
                foreach($request->groups as $g){
                    $gu = GroupMembers::join('users', 'group_members.user_id', '=', 'users.id')
                    ->where('group_members.group_id', $g)
                    ->where('users.status', 1)
                    ->where('users.isAdmin', 1)
                    ->pluck('users.id')
                    ->toArray();

                    $users = array_merge($users, $gu);
                }
            }
        }

        //Quitamos duplicados:
        array_unique($users);

        //Insertamos comprobando que no exista previamente:
        foreach($users as $u){
            ProtocolUser::firstOrCreate(
                ['protocol_id' => $request->protocol, 'user_id' => $u],
                ['protocol_id' => $request->protocol, 'user_id' => $u]
            );
        }

        $msg = trans('textos.usuarios_vinculados_ok');
        return redirect()->route('protocols.edit', [$request->protocol, 'users'])->with(compact('msg'));
    }

    /**
     * 3. Desvincular usuario del protocolo.
     */
    public function destroy(ProtocolUser $user){
        $user->delete();
        
        $msg = trans('textos.usuario_deleted');
        return redirect()->route('protocols.edit', [$user->protocol_id, 'users'])->with(compact('msg'));       
    }
}
