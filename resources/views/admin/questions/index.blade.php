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

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblQuestions">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.titulo')) !!}</th>
					<th>{!! ucfirst(trans('textos.cuestion')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th class="text-center wdt200">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection