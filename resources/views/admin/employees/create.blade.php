@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('employees.index')
			<a href="{{ route('employees.user', $user) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_contratos_empleado') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-sm-12">		
		<div class="card mb-3">
			<div class="card-body">				
				<h3 class="mb-4"><u>{!! ucfirst(trans('textos.contrato_nuevo')) !!} {{ $user->name }} {{ $user->surname }}</u></h3>	

				@if($contracts->count())
					<form method="post" autocomplete="off" action="{{ route('employees.store', $user->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
						{{ csrf_field() }}

						@if(count($workplaces) == 1)
							@php
							$array_keys = array_keys($workplaces);
							@endphp
							<input type="hidden" name="workplace_id" value="{{ $array_keys[0] }}">
						@endif

						<div class="form-group row">
							{{-- Centro de trabajo --}}
							@if(count($workplaces) > 1)
								<label for="workplace_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_trabajo')) !!}*</label>
								<div class="col-lg-3">
									<x-select name="workplace_id" class="select2" label="centro_trabajo_selec" :options="$workplaces" required/>
								</div>
							@endif

							{{-- Fecha inicio --}}
							<label for="start_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.inicio')) !!}*</label>
							<div class="col-lg-2">
								<x-date-picker name="start_date" :format="$languages[config('app.locale')][4]" required/>
							</div>

							{{-- Fecha fin --}}
							<label for="end_date" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.fin')) !!}</label>
							<div class="col-lg-2">
								<x-date-picker name="end_date" :format="$languages[config('app.locale')][4]"/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Tipo de contrato --}}
							<label for="contract_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contrato')) !!}*</label>
							<div class="col-lg-4">
								@php
								$contracts_ = [];
								foreach($contracts as $contract){
									$contracts_[$contract->id] = $contract->name;
								}
								@endphp

								<x-select name="contract_id" class="select2" label="contrato_selecciona" :options="$contracts_"/>
							</div>

							{{-- Puesto laboral --}}
							<label for="job_title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.puesto')) !!}</label>
							<div class="col-lg-4">
								<x-input type="text" name="job_title"  autocomplete="off" maxlength="150"/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Horas semanales --}}
							<label for="weekly_hours" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.horas_semanales')) !!}*</label>
							<div class="col-lg-2">
								<x-input type="text" name="weekly_hours" class="set-decimals text-right" id="weekly_hours" autocomplete="off" placeholder="0" maxlength="2" required/>
							</div>

							{{-- Control horario --}}
							<label for="time_control" class="col-lg-2 col-form-label">{!! trans('textos.control_horario') !!}</label>
							<div class="col-lg-2">
								<label class="custom-chk-rdb" rel="tooltip">
					                <input type="checkbox" name="time_control" checked>
					                <span class="chk" style="top: 5px;"></span>
					            </label>
							</div>

							{{-- Salario anual --}}
							<label for="year_salary" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.salario_anual')) !!}</label>
							<div class="col-lg-2">
								<x-input type="text" name="year_salary" class="text-right set-decimals" id="year_salary" autocomplete="off" placeholder="0" maxlength="9" group="right" groupvalue="{{ $currency->symbol }}"/>
							</div>
						</div>

						<div class="form-group row">
							{{-- Observaciones --}}
							<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
							<div class="col-lg-10">
								<x-input-textarea name="observations" class="editor"/>
							</div>
						</div>
						
						<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
					</form>

				@else
					<br><br>
					<h5 class="text-warning">{{ trans('textos.contratos_no_hay_aviso') }}
						<a href="{{ route('contracts.create') }}" class="btn btn-info ml-4">{{ trans('textos.contrato_crear') }}</a>
					</h5>
				@endif
			</div>
		</div>
	</div>
</div>

@endsection