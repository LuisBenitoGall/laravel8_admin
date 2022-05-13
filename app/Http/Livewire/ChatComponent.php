<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

//Models:
use App\Models\Chat;
use App\Models\ChatRooms;
use App\Models\ChatRoomUsers;
use App\Models\User;

class ChatComponent extends Component{
    /**
     * 1. Render componente.
     * 2. Seleccionar sala de chat.
     * 3. Guardar nuevo mensaje.
     * 4. Abrir sala privada de chat.
     * 5. Recargar página por Pusher.
     */
    
    public $admins;
    public $avatar;
    public $chat_private;
    public $chat_room_name;
    public $chat_room_on;
    public $chat_rooms;
    public $form;
    public $interlocutor;
    public $languages;
    public $message;
    public $messages;
    public $new_messages_in_private_room;
    public $new_messages_in_room;
    public $prev_chat_room;
    public $private_user;
    //public $reload;
    public $users;

    protected $listeners = ['reload'];

    protected $rules = [
        'message' => 'required'
    ];
    
    public function mount(){
        $this->chat_rooms = ChatRooms::select('chat_rooms.*')
        ->join('chat_room_users', 'chat_rooms.id', '=', 'chat_room_users.chat_room_id')
        ->where('chat_rooms.status', 1)
        ->where('chat_room_users.status', 1)
        ->where('chat_room_users.user_id', Auth::user()->id)
        ->get();

        $this->admins = [];
        $this->chat_private = [];
        $this->chat_room_name = false;
        $this->chat_room_on = false;
        $this->form = false;
        $this->interlocutor = false;
        $this->languages = Helpers::languages();
        $this->message = "";
        $this->messages = [];
        $this->new_messages_in_private_room = false;
        $this->new_messages_in_room = false;
        $this->private_user = false;
        $this->users = [];
    }     

    /**
     * 1. Render componente.
     */
    public function render(){
        $this->reset('message');

        //Sala declarada:
        if($this->chat_room_on){
            //Mensajes de la sala:
            $messages = Chat::select('chats.message', 'chats.created_at', 'chats.sender', 'users.name', 'users.surname')
            ->join('users', 'chats.sender', '=', 'users.id')
            ->where('chats.chat_room_id', $this->chat_room_on)
            ->orderBy('chats.created_at', 'DESC')
            ->get()
            ->take(20);

            $this->messages = $messages->count()? $messages:[];  

            $this->prev_chat_room = $this->chat_room_on; 
            $this->interlocutor = false;
            
        //Sin sala: 2 opciones: 
        }else{
            //Chat privado:
            if(!empty($this->chat_private)){
                //Mensajes con el usuario seleccionado:
                $first_condition = ['sender' => $this->private_user, 'receiver' => Auth::user()->id];
                $second_condition = ['sender' => Auth::user()->id, 'receiver' => $this->private_user];

                $messages = Chat::select('chats.message', 'chats.created_at', 'chats.sender', 'users.name', 'users.surname')
                ->join('users', 'chats.sender', '=', 'users.id')
                ->whereNull('chats.chat_room_id')
                ->where(function($query) use($first_condition, $second_condition){
                    $query->where($first_condition)
                    ->orWhere($second_condition);
                })
                ->orderBy('chats.created_at', 'DESC')
                ->get()
                ->take(20);

                $this->messages = $messages->count()? $messages:[];

                $this->interlocutor = User::select('id', 'name', 'surname')->find($this->private_user);
                $this->chat_room_name = false;

                //Marcamos como leídos los mensajes de este chat privado recibidos por el usuario en session:
                Chat::where('sender', $this->private_user)
                ->where('receiver', Auth::user()->id)
                ->whereNull('read')
                ->whereNull('chat_room_id')
                ->update(['read' => Carbon::now()]);

            //Obtenemos administradores de salas de chat de la empresa en session:
            }else{
                $admins = ChatRoomUsers::select('users.id', 'users.name', 'users.surname')
                ->join('users', 'chat_room_users.user_id', '=', 'users.id')
                ->join('user_companies', 'users.id', '=', 'user_companies.user_id')
                ->where('user_companies.company_id', session('currentCompany'))
                ->where('chat_room_users.role', config('constants.CHAT_ROLE_ADMIN_'))
                ->where('chat_room_users.status', 1)
                ->groupBy('users.id')
                ->orderBy('users.name', 'ASC')
                ->get();

                $this->admins = $admins->count()? $admins:[];
            }
        }

        //Usuarios de la sala:
        $users = ChatRoomUsers::select('chat_room_users.role', 'users.id', 'users.name', 'users.surname', DB::raw('(SELECT image FROM user_images WHERE user_id = users.id AND featured = 1) AS Image'))
        ->join('users', 'chat_room_users.user_id', '=', 'users.id')
        ->where('chat_room_users.chat_room_id', $this->prev_chat_room)
        ->where('chat_room_users.status', 1)
        ->where('users.status', 1)
        ->orderBy('chat_room_users.role', 'DESC')
        ->orderBy('users.name')
        ->get();

        $this->users = $users->count()? $users:[];

        return view('livewire.chat-component');
    }

    /**
     * 2. Seleccionar sala de chat.
     */
    public function selectChat($chat_room){
        $this->chat_room_name = $chat_room['name'];
        $this->chat_room_on = $chat_room['id'];
        $this->form = true;
    }

    /**
     * 3. Guardar nuevo mensaje.
     */
    public function store(){
        // 30/12/21: revisar la validación, no funciona correctamente. Da error
        //$this->validate(/);  
        
        if($this->message != ''){
            $msg = new Chat();
            $msg->sender = Auth::user()->id;
            $msg->message = $this->message;

            if($this->chat_room_on){
                $msg->chat_room_id = $this->chat_room_on;    
            }elseif($this->private_user){
                $msg->receiver = $this->private_user;
            }        
            $msg->save(); 

            //Evento Pusher:
            $sender_ = $this->chat_room_on? false:$msg->sender;
            event(new \App\Events\ChatMessage($this->chat_room_on, $sender_));
        }
    }

    /**
     * 4. Abrir sala privada de chat.
     */
    public function selectUser($user){
        $this->private_user = $user['id'];
        array_push($this->chat_private, $user);
        $this->chat_room_on = false;
    }

    /**
     * 5. Recargar página por Pusher.
     */
    public function reload($data){
        //Verificamos la situación del receptor, si está en chat privado o sala. En caso negativo avisamos de haber recibido un nuevo mensaje.
        if($data['chat_room']){
            if($data['chat_room'] == $this->chat_room_on){
                $this->chat_room_on = $data['chat_room'];
            }else{
                $this->chat_room_on = false;
                $this->emit('new_message', false, $data['chat_room']);
            }    
        }

        if($data['sender']){
            $this->emit('new_message', $data['sender'], false);
        }
    }
}
