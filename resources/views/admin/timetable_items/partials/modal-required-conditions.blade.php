<div class="modal fade modal-required-conditions" id="modal-required-conditions" tabindex="-1" role="dialog" aria-labelledby="itemConditions" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="itemConditions">{!! ucfirst(trans('textos.condiciones')) !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('timetable-items.store-rule') }}" id="frmAddItem" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $item->id }}">

                    <p class="text-warning">{{ trans('textos.tipo_jornada_condiciones_texto') }}</p>
                    
					<div class="form-group row">
						{{-- Tabla --}}
						<div class="col-6 col-md-3">
							<select name="tabla" class="form-control" id="ruleTabla">
								<option value="">{{ trans('textos.tabla_selec') }}</option>

								@php
								$tablas = [
									'work_calendars' => 'calendario_empresa'
								];
								@endphp

								@foreach($tablas as $i => $t)
									<option value="{{ $i }}">{{ ucfirst(trans('textos.'.$t)) }}</option>
								@endforeach
							</select>
						</div>

						{{-- Campo --}}
						<div class="col-6 col-md-3">
							<select name="campo" class="form-control" id="ruleCampo">
								<option value="">{{ trans('textos.campo_selec') }}</option>
							</select>
						</div>

						{{-- Regla --}}
						<div class="col-6 col-md-3">
							<select name="regla" class="form-control" id="ruleRegla">
								<option value="">{{ trans('textos.regla_selec') }}</option>
								@foreach($rules as $i => $r)
									<option value="{{ $i }}">{{ ucfirst(trans('textos.'.$r)) }}</option>
								@endforeach
							</select>
						</div>

						{{-- Valor --}}
						<div class="col-6 col-md-3">
							<x-input type="text" name="valor" id="ruleValor" placeholder="{{ ucfirst(trans('textos.valor')) }}" autocomplete="off" maxlength="25"/>
						</div>
					</div>

					<x-btn-submit name="submit_rule" label="{{ trans('textos.guardar') }}"/>                
				</form>    
            </div>
        </div>
    </div>
</div>

