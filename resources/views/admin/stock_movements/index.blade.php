@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
        {{-- Nuevo movimiento --}}
		@can('stock-movements.create')
			<a class="btn btn-info" href="{{ route('stock-movements.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.movimiento_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<input type="hidden" id="dtController" value="stock_movements">

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblStockMovements">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.movimiento')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.siglas')) !!}</th>
					<th>{!! ucfirst(trans('textos.signo')) !!}</th>
					<th>{!! ucfirst(trans('textos.explicacion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection