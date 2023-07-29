<form method="post" autocomplete="off" action="{{ route('questions.update', $question) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Título --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
		<div class="col-lg-10">
			<x-input type="text" name="name" id="name" value="{!! $question->name !!}" autocomplete="off" maxlength="255" required/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Tiempo --}}
		<label for="time" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.duracion')) !!} <small class="text-warning">({{ trans('textos.minutos') }})</small></label>
		<div class="col-lg-1">
			@php
			//Convertimos segundos en minutos
			$minutes = $question->time? $question->time / 60:'';
			@endphp
			<x-input type="text" name="time" id="time" class="text-right set-decimals" value="{!! $minutes !!}" autocomplete="off" maxlength="8"/>
		</div>

		{{-- Status --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
		<div class="col-lg-1">
			@php
			//Al editarse por primera vez -cuando la cuestión no está todavía seteada- marcamos $status como checked aunque se haya creado la cuestión en estado 0. Las cuestiones no se generan como activas, para evitar que se puedan mostrar sin estar completadas.
			$checked = (!$question->question? 'checked':$question->status == 1)? 'checked':'';
			@endphp
			<label class="custom-chk-rdb" rel="tooltip">
	            <input type="checkbox" name="status" {{ $checked }}>
	            <span class="chk" style="top: 5px;"></span>
	        </label>
		</div>
	</div>
	<br>

	{{-- Tabs idiomas: --}}
	<x-tabs-languages :languages="$languages" />

	<div class="tab-content pt-5">
		@php
		$i = 0;

		$arrQuestions = $question->question? unserialize($question->question):false;
		@endphp

		@foreach($languages as $lang)	
			<div class="tab-pane fade show @if($i == 0) active @endif" role="tabpanel" id="tab-{{ $lang[0] }}">
				<div class="form-group row">
					{{-- Pregunta --}}
					<label for="question" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuestion')) !!} {{ $lang[3] }}*</label>
					<div class="col-lg-10">
						@php
						$q = isset($arrQuestions[$lang[0]])? $arrQuestions[$lang[0]]:'';
						@endphp
						<x-input-textarea name="question_{{ $lang[0] }}" class="editor" value="{!! $q !!}"/>
					</div>
				</div>
			</div>

			@php
			$i++;
			@endphp
		@endforeach
	</div>

	@php
	$arrOptions = $question->options? unserialize($question->options):[];
	@endphp

	<div class="form-group row">
		{{-- Opciones de respuesta --}}
		<label for="options" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.opciones_respuesta')) !!}*</label>
		<div class="col-lg-10">
			<div class="row">
				{{-- @foreach($arrOptions[config('constants.IDM_')] as $op) --}}
				@for($j = 0; $j < $quiz->num_options; $j++)
					<div class="col-lg-8 mb-3">
						@php
						$op = $arrOptions[config('constants.IDM_')][$j]?? '';
						@endphp
						<x-input type="text" name="options_{{ config('constants.IDM_') }}[]" value="{!! $op !!}" autocomplete="off" maxlength="255"/>
					</div>	
					<div class="col-lg-2 mb-3">
						<label class="custom-chk-rdb mr-4" style="margin-bottom: 10px;">
					        <input type="radio" name="right_option" value="{{ $j }}" @if($question->right_option == $j) checked @endif required>
					        <span class="rdb chk"></span>
					    </label>   
					</div>
				@endfor
			</div>
		</div>
	</div>

	{{-- Palabras clave para las cuestiones tipo texto libre: --}}
	@if($question->type == config('constants.QUESTION_FREE_TEXT_'))
		<div class="form-group row">
			{{-- Palabras clave --}}
			<label for="keywords" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.palabras_clave')) !!}</label>
			<x-selectize name="keywords" class="col-lg-10" maxlength="1000" value="{{ $question->keywords }}" autocomplete="off" style="position: relative; z-index: 0;"/>
		</div>

		<div class="form-group row">
			{{-- Mínimo palabras clave --}}
			<label for="keywords_min" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.minimo_valido')) !!}</label>
			<div class="col-lg-2">
				<x-input type="text" name="keywords_min" id="keywords_min" class="text-right set-decimals" value="{!! $question->keywords_min !!}" autocomplete="off" maxlength="3" info="question-keywords-min"/>	
			</div>
		</div>
	@endif

	<div class="form-group row">
		{{-- Imágenes --}}
		<label for="images" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.imagenes')) !!}</label>
		<div class="col-lg-10">
			@if($images->count())
				<x-lightbox-gallery-questions :images="$images"/>
			@endif

			{{-- Dropzone --}}
			<x-dropzone url="/admin/question-images/{{ $question->id }}/store"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Videos --}}
		<label for="videos" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.videos')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="videos" value="{{ $question->videos }}"/>
			<small class="text-warning">{{ trans('textos.videos_aviso') }}</small>
		</div>
	</div>

	<div class="form-group row">
		{{-- URL --}}
		<label for="url" class="col-lg-2 col-form-label">URL</label>
		<div class="col-lg-10">
			<x-input type="text" name="url" value="{!! $question->url !!}" autocomplete="off" maxlength="255"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>