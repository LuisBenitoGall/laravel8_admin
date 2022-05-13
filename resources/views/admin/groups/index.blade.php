@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('groups.create')
			<a class="btn btn-info" href="{{ route('groups.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.grupo_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblGroups">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.grupo')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection