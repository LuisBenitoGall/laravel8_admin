@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones --}}
		@can('units.create')
			<a class="btn btn-info" href="{{ route('units.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.unidad_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblUnits">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.unidad_venta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.simbolo')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection