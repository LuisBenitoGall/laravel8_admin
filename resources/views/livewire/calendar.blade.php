<div class="position-relative">
    <div class="row" style="row-gap: .5em;">
        @if($dayTypes->count())
            {{-- Tipos de jornada --}}
            <div id="day-types" class="custom-dropdown">
                <div class="cd-head" id="heading">
                    <a data-toggle="collapse" href="#cd-collapse" aria-expanded="false" aria-controls="cd-collapse" class="toggle-arrow collapsed">
                        {{ trans('textos.tipo_jornada_selec') }}
                        <i class="fa fa-caret-down ml-3"></i>
                    </a>
                </div> 

                <div class="cd-body collapse" id="cd-collapse" role="tabpanel" aria-labelledby="heading" data-parent="#day-types-dropdown">
                    <ul>
                        @foreach($dayTypes as $dt)
                            {{-- Para usuario sólo aquellas opciones user_enabled = true --}}
                            <li @if($user && !$dt->user_enabled) class="d-none" @endif>
                                <a href="#" class="option" data-id="{{ $dt->id }}" data-name="{{ $dt->name }}" data-color="{{ $dt->color }}">
                                    <span class="colorEx mr-2" style="background-color: {{ $dt->color }}"></span>
                                    {{ $dt->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div> 

                <div class="cd-foot" @if(!$day_type) style="display: none;" @endif>
                    {{ trans('textos.gestionando') }} <b @if($day_type) style="border-color: {{ $day_type->color }};" @endif>@if($day_type) {{ $day_type->name }} @endif</b> 
                    <a href="#" class="cancel" rel="tooltip" title="{{ trans('textos.gestion_cancelar') }}" wire:click="cancelDayType()">
                        <i class="fa fa-times-circle ml-3 text-danger"></i>
                    </a>
                </div>
            </div>
            {{-- / Tipos de jornada --}}

            @if($user)
                {{-- Notificación fechas solicitadas --}}
                @if($this->day_type)
                    <div class="float-fixed-div" id="notify-days">
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('work-user-calendar-notify.notify-days', $user) }}" class="btn btn-info btn-confirm" data-msg="{{ ucfirst(trans('textos.notificar_fechas_solicitadas_aviso')) }}">
                                    {{ ucfirst(trans('textos.notificar_fechas_solicitadas')) }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Información sobre el usuario --}}
                @foreach($dayTypes as $dt_)
                    @if($dt_->user_enabled)
                        <div class="col-md-2">
                            @php
                            //Configuraciones del tipo de día
                            $config = $dt->dayConfig($dt_->id, $year);

                            $userDays = $dt_->getNumDayTypeByUser($user->id, $dt_->id, $year);

                            //Si el tipo de jornada es acumulativa, buscamos días pendientes del año anterior:
                            $acumulados = $dt->getCumulativeDays($user->id, $year);

                            $max = $config && $config['max']? $config['max']:0;

                            //El número máximo de días se ve alterado por la fecha de inicio del contrato del usuario, esto es, si lleva menos de 1 año le corresponde la parte proporcional.
                            if(isset($year_days)){
                                $max = ($year_days * $max) / 365;
                            }

                            $max = round($max + $acumulados);

                            //Comprobamos si el usuario no tiene una cantidad personalizada de jornadas:
                            $day_type_user_config = $dt_->getDayTypeQtyByUser($user->id, $year);
                            if($day_type_user_config) $max = $day_type_user_config->qty;
                            @endphp

                            <span class="colorEx mr-2" style="background-color: {{ $dt_->color }}"></span>
                            <span class="mr-2">{{ ucfirst($dt_->name) }}</span>

                            <strong>
                                <span class="dtPartial-{{ $dt_->id }} @if(is_array($userDays) && count($userDays) <= $max) text-success @else text-danger @endif">{{ count($userDays) }}</span> 

                                @if($max > 0 || $acumulados > 0)
                                    / <span class="dtTotal">{{ $max }}</span>
                                @endif
                            </strong>

                            {{-- Modificar número de jornadas --}}
                            @can('work-calendar.edit')
                                @if($dt_->modifiable)
                                    <a href="#" class="btn btn-warning btn-circle btn-circle-micro no-spinner ml-2 btnModifiable" rel="tooltip" title="{{ ucfirst(trans('textos.modificar')) }}" data-toggle="modal" data-target="#modal-modify-day-type" data-id="{{ $dt_->id }}" data-daytype="{{ $dt_->name }}" data-qty="{{ $max }}">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                @endif
                            @endcan
                        </div>
                    @endif
                @endforeach    
            @endif
        @endif
    </div>

    <div class="row">
        @php
        $hoy = date('Y-m-d');
        @endphp

        {{-- Loop meses --}}
        @for($i=1; $i<=12; $i++)
            @php
            $mes = ($i<10)? '0'.$i:$i;
            @endphp

            <div class="col-sm-12 col-md-6 mt-4 table-responsive wo-min-height">
                <p class="text-center"><strong>{{ ucfirst(App\Helpers\helpers::yearMonths($mes)) }}</strong></p>

                <table class="calendario" id="mainCalendar_">
                    <thead>
                        <tr>
                            <th style="font-weight: normal;">
                                <span class="d-none d-md-block">{{ trans('textos.semana') }}</span>
                                <span class="d-block d-md-none">{{ trans('textos.semana-abbr') }}</span>
                            </th>
                            @foreach($weekDays as $wd)
                                <th>
                                    <span class="d-none d-md-block">{{ ucfirst($wd) }}</span>
                                    <span class="d-block d-md-none">{{ mb_substr(ucfirst($wd), 0, 2) }}</span>
                                </th>
                            @endforeach     
                        </tr>
                    </thead>

                    @php
                    //Obtenemos día de la semana del primer día del mes:
                    $monthFirstDayWeek = App\Helpers\helpers::getWeekDay(1, $mes, $year);

                    //Obtenemos último día del mes:
                    $monthLastDay = App\Helpers\helpers::getMonthLastDay($mes, $year);

                    //Nº total de celdas del mes:
                    $total_cells = $monthLastDay + $monthFirstDayWeek;

                    $monthDay = 1;
                    $weekDay = 0;

                    //Indica si es día del mes o celda vacía.
                    $isMonthDay = false;  
                    @endphp

                    <tbody>
                        @while($monthDay <= $monthLastDay)  
                            @php
                            //Fecha del día:
                            $date = '';

                            //Indicamos si es celda vacía o corresponde a día del mes:
                            if($weekDay == $monthFirstDayWeek && !$isMonthDay){
                                $isMonthDay = true;
                            }
                            @endphp

                            {{-- Seteando la fecha --}}
                            @if($isMonthDay)
                                @php
                                $day = $monthDay<10? '0'.$monthDay:$monthDay;
                                $date = $year.'-'.$mes.'-'.$day;
                                @endphp
                            @endif

                            {{-- Inicio semana --}}
                            @if($weekDay == 0)
                                @php
                                //Número de semana:
                                $numWeek = date('W', mktime(0,0,0,$i,$monthDay,$year));
                                @endphp

                                <tr>
                                    <td class="nweek">{{ $numWeek }}</td>
                            @endif

                            {{-- CSS styles --}}
                            @php
                            //Fin de semana:
                            $weekend = ($weekDay == 5 || $weekDay == 6)? 'weekend':'no-weekend';
                            //Festivo:
                            $festive = '';

                            //Fecha marcada por la empresa:
                            $selected_date = isset($selectedDates[$date])? true:false;
                            //Fecha marcada por el usuario:
                            $user_date = count($userDates) && isset($userDates[$date])? 1:'0';
                            @endphp

                            <td 
                                class="d-{{ $date }} {{ $weekend }}" 
                                wire:click="selectDay('{{ $date }}', {{ $user_date }})"
                                @if($selected_date) style="background-color: {!! $selectedDates[$date]['color'] !!};" @endif
                            >
                                {{-- Colocando el primer día de mes en la celda correcta --}}
                                @if($isMonthDay)
                                    <span class="month-day">{{ $monthDay }}</span>
                                @endif      

                                {{-- Horas de la jornada --}}
                                @if(isset($selectedDates[$date]) && $selectedDates[$date]['hours'])
                                    <span class="hours">
                                        {!! substr($selectedDates[$date]['hours'],0,-3) !!}h
                                    </span>
                                @endif

                                {{-- Fechas del usuario --}}
                                @if(count($userDates) && isset($userDates[$date]))
                                    <span class="user-date" style="background-color: {{ $userDates[$date]['color'] }};">
                                        {{-- Validación --}}
                                        @if($userDates[$date]['status'])
                                            <i class="fa fa-check" rel="tooltip" title="@if($userDates[$date]['day_type'] && array_key_exists($userDates[$date]['day_type'], $dayTypesArr)) {!! $dayTypesArr[$userDates[$date]['day_type']] !!} @endif {{ trans('textos.validado') }}"></i>
                                        @else
                                            <i class="fa fa-question-circle" rel="tooltip" title="@if($userDates[$date]['day_type']) {!! $dayTypesArr[$userDates[$date]['day_type']] !!} @endif {{ trans('textos.pendiente_validar') }}"></i>
                                        @endif

                                        {{-- Adjuntar documento --}}
                                        @if(!$day_type)
                                            {{-- Existe el documento --}}
                                            @if($userDates[$date]['doc'])
                                                {{-- Ver documento --}}
                                                <a target="_blank" href="{{ asset('img/calendar/'.$userDates[$date]['doc']) }}" class="ml-2">
                                                    <i class="fa fa-file" rel="tooltip" title="{{ trans('textos.documento_ver') }}"></i>
                                                </a>

                                                {{-- Eliminar documento --}}
                                                <a href="{{ route('work-user-calendar.delete-doc', $userDates[$date]['id']) }}" class="ml-2 text-danger btn-confirm" data-msg="{{ trans('textos.documento_eliminar_confirm') }}">
                                                    <i class="fa fa-times-circle" rel="tooltip" title="{{ trans('textos.documento_eliminar') }}"></i>
                                                </a>
                                            @else
                                                {{-- <span wire:click.stop="selectDay('{{ $date }}')"> --}}
                                                    <a href="#" class="ml-2 addDayDoc" data-toggle="modal" data-target="#modal-add-file" data-id="{{ $userDates[$date]['id'] }}">
                                                        <i class="fa fa-folder-plus" rel="tooltip" title="{{ trans('textos.documento_adjuntar') }}"></i>
                                                    </a>
                                                {{-- </span> --}}
                                            @endif
                                        @endif











                                    </span>
                                @endif
                            </td>

                            @php
                            if($isMonthDay){
                                $monthDay++;
                            }
                            $weekDay++;
                            @endphp

                            {{-- Cierre semana --}}
                            @if($weekDay == 7)
                                @php
                                $weekDay = 0;
                                @endphp
                                
                                </tr>
                            @endif                        
                        @endwhile
                    </tbody>
                </table>
            </div>
        @endfor
    </div>

    <script>
    //Sentencia para poder utilizar JQuery con Livewire:
    document.addEventListener('DOMContentLoaded', function(){
        //Seleccionar tipo de jornada:
        $('.option').on('click', function(){
            var id = $(this).data('id');
            var name = $(this).data('name');
            var color = $(this).data('color');
            $('.cd-foot b').css('border-color', color).text(name); 
            $('.cd-foot').slideDown();
            $('.cd-body').collapse('hide');

            Livewire.emit('selectDayType', id);
        });

        //Cancelar gestión de jornada:
        $('.cancel').on('click', function(){
            $('.cd-foot').slideUp();
            $('.cd-foot b').empty();
            $('.cd-body').collapse('show');
        });

        //Adjuntar documento:
        $(document).on('click', '.addDayDoc', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            $('#wucId').val(id);
        });
    });

    window.addEventListener('swal',function(e){
        swal.fire(e.detail);
        // swal.fire({
        //     title: 'prueba',
        //     text: "",
        //     icon: 'warning',
        //     iconColor: color_warning,
        //     showCancelButton: true,
        //     confirmButtonColor: color_primary,
        //     cancelButtonColor: color_danger,
        //     confirmButtonText: txtContinuar,
        //     cancelButtonText: txtCancelar
        // }).then((result) => {
        //     if (result.isConfirmed) {
        //         window.location.href = '';
        //     }
        // });
    });
    </script>
</div>