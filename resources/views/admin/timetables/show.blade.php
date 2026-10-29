@extends('admin.partials.layout')

@section('content')

@php
// Detectar IP:
$ip = request()->ip();

// Agent:
use Jenssegers\Agent\Agent;
$agent = new Agent();

//Obtenemos conceptos horarios de la empresa a la que pertenece el usuario:
$user_workplace = Auth::user()->workplace;
if($user_workplace && $user_workplace->workplace_id){
    $timetable_items = App\Models\TimetableItems::itemsByWorkplace($user_workplace->workplace_id);
}else{
    $timetable_items = App\Models\TimetableItems::where('company_id', session('currentCompany'))->where('status', 1)->get();    
}   
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
        $own_output = App\Models\TimetableItems::where('own_output', $lastHour->item_id)->where('status', 1)->first();
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
            @if($lastHour && $lastHour->item_id == $ti->id && ($ti->max > 0 || $ti->min > 0) )
                @php
                //Convertimos la última hora a minutos y le sumamos el tiempo máximo o mínimo - 1 minuto.
                $minutes = App\Helpers\helpers::getMinutes($lastHour->hour);
                //16/07/21: se pone un fijo de 5 minutos para el tiempo máximo.
                //01/11/22: se toma como tiempo el máximo o mínimo del item - 1 minuto.
                $max_time = $ti->max > 0? $ti->max-5:$ti->min-5;
                //$max_time = 5;
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

    <div class="row mb-3">
    	<div class="col-12">
    		<div class="card mb-3">
            	<div class="card-body">
				    <ul class="nav navbar-top-links navbar-right d-none d-md-block justify-content-start">
				    	

					    {{-- Entrada y salida básicas --}}
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

					            {{-- Conceptos horarios --}}
					            @if($timetable_items->count())
					                <ul class="dropdown-menu dropdown-alerts" @if(!$agent->isDesktop() && !in_array($ip, $setted_ips)) style="right: -150px;" @endif>
					                    <li class="dm-header">
					                        {!! ucfirst(trans('textos.conceptos_horarios')) !!}
					                    </li>

					                    @php
					                    $seeConcept = '';
					                    @endphp

					                    @if(count($timetable_items))
					                        @foreach($timetable_items as $ti)
					                            {{-- Sólo items pertenecientes al flag activo --}}
					                            @if($ti->type == $flag)
					                                {{-- Omitimos los conceptos básicos para móvil si se han bloqueado por configuración --}}
					                                {{-- && is_array($setted_ips) && in_array($ip, $setted_ips) --}}
					                                @if($ti->basic_item == 1 && !$agent->isDesktop() && !config('constants.BASIC_TIMETABLE_ITEMS_'))
					                                    @php
					                                    $seeConcept = 'oculto';
					                                    @endphp
					                                @endif

					                                {{-- Omitimos los conceptos básicos si el usuario se encuentra fuera de las ip's válidas --}}
					                                @if($ti->basic_item == 1 && $agent->isDesktop() && !in_array($ip, $setted_ips))
					                                    @php
					                                    $seeConcept = 'oculto';
					                                    @endphp
					                                @endif

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
					                                        {{ ucfirst($ti->name) }}
					                                    </a>
					                                </li>

					                                @php
					                                //Variable para controlar si mostrar las opciones adicionales:
					                                if($seeConcept == '') $with_aditionals_items = true;

					                                $seeConcept = '';
					                                @endphp
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
					</ul>
				</div>
			</div>
		</div>
	</div>

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

@endsection