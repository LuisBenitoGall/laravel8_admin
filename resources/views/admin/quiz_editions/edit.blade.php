@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="editionId" value="{{ $edition->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('quizzes.edit', [$quiz, 'editions']) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_concurso') !!} <b>{{ $quiz->name }}</b>
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
{{-- Añadir equipo --}}
<div class="modal fade modal-add-team" id="modal-add-team" tabindex="-1" role="dialog" aria-labelledby="addTeam" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="addTeam">{{ trans('textos.equipo_add') }} <u></u></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<form method="post" autocomplete="off" action="{{ route('quiz-edition-teams.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}	
					<input type="hidden" name="quiz_edition_id" value="{{ $edition->id }}">

					<div class="form-group row">
						<label class="col-12">{{ ucfirst(trans('textos.equipos')) }} *
							</label>
						<div class="col-12">
							@php
							$options = [];
							@endphp

							@foreach($teams as $team)
								@php
								$options[$team->id] = $team->name;
								@endphp
							@endforeach

							<x-select name="teams[]" class="select2" multiple="true" :options="$options" required info="quiz-edition-teams"/>
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
@endpush

@endsection