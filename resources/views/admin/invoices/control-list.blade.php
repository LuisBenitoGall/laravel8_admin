@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="orders">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado facturas --}}
		@can('invoices.index')
			<a href="{{ route('invoices.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_facturas') !!}
			</a>
		@endcan
	</div>
</div>

{{-- Tabla --}}
<div class="row">
	<div class="col-12">
		<h4 class="text-warning mb-3">Relación de pedidos con facturación pendiente.</h4>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblInvoiceControl">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="wdt150">{!! ucfirst(trans('textos.cliente')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.responsable')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.pendiente')) !!}</th>
					<th class="wdt100">{!! trans('textos.estado') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection