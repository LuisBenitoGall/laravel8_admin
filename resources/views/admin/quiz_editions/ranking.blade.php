@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		{{-- Jugar --}}
		<a href="{{ route('quiz-editions.show', $edition) }}" class="btn btn-info">
			<i class="fa fa-play"></i>
			{!! trans('textos.jugar') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">


			</div>
		</div>
	</div>
</div>

@endsection