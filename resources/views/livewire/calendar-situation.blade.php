<div class="position-relative">
    <div class="row">
        @php
        $hoy = date('Y-m-d');
        @endphp

        {{-- Loop meses --}}
        @for($i=1; $i<=12; $i++)
            @php
            $mes = ($i<10)? '0'.$i:$i;
            @endphp

            <div class="col-sm-12 col-md-6 mt-4">
                <p class="text-center">
                    <strong>{{ ucfirst(App\Helpers\helpers::yearMonths($mes)) }}</strong>
                    {{-- Seleccionar mes --}}
                    <a href="#" class="btn btn-info btn-xs ml-2 btnSelectMonth" wire:click="selectMonth('{{ $mes }}')" rel="tooltip" title="{{ trans('textos.calendario_mes') }}" data-toggle="modal" data-target="#modal-show-employees-month">
                        <i class="fa fa-calendar"></i>
                    </a>
                </p>

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
                                class="d-{{ $date }} {{ $weekend }} text-center" 
                                data-toggle="modal" 
                                data-target="#modal-show-employees"
                                wire:click="selectDay('{{ $date }}')"
                                @if($selected_date) style="background-color: {!! $selectedDates[$date]['color'] !!};" @endif
                            >
                                {{-- Colocando el primer día de mes en la celda correcta --}}
                                @if($isMonthDay)
                                    <span class="month-day">{{ $monthDay }}</span>
                                @endif

                                {{-- Info de la fecha --}}
                                @if(in_array($date, $workUserCalendar))
                                    <span class="btnCompanyCalendar">
                                        <i class="fa fa-info-circle text-warning"></i>
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

    {{-- Modal registros por fecha --}}
    <div wire:ignore.self class="modal fade modal-show-employees" id="modal-show-employees" tabindex="-1" role="dialog" aria-labelledby="employeesDate" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="employeesDate">{!! ucfirst(trans('textos.calendario')) !!} {{ $fecha }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if(count($dayRegisters))
                        <table class="modal-table" id="tblCompanyCalendar"> 
                            <thead>
                                <th>{{ ucfirst(trans('textos.empleado')) }}</th>    
                                <th>{{ ucfirst(trans('textos.jornada')) }}</th>
                            </thead>

                            <tbody>
                                @foreach($dayRegisters as $dr)
                                    <tr>
                                        <td>{{ $dr->name }} {{ $dr->surname }}</td>
                                        <td>
                                            <span class="colorEx mr-2" style="background-color: {{ $dr->color }}"></span>
                                            {{ $dr->dayType }}
                                        </td>
                                    </tr>
                                @endforeach        
                            </tbody>
                        </table>

                    @else
                        <h2 class="text-warning text-center">{{ trans('textos.fecha_sin_registros') }}</h2>
                    @endif        
                </div>
            </div>
        </div>
    </div>  

    {{-- Modal registros por mes --}}
    <div wire:ignore.self class="modal fade modal-show-employees-month" id="modal-show-employees-month" tabindex="-1" role="dialog" aria-labelledby="employeesMonth" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-xxl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="employeesMonth">{!! ucfirst(trans('textos.calendario')) !!} {{ $mes_ }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if(count($monthRegisters))
                        {{-- Tipos de jornada --}}
                        <div class="row mb-4">
                            @foreach($dayTypes as $dt)
                                <div class="col-md-2 col-xl-1">
                                    <span class="colorEx mini mr-1" style="background-color: {{ $dt->color }}; margin-top: 6px;"></span> {{ $dt->name}}    
                                </div>
                            @endforeach
                        </div>

                        <table class="calendario calendario-modal">
                            <thead>
                                <tr>
                                    @foreach($weekDays as $wd)
                                        <th>
                                            {{ ucfirst($wd) }} 
                                        </th>
                                    @endforeach     
                                </tr>
                            </thead>  

                            @php
                            //Obtenemos día de la semana del primer día del mes:
                            $monthFirstDayWeek = App\Helpers\helpers::getWeekDay(1, $mes_num, $year);

                            //Obtenemos último día del mes:
                            $monthLastDay = App\Helpers\helpers::getMonthLastDay($mes_num, $year);

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
                                        $date = $year.'-'.$mes_num.'-'.$day;
                                        @endphp
                                    @endif

                                    {{-- Inicio semana --}}
                                    @if($weekDay == 0)
                                        <tr>
                                    @endif

                                    {{-- CSS styles --}}
                                    @php
                                    //Fin de semana:
                                    $weekend = ($weekDay == 5 || $weekDay == 6)? 'weekend':'no-weekend';
                                    //Festivo:
                                    $festive = '';
                                    @endphp

                                    <td 
                                        class="d-{{ $date }} {{ $weekend }}" 
                                        @if($selected_date && isset($selectedDates[$date])) style="background-color: {!! $selectedDates[$date]['color'] !!};" @endif
                                    >
                                        {{-- Colocando el primer día de mes en la celda correcta --}}
                                        @if($isMonthDay)
                                            <span class="month-day">{{ $monthDay }}</span>
                                        @endif

                                        @foreach($monthRegisters as $mr)
                                            @if($mr->date == $date)
                                                <p rel="tooltip" title="{{ $mr->dayType }}">
                                                    <span class="colorEx mini mr-1" style="background-color: {{ $mr->color }}"></span>
                                                    {{ $mr->name }} {{ $mr->surname }}
                                                </p>    
                                            @endif
                                        @endforeach
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
                    @else
                        <h2 class="text-warning text-center">{{ trans('textos.fecha_sin_registros') }}</h2>
                    @endif 
                </div>       
            </div>
        </div>
    </div>  

    @once
    @push('scripts')
    <script>
    document.addEventListener('livewire:load', function(){
        //Vaciar modal por mes:
        $('.btnSelectMonth').on('click', function(){
            $('#modal-show-employees-month .modal-body').empty();
        });

    });
    </script>
    @endpush
    @endonce
</div>

