@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('quizzes.create')
    		<a href="{{ route('quizzes.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.concurso_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

{{-- Vista listado --}}
<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblQuizzes">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.concurso')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection