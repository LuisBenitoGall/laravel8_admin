<form method="post" autocomplete="off" action="{{ route('projects.update', $project) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{ $project->id }}">

	<div class="form-group row mt-3">
		{{-- Título --}}
		<label for="name" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.proyecto_titulo')) !!}</label>
		<div class="col-md-10">
			<x-input type="text" name="name" value="{{ $project->name }}" autocomplete="off" maxlength="255"/>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-lg-8">
			<div class="form-group row">
				{{-- Estado --}}
				<label for="status" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
				<div class="col-lg-3">	
					@php
					$estados = App\Helpers\Helpers::projectStatus();
					@endphp

					<x-select name="status" class="select2" label="indica_estado" :options="$estados" value="{{ $project->status }}"/>
				</div>

				{{-- Grupos --}}
				<label for="groups" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.grupos')) !!}</label>
				<div class="col-lg-3">
					@php
					$projectGroups = $project->groups? explode(',', $project->groups):[];
					@endphp

					<x-select name="groups[]" class="select2" multiple="true" :options="$groups" :values="$projectGroups"/>
				</div>
			</div>

			<div class="form-group row">
				{{-- Fecha entrega prevista --}}
				<label for="estimated_delivery_date" class="col-md-3 col-form-label">{!! ucfirst(trans('textos.fecha_entrega_prevista')) !!}</label>
				<div class="col-md-3">
					@php
					$estimatedDeliveryDate = $project->estimated_delivery_date != ''? \Carbon\Carbon::parse($project->estimated_delivery_date)->format($languages[session()->get('locale')][4]):'';
					@endphp

					<x-date-picker name="estimated_delivery_date" id="estimated_delivery_date" value="{{ $estimatedDeliveryDate }}" :format="$languages[config('app.locale')][4]"/>
				</div>

				{{-- Fecha entrega --}}
				<label for="delivery_date" class="col-md-3 col-form-label">{!! ucfirst(trans('textos.fecha_entrega')) !!}</label>
				<div class="col-md-3">
					@php
					$deliveryDate = $project->delivery_date != ''? \Carbon\Carbon::parse($project->delivery_date)->format($languages[session()->get('locale')][4]):'';
					@endphp

					<x-date-picker name="delivery_date" id="delivery_date" value="{{ $deliveryDate }}" :format="$languages[config('app.locale')][4]"/>
				</div>
			</div>

			<div class="form-group row">
				{{-- Previsión horas --}}
				<label for="hours_forecast" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.prevision_horas')) !!}</label>
				<div class="col-lg-3">
					<x-input type="text" name="hours_forecast" value="{{ $project->hours_forecast }}" class="text-right" autocomplete="off" maxlength="4"/>
				</div>

				{{-- Info horas previstas / trabajadas --}}
				<div class="col-lg-3 pt-2">
					@if($project->task_hours > $project->hours_forecast)
						<i class="fa fa-hourglass-half text-warning mr-3" rel="tooltip" title="{{ trans('textos.horas_previstas_aviso') }}"></i>
					@endif

					@if($project->hours_worked > $project->hours_forecast)
						<i class="fa fa-hourglass-end text-danger" rel="tooltip" title="{{ trans('textos.horas_trabajadas_aviso') }}"></i>
					@endif
				</div>
			</div>

			<div class="form-group row">
				{{-- Horas previsión de tareas --}}
				<label for="task_hours_" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.horas_x_tareas')) !!}</label>
				<div class="col-lg-3">
					<x-input type="text" name="task_hours_" value="{{ round($project->task_hours, 2) }}" class="text-right" autocomplete="off" readonly="true"/>
				</div>

				{{-- Horas trabajadas --}}
				<label for="hours_worked" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.horas_trabajadas')) !!}</label>
				<div class="col-lg-3">
					<x-input type="text" name="hours_worked" value="{{ $project->hours_worked }}" class="text-right" autocomplete="off" readonly="true"/>
				</div>
			</div>
		</div>

		{{-- Charts --}}
		<div class="col-12 col-lg-4 text-center">
			<div class="chart">
				<canvas id="chartProjectPercent"></canvas>
			</div>
		</div>
	</div>

	{{-- Descripción --}}
	<div class="form-group row">
		<label for="description" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.descripcion')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{{ $project->briefing }}"/>
		</div>
	</div>

	{{-- Briefing --}}
	<div class="form-group row">
		<label for="briefing" class="col-lg-2 col-form-label">Briefing</label>
		<div class="col-lg-10">
			<x-input-textarea name="briefing" class="editor" value="{{ $project->briefing }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>