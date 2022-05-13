@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('vehicles.create')
			<a class="btn btn-info" href="{{ route('vehicles.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.vehiculo_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblVehicles">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.vehiculo')) !!}</th>
					<th>{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th>{!! ucfirst(trans('textos.matricula')) !!}</th>
					<th>{!! ucfirst(trans('textos.marca')) !!}</th>
					<th>{!! ucfirst(trans('textos.modelo')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection