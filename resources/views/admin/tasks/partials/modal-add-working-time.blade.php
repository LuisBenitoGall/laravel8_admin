<div class="modal fade modal-add-working-time" id="modal-add-working-time" tabindex="-1" role="dialog" aria-labelledby="addWorkingTime" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addWorkingTime">{!! ucfirst(trans('textos.tiempo_trabajo')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p class="text-warning">{{ trans('textos.tiempo_trabajo_texto') }}</p>

				<form method="post" autocomplete="off" action="{{ route('tasks.store-working-time') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="workingTimeTaskId">
                    <input type="hidden" name="return" value="{{ $return }}">

                    <div class="form-group row">
                        {{-- Tiempo trabajo --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="time_" class="col-lg-12">{!! ucfirst(trans('textos.horas')) !!}*</label>
                                <div class="col-lg-12">
                                    <x-input type="text" name="time_" class="text-right set-decimals" autocomplete="off" maxlength="7" required/>
                                </div>        
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="col-md-6">
                            <div class="row">
                                <label for="date_" class="col-lg-12">{!! ucfirst(trans('textos.fecha')) !!}*</label>
                                <div class="col-lg-12">
                                    <x-date-picker name="date_" :format="$languages[config('app.locale')][4]" required/>
                                </div>    
                            </div>
                        </div>
                    </div>

                    <x-btn-submit name="submit_working-time" label="{{ trans('textos.guardar') }}"/>
                </form>
                <hr>

                {{-- Histórico --}}
                <div id="working-time-historical">
                    
                </div>
            </div>
        </div>
    </div>
</div>