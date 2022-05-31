<form method="post" autocomplete="off" action="{{ route('procedures.update', $procedure) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de procedimiento --}}
		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.procedimiento_num')) !!}</label>
		<div class="col-md-3 col-lg-2">
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

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="procedure-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($procedure->procedure_num)
						{{ $procedure->procedure_num }}
					@else
						{{ $procedure->id }}
					@endif
				</u></b></label>
			@endif
		</div>

		{{-- Nº manual --}}
		<label for="procedure_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.num_manual')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-input type="text" name="procedure_old_num" value="{{ $procedure->procedure_old_num }}" autocomplete="off" maxlength="150" info="procedure-old-num"/>
		</div>

		{{-- Status --}}
		{{-- <label for="procedure_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-md-1">
			<label class="custom-chk-rdb" rel="tooltip">
                <input type="checkbox" name="status" @if($procedure->status == 1) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
		</div> --}}
	</div>

	<div class="form-group row">
		{{-- Título procedimiento --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}*</label>
		<div class="col-lg-10">
			<x-input type="text" name="name" id="name" autocomplete="off" maxlength="255" value="{{ $procedure->name }}" required/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Descripción --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{{ $procedure->description }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>