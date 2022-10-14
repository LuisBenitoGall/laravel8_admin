@php
    // Detectar IP:
    $ip = request()->ip();

    // Agent:
    use Jenssegers\Agent\Agent;
    $agent = new Agent();

    // Control horario:
    $controller = new App\Http\Controllers\Controller;
    $setTimetable = $controller->settedModule('timetables');

    // Idiomas:
    $languages = App\Helpers\Helpers::languages();

    //Configuraciones 1ª vez:
    $initial_imports = App\Models\AppSettings::select('id', 'value')->where('field', 'initial_imports')->first();
    $imports_done = false;
    if(!$initial_imports->value){
        $imports = new App\Http\Controllers\Controller;
        $imports_done = $imports->initialImports();

        if($imports_done){
            $initial_imports->value = true;
            $initial_imports->save();
        }
    }
@endphp

<div id="page-wrapper" class="gray-bg dashbard-1">
    {{-- Top Menu --}}
    <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation">
            <div class="navbar-header">
                {{-- Minimizar menú lateral --}}
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary no-spinner " href="#">
                    <i class="fa fa-bars"></i>
                </a>

                {{-- Buscador --}}
                {{-- <form role="search" class="navbar-form-custom" action="">
                    <div class="form-group">
                        <input type="text" placeholder="{{ ucfirst(trans('textos.buscar')) }}..." class="form-control" name="top-search" id="top-search">
                    </div>
                </form> --}}
            </div>

            {{-- Botón menú top responsive --}}
            <a class="btn-top-links minimalize-styl-2 btn btn-warning no-spinner" href="#">
                <i class="fa fa-bars"></i>
            </a>

            <ul class="nav navbar-top-links navbar-right">
                {{-- Control horario --}}
                @if($setTimetable)
                    @php
                        //Obtenemos conceptos horarios de la empresa.
                        $timetable_items = App\Models\TimetableItems::where('company_id', session('currentCompany'))->get();
                    @endphp

                    {{-- Condiciones:
                        1. Deben existir conceptos horarios.
                        2. El usuario debe tener activo un empleo.
                    --}}
                    @if($timetable_items->count() && session('userEmployee'))
                        @php
                            //Separamos los conceptos básicos de Entrada y Salida:
                            $entradaId = false;
                            $salidaId = false;

                            //Para mostrar los items adicionales:
                            $aditional_items_in = false;
                            $aditional_items_out = false;

                            //Conceptos de control horario con limitación de veces:
                            $nVeces = App\Models\Timetable::select('timetable_items.id', 'timetable_items.qty')
                            ->join('timetable_items', 'timetables.item_id', '=', 'timetable_items.id')
                            ->where('timetables.user_id', Auth::user()->id)
                            ->where('timetable_items.qty', '>', 0)
                            ->whereDate('timetables.date', Carbon\Carbon::today())
                            ->get();

                            //Obtenemos último registro horario del usuario, si es Entrada o Salida
                            $lastHour = App\Models\Timetable::select('hour', 'flag', 'item_id')
                            ->where('user_id', Auth::user()->id)
                            ->orderBy('date', 'DESC')
                            ->orderBy('hour', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->first();

                            //Si el registro es de entrada comprobamos si tiene concepto de salida propio. En caso verdadero será esta la única opción de salida que se le muestre al usuario.
                            $own_output = false;
                            if($lastHour && $lastHour->flag == 1){
                                $own_output = App\Models\TimetableItems::where('own_output', $lastHour->item_id)->first();
                            }

                            //Opciones de entrada bloqueadas para conceptos con un tiempo máximo o duración mínima:
                            $blocked = false;
                            $with_aditionals_items = false;
                        @endphp

                        @if($timetable_items->count() && !$own_output)
                            @foreach($timetable_items as $ti)
                                {{-- Entrada simple --}}
                                @if($ti->basic_item && $ti->type == 1)
                                    @php
                                        $entradaId = $ti->id;
                                    @endphp
                                @endif

                                {{-- Salida simple --}}
                                @if($ti->basic_item && $ti->type == 2)
                                    @php
                                        $salidaId = $ti->id;
                                    @endphp
                                @endif

                                {{-- Otros tipos de entrada --}}
                                @if(!$ti->basic_item && $ti->type == 1)
                                    @php
                                        $aditional_items_in = true;
                                    @endphp
                                @endif

                                {{-- Otros tipos de salida --}}
                                @if(!$ti->basic_item && $ti->type == 2)
                                    @php
                                        $aditional_items_out = true;
                                    @endphp
                                @endif

                                {{-- Bloqueo de conceptos 1 --}}
                                @if($lastHour && $lastHour->item_id == $ti->id && $ti->max > 0)
                                    @php
                                        //Convertimos la última hora a minutos y le sumamos el tiempo máximo.
                                        $minutes = App\Helpers\helpers::getMinutes($lastHour->hour);
                                        //16/07/21: se pone un fijo de 5 minutos para el tiempo máximo:
                                        $max_time = 5;
                                        $total_minutes = $minutes + $max_time; //$ti->max;

                                        //Hora de desbloqueo:
                                        $unlock_hour = App\Helpers\helpers::getTimeFromMinutes($total_minutes);

                                        if(date('Y-m-d').' '.$unlock_hour > date('Y-m-d H:i')){
                                            $blocked = $unlock_hour;
                                        }
                                    @endphp
                                @endif

                                {{-- Bloqueo de conceptos 2
                                    Si la hora del último registro es superior a la hora actual:
                                --}}
                                {{-- 16/08/22 revisar esta condición, no acabo de entenderla --}}
                                {{-- @if($lastHour && $lastHour->hour >= date('H:i:s', time()))
                                    @php
                                    $blocked = $lastHour->hour;
                                    @endphp
                                @endif --}}
                            @endforeach
                        @endif

                        {{-- Concepto de entrada --}}
                        @if(!$lastHour || $lastHour->flag == 2)
                            @php
                                $tipoCH = $entradaId;
                                $actionCH = 'entrada';
                                $flag = 1;
                            @endphp

                            {{-- Concepto de salida --}}
                        @else
                            @php
                                $tipoCH = !$own_output? $salidaId:$own_output->id;
                                $actionCH = 'salida';
                                $flag = 2;
                            @endphp
                        @endif

                        {{-- IP´s de la empresa --}}
                        @php
                            $setted_ips = session('companySettings') && session('companySettings')->ip? unserialize(session('companySettings')->ip):false;
                        @endphp

                        {{-- Comentar la línea siguiente para habilitar deteccíon de IP --}}
                        {{-- @if($agent->isDesktop()) --}}

                        {{-- Descomentar la línea siguiente para habilitar la detección de IP --}}
                        @if($agent->isDesktop() && is_array($setted_ips) && in_array($ip, $setted_ips))
                            <li class="HourlyControl basicHourlyControl list-inline-item notif display-none"
                                id="HCdesktop">
                                <a href="{{ route('timetable.store', $tipoCH) }}"
                                   class="arrow-none btnHourlyControl {{ $actionCH }} one-click" role="button">
                                    @if($actionCH == 'entrada')
                                        {{ ucfirst(trans('textos.entrar')) }}
                                    @else
                                        {{-- Concepto con salida propia --}}
                                        @if($own_output)
                                            {{ ucfirst($own_output->name) }}
                                        @else
                                            {{ ucfirst(trans('textos.salir')) }}
                                        @endif
                                    @endif

                                    {{-- Reloj --}}
                                    <span id="currentHour"></span>
                                </a>

                                {{-- Bloqueo del selector --}}
                                <span class="blocked-enter" @if($blocked) style="display: block;" rel="tooltip"
                                      title="{{ trans('textos.bloqueado_hasta') }} {{ $blocked }}h." @endif>
                                    <i class="fa fa-ban"></i>
                                </span>
                            </li>
                        @endif

                        {{-- Conceptos adicionales. Condiciones:
                            1. Deben existir conceptos adicionales de entrada o salida:
                            2. El último registro de entrada no debe tener concepto de salida propio.
                            3. No debe existir ningún bloqueo horario ($blocked)
                        --}}
                        @if( ($aditional_items_in && $flag == 1) || ($aditional_items_out && $flag == 2) )
                            <li class="HourlyControl subItems_ list-inline-item dropdown notif"
                                @if($blocked) style="display: none" @endif>
                                <a href="#" class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown"
                                   role="button" aria-haspopup="false" aria-expanded="false">
                                    <span class="btnHourlyControl {{ $actionCH }}">
                                        <i class="fa fa-fw fa-caret-down"></i>
                                    </span>
                                </a>

                                @if($timetable_items->count())
                                    <ul class="dropdown-menu dropdown-alerts">
                                        <li class="dm-header">
                                            {!! ucfirst(trans('textos.conceptos_horarios')) !!}
                                        </li>

                                        @php
                                            $seeConcept = '';
                                        @endphp

                                        @if(count($timetable_items))
                                            @foreach($timetable_items as $ti)
                                                {{-- Omitimos los conceptos básicos de Entrada y Salida --}}
                                                @if(!$ti->basic_item)
                                                    @if($ti->type == $flag)
                                                        {{-- Omitimos los conceptos que hayan alcanzado el número de veces diario: --}}
                                                        @if(isset($ti->qty))
                                                            @foreach($nVeces as $vez)
                                                                @php
                                                                    if($ti->id == $vez->id && $ti->qty >= $vez->qty){
                                                                        $seeConcept = 'oculto';
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                        @endif

                                                        {{-- Omitimos los conceptos si hay bloqueo hasta determinada hora --}}
                                                        @if($blocked && $flag == 1)
                                                            @php
                                                                $seeConcept = 'oculto';
                                                            @endphp
                                                        @endif

                                                        {{-- Omitimos los conceptos de salida que son propias de un concepto de entrada --}}
                                                        @if($ti->type == 2 && $ti->own_output > 0)
                                                            @php
                                                                $seeConcept = 'oculto';
                                                            @endphp
                                                        @endif

                                                        <li class="{{ $seeConcept }}">
                                                            <a href="{{ route('timetable.store', $ti->id) }}"
                                                               class="one-click">
                                                                <i class="fa fa-{{ $ti->icon }}"></i>
                                                                {{ $ti->name }}
                                                            </a>
                                                        </li>

                                                        @php
                                                            //Variable para controlar si mostrar las opciones adicionales:
                                                            if($seeConcept == '') $with_aditionals_items = true;

                                                            $seeConcept = '';
                                                        @endphp
                                                    @endif

                                                    {{-- Habilitamos los conceptos básicos de Entrada y Salida dentro de la red de ADN por móvil --}}

                                                    {{-- Descomentar el siguiente código para habilitar los conceptos Entrada y Salida para Móvil --}}
                                                @elseif($agent->isMobile() && is_array($setted_ips) && in_array($ip, $setted_ips))
                                                    @if($ti->type == $flag)
                                                        @if(isset($ti->qty))
                                                            @foreach($nVeces as $vez)
                                                                @php
                                                                    if($ti->id == $vez->id && $ti->qty >= $vez->qty){
                                                                        $seeConcept = 'oculto';
                                                                    }
                                                                @endphp
                                                            @endforeach
                                                        @endif

                                                        {{-- Omitimos los conceptos si hay bloqueo hasta determinada hora --}}
                                                        @if($blocked && $flag == 1)
                                                            @php
                                                                $seeConcept = 'oculto';
                                                            @endphp
                                                        @endif

                                                        <li class="{{ $seeConcept }}">
                                                            <a href="{{ route('timetable.store', $ti->id) }}">
                                                                <i class="fa fa-{{ $ti->icon }}"></i>
                                                                {{ $ti->name }}
                                                            </a>
                                                        </li>

                                                        @php
                                                            $seeConcept = '';
                                                        @endphp
                                                    @endif
                                                @endif
                                            @endforeach
                                        @else
                                            {{ trans('textos.horarios_conceptos_sin') }}
                                        @endif

                                        {{-- Aviso de bloqueo de entrada --}}
                                        @if($blocked && $flag == 1)
                                            <li style="padding: 4px 10px;" class="blocked-hour">
                                                {{ trans('textos.entrada_bloqueada_hasta') }} {{ $blocked }}h.
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </li>
                        @endif

                        {{-- Control mediante timeout .js --}}
                        <script>
                            var blocked_timetables = '{{ $blocked }}';

                            //Mostrar selector de opciones adicionales:
                            var with_aditionals_items = '{{ $with_aditionals_items }}';
                            if (!with_aditionals_items) {
                                $('.subItems_').hide();
                            }
                        </script>
                    @endif
                @endif

                {{-- Selector idioma --}}
                @if(count($languages) > 1)
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);" role="button"
                           rel="tooltip" title="{{ ucfirst(trans('textos.idiomas')) }}">
                            <i class="fa fa-globe"></i>
                            <span
                                class="current-idm">{{ (session()->get('locale') != NULL)? session()->get('locale'):App::getLocale() }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-alerts">
                            <li class="dm-header">
                                {!! ucfirst(trans('textos.lenguajes')) !!}
                            </li>

                            @php
                                $divider = 0;
                            @endphp

                            @foreach($languages as $lang)
                                @if($lang[0] != App::getLocale() && $lang[5])
                                    @if($divider > 0)
                                        <li class="dropdown-divider"></li>
                                    @endif
                                    <li>
                                        <a href="{!! route('locale', $lang[0]) !!}">
                                            {!! $lang[3] !!}
                                        </a>
                                    </li>

                                    @php
                                        $divider++;
                                    @endphp
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif

                {{-- Notificaciones --}}
                <li class="dropdown position-relative">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="javascript:void(0);"
                       role="button" rel="tooltip" title="{{ ucfirst(trans('textos.notificaciones')) }}">
                        <i class="fa fa-bell"></i>
                        {{--   <span class="label label-danger" id="notificationsQty"
                               @if(Auth::user()->unreadNotifications()->count() == 0) style="display:none;" @endif>
                               {{ Auth::user()->unreadNotifications()->count() }}
                           </span>--}}
                    </a>

                    <ul class="dropdown-menu dropdown-alerts">
                        <li class="dm-header">
                            {!! ucfirst(trans('textos.notificaciones')) !!}
                        </li>

                        {{--@php
                        //Mostramos un máximo de 5
                        $i = 1;
                        @endphp

                        @forelse(auth()->user()->unreadNotifications as $notification)
                            @if($i <= 5)
                                <li class="notification_">
                                    <a href="{!! route('notifications.show', $notification->id) !!}">
                                        <i class="fa fa-envelope"></i>
                                        {!! $notification->data['title'] !!}

                                        <small class="text-warning pull-right">{!! $notification->created_at->diffForHumans() !!}</small>
                                    </a>
                                </li>
                            @endif

                            @if($i == 5 && count(auth()->user()->unreadNotifications) > 5)
                                <li class="notification_">
                                    <a href="{!! route('notifications.index') !!}">
                                        <i class="fa fa-envelope"></i>
                                        ...
                                    </a>
                            @endif

                            @php
                            $i++;
                            @endphp

                        @empty
                            <li class="notify-item" id="withoutNotifications">
                                <span class="special-span">{{ trans('textos.notificaciones_no_nuevas') }}</span>

                                <a href="{{ route('notifications-reload') }}" rel="tooltip" title="{{ trans('textos.notificaciones_buscar') }}" class="co-link pull-right;">
                                    <i class="fa fa-sync"></i>
                                </a>
                            </li>
                        @endforelse--}}

                        <li class="notify-all">
                            <a href="{!! route('notifications.index') !!}">
                                {{ ucfirst(trans('textos.ver_todo')) }}
                            </a>
                        </li>
                        <li class="notify-all">
                            <a href="{{ route('notifications-read') }}">
                                {{ ucfirst(trans('textos.notificaciones_marcar')) }}
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Nueva notificación --}}
                <li>
                    <a href="{{ route('messages.create') }}" role="button" rel="tooltip"
                       title="{{ ucfirst(trans('textos.notificacion_nueva')) }}">
                        <i class="fa fa-paper-plane"></i>
                    </a>
                </li>

                {{-- Notas --}}
                <li class="dropdown">
                    <a href="#" role="button" class="count-info" rel="tooltip"
                       title="{{ ucfirst(trans('textos.notas')) }}" data-toggle="modal" data-target="#modal-add-note">
                        <i class="fa fa-sticky-note"></i>
                        <span class="label label-warning" id="notesQty">
                            {{ optional(Auth::user())->notes()  ?? 0}}
                        </span>
                    </a>
                </li>

                {{-- Chat --}}
                <li class="dropdown">
                    <a href="{{ route('chat.index') }}" role="button" class="count-info" rel="tooltip"
                       title="{{ ucfirst(trans('textos.chat')) }}">
                        <i class="fa fa-comments"></i>
                        <span class="label label-success" id="chat-messages-num">

                        </span>
                    </a>
                </li>

                {{-- Preferencias --}}
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                       aria-expanded="false" rel="tooltip" title="{{ ucfirst(trans('textos.opciones_preferidas')) }}">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-alerts">
                        <li class="dm-header">
                            {!! trans('textos.opciones_preferidas') !!}
                        </li>

                        <li class="p-3">
                            <form method="post" action="#" id="frm_preference" role="form">
                                {{ csrf_field() }}
                                <input type="hidden" name="url" value="{!! url()->full() !!}">

                                <input type="text" name="name" id="preferenceName" class="form-control">
                                <small class="text-warning">
                                    {!! trans('textos.guarda_pantalla') !!}
                                </small>
                            </form>
                        </li>
                    </ul>
                </li>

                {{-- Selector de empresa: desde algunas páginas no se puede cambiar de empresa --}}
                @if(session('companies'))
                    <li class="list-inline-item" style="min-width: 200px;">

                        @if(count(session('companies')) > 1 && !isset($noSelectCompany))
                            <select class="form-control select-company select2">
                                @if(!session('currentCompany'))
                                    <option value="" selected>{!! trans('textos.empresa_selec') !!}</option>
                                @endif

                                @foreach(session('companies') as $c)
                                    @php
                                        $selected = $c->id == session('currentCompany')? 'selected':'';
                                    @endphp

                                    <option value="{{ $c->id }}" {{ $selected }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        @else
                            @foreach(session('companies') as $c)
                                @if($c->id == session('currentCompany'))
                                    <h5 class="companyName">{{ $c->name }}</h5>
                                @endif
                            @endforeach
                        @endif
                    </li>
                @else

                @endif

                {{-- Administrador: --}}
                <li class="dropdown">
                    @php
                    $avatar= null;
                    if(!Auth::user()->images->isEmpty()){
                        $avatar = Auth::user()->images()->first();
                    }
                    @endphp

                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                       aria-expanded="false">
                        <img class="rounded-circle avatar"
                             src=" {{ !is_null($avatar)  ? asset('img/users/'.$avatar->image) : asset('img/users/user.png') }}"
                             alt="{{ Auth::user()->name }}"/>
                    </a>

                    <ul class="dropdown-menu dropdown-alerts" style="margin-top: -4px;">
                        <li class="dm-header">
                            {!! trans('textos.hola') !!}, {{ Auth::user()->name }}
                        </li>

                        {{-- User profile --}}
                        <li>
                            <a href="{{ route('users.edit', Auth::user()->id) }}" class="dropdown-item notify-item">
                                <i class="fa fa-user"></i> <span>{!! ucfirst(trans('textos.perfil')) !!}</span>
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>

                        {{-- Logout --}}
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="frmLogout">
                                @csrf
                                <i class="fa fa-power-off"></i>
                                <input type="submit" value="Logout">
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>

    {{-- Page Heading --}}
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-9">
            @if(isset($header))
                <h2>
                    {!! ucfirst(trans('textos.'.$header['option'])) !!}
                </h2>

                @if(isset($header['suboption']))
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="">{!! ucfirst(trans($header['suboption'])) !!}</a>
                        </li>
                    </ol>
                @endif
            @endif
        </div>
    </div>

    {{-- Page Content --}}
    <div class="wrapper wrapper-content" id="mainContent">
        {{-- Warnings: --}}
        @if(session('msg'))
            <div class="alert alert-success" role="alert">
                {!! session('msg') !!}
            </div>
        @endif
        @if(session('alert'))
            <div class="alert alert-danger" role="alert">
                {!! session('alert') !!}
            </div>
@endif

