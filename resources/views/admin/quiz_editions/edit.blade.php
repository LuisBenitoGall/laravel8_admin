@extends('admin.partials.layout')

@section('content')

@php
//Durante el período activo del concurso, no se pueden modificar algunos datos:
$disabled = $edition->start && Carbon\Carbon::now()->format('Y-m-d H:i:s') >= $edition->start && Carbon\Carbon::now()->format('Y-m-d H:i:s') <= $edition->finish? 1:0;
@endphp

<input type="hidden" id="editionId" value="{{ $edition->id }}">
<input type="hidden" id="editionDisabled" value="{{ $disabled }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('quizzes.edit', [$quiz, 'editions']) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_concurso') !!} <b>{{ $quiz->name }}</b>
		</a>

		{{-- Jugar --}}
		<a target="_blank" href="{{ route('quiz-editions.show', $edition->id) }}" class="btn btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.jugar')) }}">
			<i class="fa fa-play"></i>
		</a>

		@can('quizzes.edit')
			{{-- Crear edición --}}
    		<a href="{{ route('quiz-editions.create', $quiz) }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.edicion_nueva')) !!}
    		</a>

    		{{-- Crear cuestión --}}
    		<a href="{{ route('questions.create', $edition) }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.cuestion_nueva')) !!}
    		</a>

    		{{-- Añadir empresas --}}
    		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-company">
    			<i class="fa fa-building"></i>
    			{!! ucfirst(trans('textos.empresas_add')) !!}
    		</a>

    		{{-- Añadir equipo --}}
    		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-team">
    			<i class="fa fa-users"></i>
    			{!! ucfirst(trans('textos.equipo_add')) !!}
    		</a>
        @endcan
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">
					{!! ucfirst(trans('textos.edicion')) !!} 
					<u>{{ $edition->name }}</u>
				</h3>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
			  		<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-edition-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'companies') active @endif">
			    		<a class="nav-link @if($tab == 'companies') active @endif" href="#tab-edition-companies" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.empresas_participantes')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'teams') active @endif">
			    		<a class="nav-link @if($tab == 'teams') active @endif" href="#tab-edition-teams" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.equipos_participantes')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'questions') active @endif">
			    		<a class="nav-link @if($tab == 'questions') active @endif" href="#tab-edition-questions" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.cuestiones')) !!}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-5">
			  		{{-- Info de la edición --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-edition-info">
						@include('admin.quiz_editions.partials.edition-info')
					</div>	

					{{-- Empresas participantes --}}
					<div class="tab-pane fade @if($tab == 'companies') show active @endif" role="tabpanel" id="tab-edition-companies">
						@include('admin.quiz_editions.partials.edition-companies')
					</div>

					{{-- Equipos participantes --}}
					<div class="tab-pane fade @if($tab == 'teams') show active @endif" role="tabpanel" id="tab-edition-teams">
						@include('admin.quiz_editions.partials.edition-teams')
					</div>

					{{-- Cuestiones --}}
					<div class="tab-pane fade @if($tab == 'questions') show active @endif" role="tabpanel" id="tab-edition-questions">
						@include('admin.quiz_editions.partials.edition-questions')
					</div>
			  	</div>
			</div>
		</div>	
	</div>
</div>

{{-- Modals --}}
{{-- Add company --}}
@include('admin.quiz_editions.partials.modal-add-company')

{{-- Add team --}}
@include('admin.quiz_editions.partials.modal-add-team')

@push('scripts')
<script>
$(function(){
	//Marcar / desmarcar pregunta en edición.
	$(document).on('click', '.chkSetQuestion', function(){
		var elem = $(this);
		var edition_id = $('#editionId').val();
		var question_id = $(this).data('id');

		$.get("/admin/quiz-edition-questions/"+edition_id+"/store/"+question_id, function(response, state){
            if(response){ 
            	$('.check-ko').remove();
                elem.parent('label').before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            } else {
                elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
			}
		});
	});

	//Marcar / desmarcar todas las preguntas.
	$('.btnSetEditionQuestions').on('click', function(){
		var edition_id = $('#editionId').val();
		var status = $(this).is(':checked')? 1:0;

		window.location.href = '/admin/quiz-edition-questions/'+edition_id+'/all-questions/'+status;
	});

	//Setear puntos de la pregunta:
	$(document).on('change', '.setPoints', function(){
		var points = $(this).val();
		var q = $(this).data('id');

		$.get("/admin/quiz-edition-questions/"+q+"/set-points/"+points, function(response, state){
            if(response){ 

            }
		});
	});

	//Setear puntos para todas las preguntas:
	$('.setPointsAll').on('change', function(){
		var points = $(this).val();
		var edition_id = $('#editionId').val();

		$.get("/admin/quiz-edition-questions/"+edition_id+"/set-points-all/"+points, function(response, state){
            if(response){ 
            	window.location.href = '/admin/quiz-editions/'+edition_id+'/edit/questions';
            }
		});	
	});
});
</script>
@endpush

@endsection