<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\Chat;
use App\Models\ChatRooms;
use App\Models\ChatRoomUsers;

class ChatController extends Controller{
    /**
     * 1. Pantalla principal de chat.
     * 2. Nuevos mensajes privados.
     */
    
    private $option = 'chat'; 
    private $module = 'dashboard';

    /**
     * 1. Pantalla principal de chat.
     */
    public function index(){
        $header = [
            'option' => $this->option, 
            'suboption' => 'textos.chats_mis',
            'module' => $this->module,
            'slug' => 'chats'
        ]; 

        //Salas administradas por el usuario en session:
        $rooms = ChatRoomUsers::select('chat_rooms.id', 'chat_rooms.name')
        ->join('chat_rooms', 'chat_room_users.chat_room_id', '=', 'chat_rooms.id')
        ->where('chat_rooms.status', 1)
        ->where('chat_room_users.user_id', Auth::user()->id)
        ->where('chat_room_users.role', config('constants.CHAT_ROLE_ADMIN_'))
        ->groupBy('chat_rooms.id')
        ->orderBy('chat_rooms.name', 'ASC')
        ->get();

        //Invitaciones pendientes:
        $pending_invitations = ChatRoomUsers::select('chat_rooms.name', 'chat_rooms.description', 'chat_room_users.id')
        ->join('chat_rooms', 'chat_room_users.chat_room_id', '=', 'chat_rooms.id')
        ->where('chat_rooms.status', 1)
        ->where('chat_room_users.user_id', Auth::user()->id)
        ->where('chat_room_users.status', 0)
        ->orderBy('chat_rooms.name', 'ASC')
        ->get();
        
        return view('admin.chats.index')->with(compact('header', 'rooms', 'pending_invitations')); 
    }

    /**
     * 2. Nuevos mensajes privados.
     */
    public function newMessages(Request $request){
        if($request->ajax()){ 
            $data = Chat::where('receiver', Auth::user()->id)
            ->whereNull('read')
            ->whereNull('chat_room_id')
            ->whereNotNull('sender')
            ->count();

            return response()->json($data);
        }    
    }
}
