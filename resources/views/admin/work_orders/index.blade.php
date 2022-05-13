@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="work-orders">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Crear orden de trabajo --}}
		@can('work-orders.create')
			<a href="{{ route('work-orders.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! trans('textos.trabajo_orden_nueva') !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblWorkOrders">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.trabajo_orden')) !!}</th>
					<th>{!! ucfirst(trans('textos.titulo')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection