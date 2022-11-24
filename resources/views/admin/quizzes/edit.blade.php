@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		<a href="{{ route('quizzes.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_concursos') !!}
		</a>

		@can('quizzes.create')
    		<a href="{{ route('quizzes.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.concurso_nuevo')) !!}
    		</a>
        @endcan

        @can('quizzes.edit')
    		<a href="{{ route('quiz-editions.create', $quiz) }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.edicion_nueva')) !!}
    		</a>
        @endcan

        @can('questions.create')
			<a class="btn btn-info" href="{{ route('questions.create', $quiz) }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.pregunta_nueva')) !!}</a>
		@endcan
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.concurso_editar')) !!} 
					<u>{{ $quiz->name }}</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Propietario --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.propietario')) !!}:
						<b>
							{{ $owner->name }} {{ $owner->surname }}
						</b>
					</label>

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}:
						<b>
							{{ $quiz->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
			  		<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-quiz-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'editions') active @endif">
			    		<a class="nav-link @if($tab == 'editions') active @endif" href="#tab-quiz-editions" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.ediciones')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'questions') active @endif">
			    		<a class="nav-link @if($tab == 'questions') active @endif" href="#tab-quiz-questions" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.preguntas')) !!}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-5">
			  		{{-- Info del concurso --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-quiz-info">
						@include('admin.quizzes.partials.quiz-info')	
					</div>	

					{{-- Ediciones --}}
					<div class="tab-pane fade @if($tab == 'editions') show active @endif" role="tabpanel" id="tab-quiz-editions">
						@include('admin.quizzes.partials.quiz-editions')	
					</div>

					{{-- Preguntas --}}
					<div class="tab-pane fade @if($tab == 'questions') show active @endif" role="tabpanel" id="tab-quiz-questions">
						@include('admin.quizzes.partials.quiz-questions')	
					</div>
			  	</div>
			</div>
		</div>	
	</div>
</div>

@endsection