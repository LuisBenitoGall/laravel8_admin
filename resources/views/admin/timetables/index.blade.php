@extends('admin.partials.layout')

@section('content')

{{-- Filtro formulario: --}}
<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('employees-timetable.index') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Centro de trabajo --}}
						@if($workplaces->count() > 1)
							<label for="workplace_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centros_trabajo')) !!}*</label>
							<div class="col-lg-4">
								@php
								$workplaces_ = [];
								foreach($workplaces as $wp){
									$name = $wp->Town? $wp->name.' - '.$wp->Town:$wp->name;
									$workplaces_[$wp->id] = $name;
								}

								$value = $workplace? $workplace->id:'';
								@endphp

								<x-select name="workplace_id" class="select2" label="centro_trabajo_selec" :options="$workplaces_" value="{{ $value }}" required/>
							</div>
						@elseif($workplaces->count() == 1)
							<input type="hidden" name="workplace_id" value="{{ $workplaces[0]->id }}">
						@endif
					</div>

					<div class="form-group row">
						{{-- Desde --}}
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-lg-2">
							@php
							$from_ = $from != ''? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="from" value="{{ $from_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>

						{{-- Hasta --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-lg-2">
							@php
							$to_ = $to != ''? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):'';
							@endphp

							<x-date-picker name="to" value="{{ $to_ }}" :format="$languages[config('app.locale')][4]"/>	
						</div>

						<x-btn-submit name="submit_filter" class="ml-5" label="{{ trans('textos.buscar') }}" inline="true"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Resultados --}}
@if($workplace)	
	<input type="hidden" id="dtWorkplace" value="{{ $workplace->id }}">
	<input type="hidden" id="dtFrom" value="{{ $from }}">
	<input type="hidden" id="dtTo" value="{{ $to }}">

	<div class="row">
		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblEmployeesHours">
				<thead>
					<tr>
						<th>{!! ucfirst(trans('textos.empleado')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.horas_trabajadas')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.horas_laborables')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.saldo')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.situacion')) !!}</th>
						<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		</div>	
	</div>
@endif

@endsection