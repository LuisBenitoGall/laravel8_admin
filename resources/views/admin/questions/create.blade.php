@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@if($quiz)
			<a href="{{ route('quizzes.edit', $quiz) }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_concurso') !!}
			</a>
		@else
			<a href="{{ route('questions.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_cuestiones') !!}
			</a>
		@endif
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('questions.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					@if($quiz)
						<input type="hidden" name="quiz_id" value="{{ $quiz }}">
					@endif

					<div class="form-group row">
						{{-- Título --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-7">
							<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.titulo')) !!}" autocomplete="off" maxlength="255" required/>
						</div>

						{{-- Status --}}
						{{-- <label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" checked>
				                <span class="chk" style="top: 5px;"></span>
				            </label>
						</div> --}}
					</div>

					<div class="form-group row">
						{{-- Tipo de cuestión --}}
						<label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}*</label>
						<div class="col-lg-4">
							@php
				            $options = App\Helpers\Helpers::questionTypes();
				            @endphp

				            <x-select name="type" class="select2 question_type" label="cuestion_tipo_selec" :options="$options" required info="question-type"/>
						</div>

						{{-- Tipo de respuesta --}}
						<label for="answer_type" class="col-lg-2 col-form-label answer_type_input">{!! ucfirst(trans('textos.respuesta_tipo_valor')) !!}*</label>
						<div class="col-lg-4 answer_type_input">
							@php
				            $options = App\Helpers\Helpers::answerValueTypes();
				            @endphp

				            <x-select name="answer_type" class="select2" label="respuesta_tipo_selec" :options="$options" info="question-answer-type"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Selectores condicionados:
	$('.question_type').on('change', function(){
		var value = $(this).val();
		$('.answer_type_input').slideUp();

		if(value == {{ config('constants.QUESTION_TEST_') }}){
			$('.answer_type_input').slideDown();	
		}
	});
});
</script>
@endpush

@endsection