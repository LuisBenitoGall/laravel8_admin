<form method="post" autocomplete="off" action="{{ route('sat.update', $sat) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de SAT --}}
		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.sat_num')) !!}</label>
		<div class="col-md-3 col-lg-2">
			@if($patterns && !$sat->sat_num)
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
					@if($sat->sat_num)
						{{ $sat->sat_num }}
					@else
						{{ $sat->id }}
					@endif
				</u></b></label>
			@endif
		</div>

		{{-- Estado --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-lg-4">
			<x-select name="status" class="select2" label="estado_selec" :options="$estados" value="{{ $sat->status }}"/>	
        </div>
	</div>
	
	<div class="form-group row">
		{{-- Título del SAT --}}
		<label for="name" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}</label>
		<div class="col-md-5 col-lg-10">
			<x-input type="text" name="name" value="{!! $sat->name !!}" autocomplete="off" maxlength="255"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Descripción --}}
		<label for="description" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.descripcion')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{{ $sat->description }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" classdiv="col-12 text-right" label="{{ trans('textos.guardar') }}"/>
</form>