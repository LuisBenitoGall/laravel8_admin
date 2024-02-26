<form method="post" autocomplete="off" action="{{ route('projects.update', $project) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{ $project->id }}">

	<div class="form-group row mt-3">
		{{-- Título --}}
		<label for="name" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.proyecto_titulo')) !!}</label>
		<div class="col-md-7">
			<x-input type="text" name="name" value="{{ $project->name }}" autocomplete="off" maxlength="255"/>
		</div>

		{{-- Acceso restringido --}}
		<label for="restricted_access" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.acceso_restringido')) !!}</label>
		<div class="col-md-1">
			<label class="custom-chk-rdb" rel="tooltip">
	            <input type="checkbox" name="restricted_access" @if($project->restricted_access) checked @endif>
	            <span class="chk" style="top: 5px;"></span>

	        	{!! App\Helpers\helpers::info('project-restricted-access', true) !!}
	        </label>
		</div>
	</div>

	<div class="form-group row">
		{{-- Nº de proyecto --}}
		<label for="pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proyecto_num')) !!}</label>
		<div class="col-lg-2">
			{{-- Patrón de numeración - condiciones:
			 	1. Si existen patrones.
				...
			--}}	

			@if($patterns)
				@if($patterns->count())
					@php
					$patterns_ = [];
					foreach($patterns as $ptn){
						$patterns_[$ptn->id] = $ptn->name;
					}
					@endphp

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="order-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($project->project_num)
						{{ $project->project_num }}
					@else
						{{ $project->id }}
					@endif
				</u></b></label>
			@endif

			{{-- Reset de número - condiciones:
				1. Debe existir el número de proyecto
			--}}
			@if($project->project_num)
				<a href="{{ route('projects.reset-num', $project->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.proyecto_num_reset_confirm') }}">
					<i class="fa fa-redo"></i>
				</a>
			@endif
		</div>

		{{-- Proyecto nº manual --}}
		<label for="project_old_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proyecto_num_manual')) !!}</label>
		<div class="col-lg-2">
			<x-input type="text" name="project_old_num" value="{{ $project->project_old_num }}" autocomplete="off" maxlength="150" info="order-old-num"/>
		</div>

		{{-- Gràfica destacada --}}
		<label for="chart" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.grafico_principal')) !!}</label>
		<div class="col-lg-2">
			@php
			$charts = App\Helpers\helpers::projectCharts();
			@endphp
			<x-select name="chart" class="select2" label="grafico_selec" :options="$charts" value="{{ $project->chart }}" info="project-chart"/>
		</div>
	</div>

	<div class="row">
		<div class="col-12 col-lg-8 pb-5">
			<div class="form-group row">
				{{-- Estado --}}
				<label for="status" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
				<div class="col-lg-3">	
					@php
					$estados = App\Helpers\helpers::projectStatus();
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

			{{-- Cliente --}}
			@if(!$customer)
				<div class="form-group row">
					<label for="customer_id" class="col-md-3 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>
					<div class="col-md-4">
						@php
						$customers_ = [];
						@endphp

						@foreach($customers as $ctm)
							@php
							$company = $ctm->tradename? $ctm->name.' ('.$ctm->tradename.')':$ctm->name;
							$customers_[$ctm->id] = $company;
							@endphp
						@endforeach

						<x-select name="customer_id" class="select2" label="selec_cliente" :options="$customers_" info="project-customer"/>	
					</div>
				</div>
			@endif

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

			<div class="form-group row">
				{{-- Categorías --}}
				<label for="categories" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.categorias')) !!}</label>
				<div class="col-lg-9">
					<x-select name="categories[]" class="select2" multiple :options="$categories" :values="$categories_in"/>
				</div>
			</div>
		</div>

		{{-- Charts --}}
		{{-- 18/03/2023: cada nuevo chart se agrega con un condicional, y debe declararse su id en helpers/projectCharts() --}}
		@if($project->chart == 'pp')
			<div class="col-12 col-lg-4 text-center" style="height: 9vh;">
				<div class="chart chart-mini" style="position: relative; height: 5vh; width: 13vw">
					<canvas id="chartProjectPercent"></canvas>
				</div>
			</div> 

		@else
			<div class="col-12 col-lg-4 text-center">
				<div class="chart" style="display:flex; justify-content: center; align-items: center; position: relative; height: 100%; width: 90%">
					<canvas id="chartEconomicBalance_"></canvas>
				</div>
			</div>
		@endif
	</div>
	<br><br>

	{{-- Descripción breve --}}
	{{-- <div class="form-group row mt-5">
		<label for="short_description" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.descripcion_breve')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="short_description" value="{{ $project->short_description }}"/>
		</div>
	</div> --}}

	{{-- Descripción --}}
	<div class="form-group row">
		<label for="description" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.descripcion')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{{ $project->description }}"/>
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