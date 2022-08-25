@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('attributes.create')
			<a class="btn btn-info" href="{{ route('attributes.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.atributo_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAttributes">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.atributo')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th>{!! ucfirst(trans('textos.tipo_dato')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection