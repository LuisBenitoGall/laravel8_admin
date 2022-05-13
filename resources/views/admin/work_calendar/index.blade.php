@extends('admin.partials.layout')

@section('content')

@if($workplace)
	<div class="row mb-3">
		{{-- Acciones: --}}
		<div class="col-12">
			@can('work-calendar.edit')
				{{-- Establecer horarios laborables --}}
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-working-hours">
					<i class="fa fa-clock"></i>
					{!! ucfirst(trans('textos.horarios_laborables')) !!}
				</a>

				{{-- Copiar calendario --}}
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-copy-working-hours">
					<i class="fa fa-copy"></i>
					{!! ucfirst(trans('textos.calendario_copiar')) !!}
				</a>

				{{-- Establecer rango --}}
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-rank">
					<i class="fa fa-calendar"></i>
					{!! ucfirst(trans('textos.rango_agregar')) !!}
				</a>
			@endcan
		</div>
	</div>
@endif

{{-- Filtro formulario: --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('work-calendar.calendar-by') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Centro de trabajo --}}
						@if($workplaces->count() > 1)
							<label for="workplace_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centros_trabajo')) !!} </label>
							<div class="col-lg-4">
								@php
								$workplaces_ = [];
								foreach($workplaces as $wp){
									$name = $wp->Town? $wp->name.' - '.$wp->Town:$wp->name;
									$workplaces_[$wp->id] = $name;
								}

								$value = $workplace? $workplace->id:'';
								@endphp

								<x-select name="workplace_id" class="select2" label="centro_trabajo_selec" :options="$workplaces_" value="{{ $value }}"/>
							</div>
						@elseif($workplaces->count() == 1)
							<input type="hidden" name="workplace_id" value="{{ $workplace->id }}">
						@endif

						{{-- Año --}}
						<label for="year" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.any')) !!}</label>
						<div class="col-lg-2">
							@php
                            $years = [];
                            for($y=config('constants.MIN_YEAR_'); $y<=(date('Y')+3); $y++){
                                $years[$y] = $y;
                            }
                            @endphp

                            <x-select name="year" class="select2" id="calendarYear" label="selecciona_any" :options="$years" value="{{ $year }}"/>
						</div>

						<x-btn-submit name="submit_save" label="{{ trans('textos.buscar') }}" inline="true"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Calendario --}}
@if($workplace)
	<div class="row mb-3">
		<div class="col-12">						
			<div class="card mb-3">
				<div class="card-body">
					<h3 class="mb-3">
						{!! ucfirst(trans('textos.calendario')) !!} 
						<u>{{ $company->name }} - {{ $workplace->name }}, {{ $year }}</u>
					</h3>
					<p class="text-warning">{!! trans('textos.calendario_laboral_sede_texto') !!}</p>

					{{-- Component Calendario --}}
					@livewire('calendar', ['year' => $year, 'workplace' => $workplace])

				</div>
			</div>
		</div>
	</div>
@endif

{{-- Modals --}}
@if($workplace)
	{{-- Working Hours --}}
	@include('admin.work_calendar.partials.modal-working-hours')

	{{-- Copy calendar --}}
	@include('admin.work_calendar.partials.modal-copy-calendar')
@endif

@endsection