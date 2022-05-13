<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Notifications\ChatRoomsAccessRequest;
use App\Notifications\ChatRoomsInvitationReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

//Models:
use App\Models\ChatRooms;
use App\Models\ChatRoomUsers;
use App\Models\Company;
use App\Models\User;

class ChatRoomsController extends Controller{
    /**
     * 1. Guardar nueva sala de chat.
     * 2. Solicitar acceso a salas.
     * 3. Invitar usuarios.
     */
    
    //0. Validator:
    protected function validator(array $data){
        //Validación save:
        if(isset($data['submit_save'])){
            return Validator::make($data, [
                'name' => 'required|string|max:255'
            ]);

        //Validación solicitar acceso:
        }elseif(isset($data['submit_get_access'])){
            return Validator::make($data, [
                'admin_id' => 'required|integer'
            ]);

        //Validación invitar usuarios:
        }elseif(isset($data['submit_invite'])){
            return Validator::make($data, [
                'chat_room_id' => 'required|integer',
                'users' => 'required'
            ]);
        }
    } 

    /**
     * 1. Guardar nueva sala de chat.
     */
    public function store(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-new-chat-room');

            return redirect('/admin/chat')
                ->withErrors($validator)
                ->withInput();
        }

        $status = $request->status? 1:0;

        $chat_room = ChatRooms::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $status
        ]);

        //Guardamos administrador:
        ChatRoomUsers::create([
            'user_id' => Auth::user()->id,
            'chat_room_id' => $chat_room->id,
            'role' => config('constants.CHAT_ROLE_ADMIN_'),
            'status' => 1   
        ]);

        $msg = trans('textos.chat_sala_creada');
        return redirect()->route('chat.index')->with(compact('msg'));
    }

    /**
     * 2. Solicitar acceso a salas.
     */
    public function getAccess(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            return redirect('/admin/chat')
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::find($request->admin_id);

        //Envío de notificaciones:
        //1. Destinatario:
        $user->title = trans('textos.solicitud_acceso_chat_salas');
        $user->sender = Auth::user()->name.' '.Auth::user()->surname;
        $user->user_id = Auth::user()->id;
        $user->content = trans('textos.solicitud_acceso_chat_salas', ['user_id' => Auth::user()->id]);
        $user->notify(new ChatRoomsAccessRequest($user));

        //2. Remitente:
        Auth::user()->title = trans('textos.solicitud_acceso_chat_salas');
        Auth::user()->sender = Auth::user()->name.' '.Auth::user()->surname;
        Auth::user()->user_id = Auth::user()->id;
        Auth::user()->content = trans('textos.solicitud_enviada_');
        Auth::user()->notify(new ChatRoomsAccessRequest(Auth::user()));

        $msg = trans('textos.solicitud_enviada');
        return redirect()->route('chat.index')->with(compact('msg'));
    }

    /**
     * 3. Invitar usuarios.
     */
    public function inviteUsers(Request $request){
        $validator = $this->validator($request->all());
        if($validator->fails()){
            //Indicamos el origen de la acción realizada para manejar el modal:
            $validator->getMessageBag()->add('accion', 'modal-invite-users');

            return redirect('/admin/chat')
                ->withErrors($validator)
                ->withInput();
        }   

        //Guardamos usuarios invitados:
        if(count($request->users)){
            $chat_room = ChatRooms::find($request->chat_room_id);

            foreach($request->users as $ru){
                $guest = new ChatRoomUsers();
                $guest->user_id = $ru;
                $guest->chat_room_id = $request->chat_room_id;
                $guest->role = config('constants.CHAT_ROLE_USER_');
                $guest->status = '0';
                $guest->save();

                //Envío de email:
                $user = User::select('id', 'email', 'name', 'surname')->find($ru);

                $emailFrom = config('constants.EMAIL_');
                $emailTo = $user->email;
                $data['usuario'] = $user->name.' '.$user->surname;
                $data['chat'] = $chat_room->name;
                $company = Company::find(session('currentCompany'));
                $data['company'] = $company;

                Mail::send('emails.send-chat-invitation', $data, function($message) use($emailFrom, $company, $emailTo){
                    $message->from($emailFrom, $company->name);
                    $message->to($emailTo);
                    $message->subject(trans('textos.chat_invitacion')); 
                }); 

                //Envío de notificación:
                $user->title = trans('textos.chat_sala_invitacion');
                $user->sender = Auth::user()->name.' '.Auth::user()->surname;
                $user->user_id = Auth::user()->id;
                $user->content = trans('textos.chat_invitacion_email_texto').' <b>'.$chat_room->name.'</b>';
                $user->notify(new ChatRoomsInvitationReceived($user));
            }
        }
        
        $msg = trans('textos.invitaciones_enviadas');
        return redirect()->route('chat.index')->with(compact('msg')); 
    }
}
