<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Notifications\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;

//Models:
use App\Models\Group;
use App\Models\GroupMembers;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller{
    /**
     * 1. Formulario nuevo mensaje.
     * 2. Guardar mensaje.
     */

    private $option = 'mensajes';  
    private $module = 'dashboard';

    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'content' => 'required|string'
            ], [
                'content.required' => trans('textos.mensaje_obligatorio')
            ]);
        }
    }

    /**
     * 1. Formulario nuevo contenido.
     */
    public function create(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.mensaje_nuevo',
            'module' => $this->module,
            'slug' => ''
        ]; 

        //Array de empresas vinculadas al usuario:
        $user_companies = [];
        foreach(session('companies') as $uc){
            array_push($user_companies, $uc->id);
        }

        $users = User::select('users.id', 'users.name', 'users.surname', 'users.email')
        ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
        ->whereIn('user_companies.company_id', $user_companies)
        ->where('users.status', 1)
        ->whereNull('users.deleted_at')
        ->groupBy('users.id')
        ->orderBy('users.name')
        ->orderBy('users.surname')
        ->get();

        $groups = Group::select('groups.id', 'groups.name', 'groups.description', 'companies.name AS Company')
        ->join('companies', 'groups.company_id', '=', 'companies.id')
        ->whereIn('groups.company_id', $user_companies)
        ->where('groups.status', 1)
        ->orderBy('groups.name')
        ->get();

        return view('admin.messages.create')->with(compact('header', 'users', 'groups'));     
    }  

    /**
     * 2. Guardar mensaje.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('admin/messages/create')
                ->withErrors($validator)
                ->withInput();
        }  

        $message = new Message();
        $message->title = $request->title;
        $message->content = $request->content;
        $message->user_id = Auth::user()->id;
        $message->save();

        $message->sender = Auth::user()->name.' '.Auth::user()->surname;

        //Preparando destinatarios:
        $recipients = [];
        $recipients_id = [];

        $user_companies = [];
        foreach(session('companies') as $uc){
            array_push($user_companies, $uc->id);
        }

        //Integramos los usuarios por empresas o filtro de usuarios:
        if($request->companies_all || $request->users_all){
            $companies = $user_companies;
            $users = [];
        }else{
            $companies = $request->companies? $request->companies:[];
            $users = $request->users? $request->users:[];
        }

        if(!empty($companies)){
            $companies_users = User::select('users.id')
            ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
            ->whereIn('user_companies.company_id', $companies)
            ->where('users.status', 1)
            ->whereNull('users.deleted_at')
            ->where('users.id', '!=', Auth::user()->id)
            ->groupBy('users.id')
            ->get();

            foreach($companies_users as $cu){
                if(!in_array($cu->id, $recipients_id)){
                    array_push($recipients, $cu);
                    array_push($recipients_id, $cu->id);
                }
            }
        }

        if(!empty($users)){
            foreach($users as $user){
                if(!in_array($user, $recipients_id)){
                    $user_ = User::select('id')->find($user);
                    array_push($recipients, $user_);
                    array_push($recipients_id, $user);
                }
            }
        }

        //Integramos usuarios por grupos:
        if($request->groups_all){
            $groups = Group::whereIn('company_id', $user_companies)->pluck('id')->toArray();
        }else{
            $groups = $request->groups? $request->groups:[];
        }

        if(!empty($groups)){
            $groups_users = User::select('users.id')
            ->join('group_members', 'users.id', '=', 'group_members.user_id')
            ->join('groups', 'group_members.group_id', '=', 'groups.id')
            ->whereIn('groups.id', $groups)
            ->where('users.status', 1)
            ->whereNull('users.deleted_at')
            ->where('users.id', '!=', Auth::user()->id)
            ->get();

            foreach($groups_users as $gu){
                if(!in_array($gu->id, $recipients_id)){
                    array_push($recipients, $gu);
                    array_push($recipients_id, $gu->id);
                }
            }
        }

        //Enviar notificación a los destinatarios:
        foreach($recipients as $rec){
            $rec->notify(new UserMessage($message));
        }

        $msg = trans('textos.mensaje_enviado');
        return redirect()->route('dashboard')->with(compact('msg'));
    }
}
