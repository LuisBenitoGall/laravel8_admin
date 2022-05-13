<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//Models:
use App\Models\ChatRoomUsers;
use App\Models\User;

class ChatRoomUsersController extends Controller{
    /**
     * 1. Obtener nuevos usuarios para sala de chat.
     * 2. Aceptación invitaciones.
     */

    /**
     * 1. Obtener nuevos usuarios para sala de chat.
     */
    public function getNewUsers(Request $request, $id){
        if($request->ajax()){
            //Usuarios activos de la sala:
            $users_in = ChatRoomUsers::where('chat_room_id', $id)
            ->pluck('user_id')->toArray();

            //Sólo usuarios que no se encuentren ya vinculados a la sala.
            $data = User::select('id', 'name', 'surname')
            ->whereNotIn('id', $users_in)
            ->where('status', 1)
            ->where('isAdmin', 1)
            ->orderBy('name', 'ASC')
            ->get();

            return response()->json($data);  
        }
    }

    /**
     * 2. Aceptación invitaciones.
     */
    public function acceptInvitations(Request $request){
        foreach($request->all() as $i => $r){
            //Verificamos si el índice del registro empieza por 'chat_room_'
            if(strpos($i, "chat_room") === 0){
                //Extraemos el id:
                $p = explode('_', $i);

                //Validamos o...
                if($r == 1){
                    ChatRoomUsers::where('id', $p[2])->update(['status' => 1]);    

                //Eliminamos:
                }else{
                    ChatRoomUsers::where('id', $p[2])->delete();
                }
            }
        }

        $msg = trans('textos.invitaciones_tramitadas');
        return redirect()->route('chat.index')->with(compact('msg'));
    }
}
