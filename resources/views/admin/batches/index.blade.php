@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		@can('batches.create')
			<a class="btn btn-info" href="{{ route('batches.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.lote_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBatches">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.lote')) !!}</th>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_caducidad')) !!}</th>
					<th>{!! ucfirst(trans('textos.produccion_orden')) !!}</th>
					<th>{!! ucfirst(trans('textos.cantidad_inicial')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection