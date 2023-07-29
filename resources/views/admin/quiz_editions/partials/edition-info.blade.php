<form method="post" autocomplete="off" action="{{ route('quiz-editions.update', $edition) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Edición --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.edicion')) !!}*</label>
		<div class="col-lg-6">
			<x-input type="text" name="name" id="name" value="{!! $edition->name !!}" autocomplete="off" maxlength="255" required/>
		</div>
		
		{{-- Status --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip">
                <input type="checkbox" name="status" @if($edition->status == 1) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
		</div>
	</div>

	<div class="form-group row">
		<div class="col-8 offset-md-2">
			<h4>{{ trans('textos.concurso_configurar') }}</h4>	
			<p class="text-warning">{{ trans('textos.concurso_configurar_texto') }}</p>
		</div>
	</div>

	<div class="form-group row">
		{{-- Inicio --}}
		<label for="start" class="col-lg-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.inicio')) !!}</label>
		<div class="col-lg-3 col-xl-2">
			@php
			$start = $edition->start != ''? Carbon\Carbon::parse($edition->start)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="start" id="start" value="{{ $start }}" :format="$languages[config('app.locale')][4]" disabled="{{ $disabled }}"/>
		</div>

		{{-- Finalización --}}
		<label for="finish" class="col-lg-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.finalizacion')) !!}</label>
		<div class="col-lg-3 col-xl-2">
			@php
			$finish = $edition->finish != ''? Carbon\Carbon::parse($edition->finish)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="finish" id="finish" value="{{ $finish }}" :format="$languages[config('app.locale')][4]" disabled="{{ $disabled }}"/>
		</div>

		{{-- Duración --}}
		<label for="time" class="col-lg-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.duracion')) !!} <small class="text-warning">({{ trans('textos.segundos') }})</small></label>
		<div class="col-lg-3 col-xl-2">
			@php
			//Convertimos segundos en minutos
			//$minutes = $edition->time? $edition->time / 60:'';
			@endphp
			<x-input type="text" name="time" id="time" class="text-right set-decimals" value="{!! $edition->time !!}" autocomplete="off" maxlength="8" disabled="{{ $disabled }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Número de preguntas --}}
		<label for="num_questions" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.numero_preguntas')) !!}</label>	
		<div class="col-lg-2">	
			<x-input type="text" name="num_questions" value="{{ $edition->num_questions }}" autocomplete="off" maxlength="5" disabled="{{ $disabled }}"/>
		</div>

		{{-- Número de días --}}
		<label for="num_days" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.numero_dias')) !!}</label>	
		<div class="col-lg-2">	
			<x-input type="text" name="num_days" value="{{ $edition->num_days }}" autocomplete="off" maxlength="5" disabled="{{ $disabled }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Descripción --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">	
			<x-input-textarea name="description" class="editor" value="{{ $edition->description }}"/>
		</div>
	</div>

	<div class="form-group row">
        {{-- Logo --}}
        @if($edition->logo)
        	<label class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.logo')) !!}</label>

			<div class="col-lg-4 foto">
				<img class="img-fluid" src="{{ asset('img/quizzes/'.$edition->logo) }}">	

				<div class="controls text-right pt-2">
					<a href="{{ route('quiz-editions.delete-logo', $edition) }}" class="btn-confirm" data-msg="{{ ucfirst(trans('textos.imagen_confirm_eliminar')) }}" rel="tooltip" title="{{ ucfirst(trans('textos.imagen_eliminar')) }}">
						<i class="fa fa-trash"></i>
					</a>
				</div>
			</div>

		@else
            <label for="logo" class="col-md-2 col-form-label">Logo
				<br>
				<small class="text-warning">
					· {{ trans('textos.formato') }}: .jpg, .png, .gif <br>
					· {{ trans('textos.tamany_max') }}: 1MB	<br>
					· {{ trans('textos.medidas_recomendadas') }}: 400x400px							
				</small>
			</label>
			<div class="col-md-4">
				<x-input-file name="logo"/>

				@if($errors->has('logo'))
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $errors->first('logo') }}</strong>
                    </span>
                @endif
			</div>
		@endif
	</div>
	
	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>
