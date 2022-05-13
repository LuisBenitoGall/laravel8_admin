<div class="modal fade modal-config-day-types" id="modal-config-day-types" tabindex="-1" role="dialog" aria-labelledby="configDayType" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="configDayType">{!! ucfirst(trans('textos.tipo_jornada_config')) !!} <strong></strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{!! trans('textos.tipo_jornada_config_aviso') !!}</p>

                <form method="post" autocomplete="off" action="{{ route('day-types-config.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    <input type="hidden" name="id" id="typeId">
                    {{ csrf_field() }}

                    <div class="form-group row">
                        <div class="col-md-3">
                            <div class="row">
                                {{-- Año --}}
                                <label for="year" class="col-lg-12">{{ ucfirst(trans('textos.any')) }} *</label>
                                <div class="col-lg-12">
                                    @php
                                    $years = [];
                                    for($y=config('constants.MIN_YEAR_'); $y<=(date('Y')+3); $y++){
                                        $years[$y] = $y;
                                    }
                                    @endphp

                                    <x-select name="year" class="select2" id="dayTypeYear" label="selecciona_any" :options="$years"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                {{-- Mínimo de días --}}
                                <label for="min" class="col-lg-12">{{ ucfirst(trans('textos.dias_min')) }}</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="min" class="text-right" autocomplete="off" maxlength="3" placeholder="0"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                {{-- Máximo de días --}}
                                <label for="max" class="col-lg-12">{{ ucfirst(trans('textos.dias_max')) }}</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="max" class="text-right" autocomplete="off" maxlength="3" placeholder="0"/>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                {{-- Máximo de días por mes --}}
                                <label for="max_per_month" class="col-lg-12">{{ ucfirst(trans('textos.dias_max_mes')) }}</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="max_per_month" class="text-right" autocomplete="off" maxlength="2" placeholder="0"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                    $months = App\Helpers\helpers::yearMonths();
                    @endphp

                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>

                <div id="typesConfigList">
                    
                </div>
            </div>
        </div>
    </div>
</div>