@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="questionId" value="{{ $question->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('questions.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_cuestiones') !!}
		</a>

		@can('questions.create')
			<a class="btn btn-info" href="{{ route('questions.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuestion_nueva')) !!}</a>
		@endcan

		@can('questions.edit')
			<a class="btn btn-info" href="#" data-toggle="modal" data-target="#modal-add-answer"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.respuesta_nueva')) !!}</a>
		@endcan
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.edicion')) !!} 
					<u>{{ $question->name }}</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Tipo --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.tipo')) !!}: 
						<b>
							@if($question->type) {!! App\Helpers\helpers::questionTypes($question->type) !!} @endif
						</b>
					</label>

					{{-- Tipo de respuesta --}}
					@if($question->answer_type)
						<label class="mr-5">
							{!! ucfirst(trans('textos.respuesta_tipo')) !!}: 
							<b>
								{!! App\Helpers\helpers::answerValueTypes($question->answer_type) !!}
							</b>
						</label>
					@endif

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $question->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $question->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
			  		<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-question-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'answers') active @endif">
			    		<a class="nav-link @if($tab == 'answers') active @endif" href="#tab-question-answers" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.respuestas')) !!}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-5">
			  		{{-- Info de la cuestión --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-question-info">
						@include('admin.questions.partials.question-info')
					</div>	

					{{-- Respuestas --}}
					<div class="tab-pane fade @if($tab == 'answers') show active @endif" role="tabpanel" id="tab-question-answers">
						@include('admin.questions.partials.question-answers')
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>

{{-- Modals: --}}
{{-- Add answer --}}
<div class="modal fade modal-add-answer" id="modal-add-answer" tabindex="-1" role="dialog" aria-labelledby="addAnswer" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addAnswer">{{ ucfirst(trans('textos.respuesta_nueva')) }} <u></u></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('answers.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="question_id" value="{{ $question->id }}">

					{{-- Título --}}
					<div class="form-group row">
                    	<label for="name" class="col-12">{!! ucfirst(trans('textos.titulo')) !!}*</label>
						<div class="col-12">
							<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Tipo de valor: en función del tipo de respuesta de la cuestión --}}
						{{-- Booleana --}}
						@if($question->answer_type == config('constants.ANSWER_TYPE_BOOLEAN_'))
							<label for="is_true" class="col-4 col-lg-3 col-form-label" style="text-align: left;">{!! ucfirst(trans('textos.verdadero_falso')) !!}*</label>	
							<div class="col-8 col-lg-6">
								<label class="custom-chk-rdb pt-2">
					    			<input class="rdb-access" type="radio" name="is_true" value="1" required>
					    			<span class="rdb" style="top: 5px;"></span>
					    			{!! trans('textos.verdadero') !!}
								</label>	

								<label class="custom-chk-rdb pt-2 ml-5">
					    			<input class="rdb-access" type="radio" name="is_true" value="0" required>
					    			<span class="rdb" style="top: 5px;"></span>
					    			{!! trans('textos.falso') !!}
								</label>

								<x-input-errors name="is_true"/>
							</div>

						{{-- Por puntos --}}
						@else
							<label for="points" class="col-4 col-lg-1 col-form-label" style="text-align: left;">{!! ucfirst(trans('textos.puntos')) !!}*</label>
							<div class="col-8 col-lg-4">
								<x-input type="text" name="points" autocomplete="off" maxlength="8" required value="0"/>
							</div>	
						@endif

						{{-- Estado --}}
						<label for="status" class="col-4 col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-8 col-lg-1">
							<label class="custom-chk-rdb" style="margin-top: 5px;">
			    				<input type="checkbox" name="status" checked>
			    				<span class="chk"></span>
							</label>
						</div>	
					</div>

					{{-- Respuesta --}}
					<div class="form-group row">
                    	<label for="answer" class="col-12">{!! ucfirst(trans('textos.respuesta')) !!}*</label>
						<div class="col-12">
							<x-input-textarea name="answer" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Edit answer --}}
<div class="modal fade modal-edit-answer" id="modal-edit-answer" tabindex="-1" role="dialog" aria-labelledby="editAnswer" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editAnswer">{{ ucfirst(trans('textos.respuesta_editar')) }} <u></u></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('answers.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="id" id="answerId">
					<input type="hidden" name="question_id" value="{{ $question->id }}">

					{{-- Título --}}
					<div class="form-group row">
                    	<label for="name" class="col-12">{!! ucfirst(trans('textos.titulo')) !!}*</label>
						<div class="col-12">
							<x-input type="text" name="name" id="answerName" autocomplete="off" maxlength="255" required/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Tipo de valor: en función del tipo de respuesta de la cuestión --}}
						{{-- Booleana --}}
						@if($question->answer_type == config('constants.ANSWER_TYPE_BOOLEAN_'))
							<label for="is_true" class="col-4 col-lg-3 col-form-label" style="text-align: left;">{!! ucfirst(trans('textos.verdadero_falso')) !!}*</label>	
							<div class="col-8 col-lg-6">
								<label class="custom-chk-rdb pt-2">
					    			<input class="rdb-access" type="radio" name="is_true" id="answerTrue" value="1" required>
					    			<span class="rdb" style="top: 5px;"></span>
					    			{!! trans('textos.verdadero') !!}
								</label>	

								<label class="custom-chk-rdb pt-2 ml-5">
					    			<input class="rdb-access" type="radio" name="is_true" id="answerFalse" value="0" required>
					    			<span class="rdb" style="top: 5px;"></span>
					    			{!! trans('textos.falso') !!}
								</label>

								<x-input-errors name="is_true"/>
							</div>

						{{-- Por puntos --}}
						@else
							<label for="points" class="col-4 col-lg-1 col-form-label" style="text-align: left;">{!! ucfirst(trans('textos.puntos')) !!}*</label>
							<div class="col-8 col-lg-4">
								<x-input type="text" name="points" class="text-right" id="answerPoints" autocomplete="off" maxlength="8" required value="0"/>
							</div>	
						@endif

						{{-- Estado --}}
						<label for="status" class="col-4 col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-8 col-lg-1">
							<label class="custom-chk-rdb" style="margin-top: 5px;">
			    				<input type="checkbox" name="status" id="answerStatus" checked>
			    				<span class="chk"></span>
							</label>
						</div>	
					</div>

					{{-- Respuesta --}}
					<div class="form-group row">
                    	<label for="answer" class="col-12">{!! ucfirst(trans('textos.respuesta')) !!}*</label>
						<div class="col-12">
							<x-input-textarea name="answer" id="answerAnswer" class="editor"/>
						</div>
					</div>

					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Modal editar respuesta:
	$(document).on('click', '.btnEditAnswer', function(){
		var id = $(this).data('id');
		var name = $(this).data('name');
		var status = $(this).data('status');
		var points = $(this).data('points');
		var tf = $(this).data('tf');
		var answer = $(this).data('answer');

		$('#answerId').val(id);
		$('#answerName').val(name);
		$('#answerPoints').val(points);
		$('#answerAnswer').trumbowyg('html', answer);
		if(status == 1){
            $('#answerStatus').prop('checked',true);    
        }else{
            $('#answerStatus').prop('checked',false); 
        }
        if(tf == 1){
            $('#answerTrue').prop('checked',true);    
        }else if(tf == '0'){
            $('#answerFalse').prop('checked',true); 
        }
	});
});
</script>
@endpush

@endsection