@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('employees.index')
			<a href="{{ route('employees.user', $employee->user_id) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_contratos_empleado') !!}
			</a>
		@endcan

		@can('employees.create')
			<a class="btn btn-info" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.empleado_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-sm-12">		
		<div class="card mb-3">
			<div class="card-body">				
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.contrato_editar')) !!} <u>{{ $employee->name }} {{ $employee->surname }}</u>
				</h3>	

				{{-- Info --}}
				<div class="mb-4">
					<label class="mr-5">{!! ucfirst(trans('textos.contrato')) !!}: <strong>{{ $employee->Contract }}</strong></label>

					@php
					$inicio = Carbon\Carbon::parse($employee->start_date)->format($languages[session()->get('locale')][4]);
					@endphp

					<label class="mr-5">{!! ucfirst(trans('textos.inicio')) !!}: <strong>{{ $inicio }}</strong></label>

					@if($employee->end_date == config('constants.UNDEFINED_DATE_'))
						@php
						$fin = ucfirst(trans('textos.indefinido'));
						@endphp
					@else
						@php
						$fin = Carbon\Carbon::parse($employee->end_date)->format($languages[session()->get('locale')][4]);
						@endphp
					@endif

					<label class="mr-5">{!! ucfirst(trans('textos.fin')) !!}: <strong>{{ $fin }}</strong></label>
				</div>

				<form method="post" autocomplete="off" action="{{ route('employees.update', $employee->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Fecha inicio --}}
						<label for="start_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.inicio')) !!}*</label>
						<div class="col-lg-4">
							<x-date-picker name="start_date" :format="$languages[config('app.locale')][4]" required value="{{ $inicio }}"/>
						</div>

						{{-- Fecha fin --}}
						<label for="end_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fin')) !!}</label>
						<div class="col-lg-4">
							<x-date-picker name="end_date" :format="$languages[config('app.locale')][4]" value="{{ $fin }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Puesto laboral --}}
						<label for="job_title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.puesto')) !!}</label>
						<div class="col-lg-4">
							<x-input type="text" name="job_title"  autocomplete="off" maxlength="150" value="{{ $employee->job_title }}"/>
						</div>

						{{-- Salario anual --}}
						<label for="year_salary" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.salario_anual')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="year_salary" class="text-right set-decimals" id="year_salary" autocomplete="off" placeholder="0" maxlength="9" group="right" groupvalue="{{ $currency->symbol }}" value="{{ $employee->year_salary }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Horas semanales --}}
						<label for="weekly_hours" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.horas_semanales')) !!}*</label>
						<div class="col-md-1">
							<x-input type="text" name="weekly_hours" class="set-decimals text-right" id="weekly_hours" autocomplete="off" placeholder="0" maxlength="2" required value="{{ $employee->weekly_hours }}"/>
						</div>

						{{-- Control horario --}}
						<label for="time_control" class="col-lg-2 col-form-label">{!! trans('textos.control_horario') !!}</label>
						<div class="col-md-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="time_control" @if($employee->time_control == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>

				                {!! App\Helpers\helpers::info('employee-time-control', true) !!}
				            </label>
						</div>

						{{-- Conceptos obligatorios --}}
						<label for="required_concepts" class="col-lg-2 col-form-label">{!! trans('textos.conceptos_obligatorios') !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="required_concepts" @if($employee->required_concepts == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>

				                {!! App\Helpers\helpers::info('employee-required-concepts', true) !!}
				            </label>
						</div>
					</div>

					{{-- Observaciones --}}
					<div class="form-group row">
						<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" class="editor" value="{{ $employee->observations }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection