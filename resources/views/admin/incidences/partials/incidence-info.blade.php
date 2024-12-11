<form method="post" autocomplete="off" action="{{ route('incidences.update', $incidence) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	{{ csrf_field() }}

	<div class="form-group row mt-3">
		{{-- Fecha incidencia --}}
		<label for="incidence_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}*</label>
		<div class="col-lg-2">
			@php
			$incidenceDate = $incidence->incidence_date != ''? \Carbon\Carbon::parse($incidence->incidence_date)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="incidence_date" :format="$languages[config('app.locale')][4]" required value="{{ date('d/m/Y') }}"/>
		</div>	

		{{-- Concepto --}}
		<label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
		<div class="col-lg-6">
			<x-input type="text" name="concept" maxlength="255" placeholder="{!! ucfirst(trans('textos.concepto')) !!}" autocomplete="off" required value="{{ $incidence->concept }}"/>
		</div>	
	</div>

	<div class="form-group row">
		{{-- Nº de proyecto --}}
		@php
		//Patrón requerido:
		$pattern_required = $patterns && $patterns->count() && session('companySettings')->pattern_incidences? true:false;
		@endphp

		<label for="pattern_id" class="col-lg-2 col-form-label">
			{!! ucfirst(trans('textos.incidencia_num')) !!}
			@if($pattern_required)
				*
			@endif
		</label>
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

					<x-select name="pattern_id" class="select2 prominent" label="patron_selecciona" :options="$patterns_" required="{{ $pattern_required }}" info="incidence-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($incidence->incidence_num)
						{{ $incidence->incidence_num }}
					@else
						{{ $incidence->id }}
					@endif
				</u></b></label>
			@endif

			{{-- Reset de número - condiciones:
				1. Debe existir el número de proyecto
			--}}
			@if($incidence->project_num)
				<a href="{{ route('projects.reset-num', $incidence->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.proyecto_num_reset_confirm') }}">
					<i class="fa fa-redo"></i>
				</a>
			@endif
		</div>

		{{-- Incidence nº manual --}}
		<label for="incidence_old_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.num_manual')) !!}</label>
		<div class="col-lg-2">
			<x-input type="text" name="incidence_old_num" value="{{ $incidence->incidence_old_num }}" autocomplete="off" maxlength="150" info="incidence-old-num"/>
		</div>

		{{-- Estado --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-lg-2">
			<x-select name="status" class="select2" label="estado_selecciona" :options="$incidence_status" value="{{ $incidence->status }}"/>
		</div>
	</div>



	<div class="form-group row">

		{{-- Usuario --}}
		<label for="subject" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.usuario')) !!}</label>
		<div class="col-lg-4 position-relative">
			<x-input type="text" name="subject" id="search_user" placeholder="{!! ucfirst(trans('textos.usuario')) !!}" maxlength="150"/>

			<x-input-errors name="subject_id"/>
		</div>

		<input type="hidden" name="subject_id" id="userId">
	</div>

	<div class="form-group row">
		{{-- Descripción --}}
    	<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="observations" class="editor" value="{!! $incidence->observations !!}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>