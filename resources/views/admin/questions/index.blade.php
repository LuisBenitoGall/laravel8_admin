@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('questions.create')
			<a class="btn btn-info" href="{{ route('questions.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.cuestion_nueva')) !!}</a>
		@endcan
	</div>
</div>


@endsection