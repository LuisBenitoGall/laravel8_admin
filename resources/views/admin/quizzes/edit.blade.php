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
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">
					{!! ucfirst(trans('textos.concurso_editar')) !!} 
					<u>{{ $quiz->name }}</u>
				</h3>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
			  		<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-quiz-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'editions') active @endif">
			    		<a class="nav-link @if($tab == 'editions') active @endif" href="#tab-quiz-editions" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.ediciones')) !!}</a>
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
			  	</div>
			</div>
		</div>	
	</div>
</div>

@endsection