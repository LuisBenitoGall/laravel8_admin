<div class="row position-relative">
    {{-- Tipos de jornada --}}
    @if($dayTypes->count())
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
                        <li>
                            <a href="#" class="option" data-id="{{ $dt->id }}"  data-name="{{ $dt->name }}" data-color="{{ $dt->color }}">
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
    @endif
    {{-- / Tipos de jornada --}}

    @php
    $hoy = date('Y-m-d');
    @endphp

    {{-- Loop meses --}}
    @for($i=1; $i<=12; $i++)
        @php
        $mes = ($i<10)? '0'.$i:$i;
        @endphp

        <div class="col-sm-12 col-md-6 mt-4">
            <p class="text-center"><strong>{{ ucfirst(App\Helpers\helpers::yearMonths($mes)) }}</strong></p>

            <table class="calendario" id="mainCalendar_">
                <thead>
                    <tr>
                        <th style="font-weight: normal;">{{ trans('textos.semana') }}</th>
                        @foreach($weekDays as $wd)
                            <th>
                                {{ ucfirst($wd) }} 
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

                        //Fecha marcada:
                        $selected_date = isset($selectedDates[$date])? true:false;
                        @endphp

                        <td 
                            class="d-{{ $date }} {{ $weekend }}" 
                            wire:click="selectDay('{{ $date }}')"
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

        //Cancelar gestión de jornada.
        $('.cancel').on('click', function(){
            $('.cd-foot').slideUp();
            $('.cd-foot b').empty();
            $('.cd-body').collapse('show');
        });
    });
    </script>
</div>