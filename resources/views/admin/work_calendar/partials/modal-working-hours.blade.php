<div class="modal fade modal-working-hours" id="modal-working-hours" tabindex="-1" role="dialog" aria-labelledby="addWorkingHours" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addWorkingHours">{!! ucfirst(trans('textos.horarios_laborables')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{!! trans('textos.horarios_laborables_texto') !!}</p>

                <form method="post" autocomplete="off" action="{{ route('work-calendar.store-intervals') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="workplace_id" value="@if($workplace) {{ $workplace->id }} @endif">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <div class="form-group row">
                        {{-- Fecha desde --}}
                        <label for="from" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.desde')) !!}*</label>
                        <div class="col-lg-3">
                            <x-date-picker name="from" class="text-right" id="from" :format="$languages[config('app.locale')][4]" required/>
                        </div>

                        {{-- Fecha hasta --}}
                        <label for="to" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
                        <div class="col-lg-3">
                            <x-date-picker name="to" class="text-right" id="to" :format="$languages[config('app.locale')][4]"/>
                        </div>

                        {{-- Horas --}}
                        <label for="hours" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.horas')) !!}*</label>
                        <div class="col-lg-2">
                            <x-input type="text" name="hours" class="text-right maskHours" id="hours" placeholder="hh:mm" autocomplete="off" maxlength="5" required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        {{-- Por días --}}
                        <label for="weekdays" class="col-12">{!! ucfirst(trans('textos.por_dias')) !!}</label>
                        <div class="col-12">
                            @php
                            $k = 1;
                            @endphp

                            @foreach($weekDays as $idx => $day)
                                <label class="custom-chk-rdb mr-3" rel="tooltip" style="margin-bottom: 0; padding-top: 3px;">
                                    <input type="checkbox" name="weekdays[]" value="{{ $k }}">
                                    <span class="chk" style="top: 5px;"></span>
                                    <span class="text-warning" style="line-height: 30px;">{{ ucfirst($day) }}</span>
                                </label>

                                @php
                                $k++;
                                @endphp
                            @endforeach
                        </div>
                    </div>

                    {{-- Copiar a otros centros de trabajo --}}
                    @if($workplaces->count() > 1 && $workplace)
                        <div class="form-group row mt-4">
                            <label for="workplaces" class="col-lg-12">{!! ucfirst(trans('textos.copiar_otros_centros_trabajo')) !!}</label> 
                            <div class="col-lg-6">
                                @php
                                $workplaces__ = [];
                                foreach($workplaces as $wp){
                                    //Descontamos el centro de trabajo seleccionado:
                                    if($wp->id != $workplace->id){
                                        $name = $wp->Town? $wp->name.' - '.$wp->Town:$wp->name;
                                        $workplaces__[$wp->id] = $name;
                                    }
                                }
                                @endphp

                                <x-select name="workplaces[]" class="select2" multiple="true" :options="$workplaces__"/>
                            </div>
                        </div>
                    @endif

                    <x-btn-submit name="submit_save_intervals" label="{{ trans('textos.guardar') }}"/>
                </form>
            </div>
        </div>
    </div>
</div>