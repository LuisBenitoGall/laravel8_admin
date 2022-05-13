<div class="row" id="chat">
    @if($chat_rooms->count())

        {{-- Salas --}}
        <div class="col-md-6 col-lg-2 mb-4">
            <h3 class="mb-3">{{ trans('textos.salas') }}</h3>
        
            <ul>
                @foreach($chat_rooms as $cr)
                    <li>
                        <a href="#" class="btnSelectChat @if($chat_room_on == $cr->id) active @endif" wire:click="selectChat({{ $cr }})">
                            {{ $cr->name }}
                        </a>

                        {{-- Aviso nuevos mensajes --}}
                        <div class="sk-spinner-mini sk-spinner-pulse new-messages room-{{ $cr->id }}"></div>
                    </li>
                @endforeach
            </ul>
        </div>
        
        {{-- Usuarios --}}
        <div class="col-md-6 col-lg-3 sinpadding">
            <h3 class="mb-3">{{ ucfirst(trans('textos.usuarios')) }} @if(count($users)) ({{ count($users) }}) @endif</h3>

            @if(count($users))
                <ul class="chat-users-list">
                    @foreach($users as $user)
                        <li @if($interlocutor && $interlocutor->id == $user->id) class="active" @endif>
                            <span class="mr-2">
                                @if($user->Image)
                                    <img alt="{{ $user->name }} {{ $user->surname }}" class="img-circle" src="{{ asset('img/users/'.$user->Image) }}">
                                @else
                                    <img src="{{ Avatar::create($user->name.' '.$user->surname)->toBase64() }}" />
                                @endif
                            </span>

                            @if($user->id != Auth::user()->id)
                                <a href="#" class="btnSelectUser" wire:click="selectUser({{ $user }})" rel="tooltip" title="{{ ucfirst(trans('textos.chat_privado_abrir')) }}">
                                    {{ $user->name }} {{ $user->surname }}
                                </a> 
                            @else
                                <span>{{ $user->name }} {{ $user->surname }}</span>
                            @endif  

                            {{-- Role --}}
                            @if($user->role == config('constants.CHAT_ROLE_ADMIN_'))
                                <i class="fa fa-user-shield float-right" rel="tooltip" title="{{ ucfirst(trans('textos.administrador')) }}"></i> 
                            @endif

                            {{-- Aviso nuevos mensajes --}}
                            <div class="sk-spinner-mini sk-spinner-pulse new-messages interlocutor-{{ $user->id }}"></div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Chat activo --}}
        <div class="col-md-12 col-lg-7">
            <h3 class="mb-3">
                @if($chat_room_name)
                    {{ trans('textos.sala_mensajes') }} <b>{{ $chat_room_name }}</b>
                @elseif($interlocutor)
                    {{ trans('textos.chat_privado_con') }} <b>{{ ucfirst($interlocutor->name) }} {{ ucfirst($interlocutor->surname) }}</b>
                @else
                    &nbsp;
                @endif
            </h3>

            {{-- Formulario mensaje --}}
            @if($form)
                <div wire:ignore.self class="mt-3">
                    <form wire:submit.prevent="store" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                        {{ csrf_field() }}

                        <div class="form-group row">
                            {{-- Mensaje --}}
                            <label for="message" class="col-4 col-sm-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.mensaje_nuevo')) !!}</label>
                            <div class="col-8 col-sm-6 col-xl-8">
                                <textarea class="form-control" wire:model.defer="message" required></textarea>
                            </div>
                        

                            <x-btn-submit name="submit_save" label="{{ trans('textos.enviar') }}" inline="true"/>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Mensajes --}}
            <div class="chat-messages">
                {{-- Chat en sala --}}
                @if($chat_room_on || $interlocutor)
                    @if(count($messages))
                        @foreach($messages as $msg)
                            <p>
                                <small class="author"># 
                                    {{-- Autor --}}
                                    @if($msg->sender == Auth::user()->id)
                                        <b>{{ trans('textos.tu') }}</b>,
                                    @else
                                        {{ $msg->name }} {{ $msg->surname }},
                                    @endif
                                    {{-- Fecha y hora --}}
                                    {{ $msg->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}:
                                </small>
                                <i>{{ $msg->message }}</i>
                            </p>
                        @endforeach 

                    {{-- Sin mensajes --}}
                    @else
                        <h2 class="mb-5 mt-5 text-center text-warning">
                            {!! trans('textos.chat_sin_mensajes') !!}
                        </h2>
                    @endif
                
                @else
                    {{-- Chat privado --}}
                    @if($interlocutor)
                        <h2 class="mb-5 mt-5 text-center text-warning">
                            <i class="fa fa-comments"></i>
                            <br>
                            {!! trans('textos.chat_empieza_con') !!} <b>{{ ucfirst($interlocutor->name) }} {{ ucfirst($interlocutor->surname) }}</b>
                        </h2>

                    {{-- Sin sala seleccionada ni chat privado --}}
                    @else
                        <h2 class="mb-5 mt-5 text-center text-warning">
                            <i class="fa fa-comments"></i>
                            <br>
                            {!! trans('textos.chat_sala_selecciona_inicio') !!}
                        </h2>
                    @endif
                @endif   
            </div>
        </div>

    {{-- Usuario no vinculado a ninguna sala --}}
    @else
        <div class="col-12">
            <h2 class="mb-5 mt-5 text-center text-warning">
                {!! trans('textos.usuario_sin_salas_chat') !!}
            </h2>

            @if(count($admins))
                <p class="text-center">{!! trans('textos.solicita_accesos_admin') !!}</p>

                <form method="post" autocomplete="off" action="{{ route('chat-rooms.get-access') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}

                    <div class="row form-group justify-content-center mb-5">
                        {{-- Selector administrador --}}
                        <div class="col-sm-6 col-lg-4">
                            <select name="admin_id" class="form-control select2" required>
                                <option value="" selected disabled>{{ trans('textos.administrador_selec') }}</option>
                                @foreach($admins as $adm)
                                    <option value="{{ $adm->id }}">{{ $adm->name }} {{ $adm->surname }}</option>
                                @endforeach
                            </select> 

                            @if($errors->has('admin_id'))
                                <span class="invalid-feedback" role="alert">
                                  <strong>{{ $errors->first('admin_id') }}</strong>
                                </span>
                            @endif
                        </div>  

                        <div class="col-sm-2 text-right">
                            <input type="submit" name="submit_get_access" class="btn btn-info" value="{{ trans('textos.solicitar_acceso') }}">
                        </div>   
                    </div>
                </form>
            @endif
        </div>
    @endif

    <script>
    //Sentencia para poder utilizar JQuery con Livewire:
    document.addEventListener('DOMContentLoaded', function(){
        //Seleccionar chat:
        $('.btnSelectChat').on('click', function(e){
            e.preventDefault();
            $('.btnSelectChat').removeClass('active');
            $(this).addClass('active');
        });

        //Pusher script
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('7b913367afbfdcd7c0fe', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('chat-channel');
        channel.bind('chat-event', function(data){
            //alert(JSON.stringify(data));
            window.livewire.emit('reload', data);
        });

        //Aviso de nuevos mensajes:
        window.livewire.on('new_message', (sender, chat_room) =>{
            if(chat_room){
                $('.new-messages.room-'+chat_room).fadeIn();
            }

            if(sender && sender != {{ Auth::user()->id }}){
                $('.new-messages.interlocutor-'+sender).fadeIn();
            }
        });
    });
    </script>
</div>



