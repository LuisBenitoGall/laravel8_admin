@extends('admin.partials.layout')

@section('content')

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
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblInvoiceControl">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="wdt150">{!! ucfirst(trans('textos.cliente')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.responsable')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}</th>
					<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
					<th class="wdt100">{!! trans('textos.mes') !!}</th>
					<th class="wdt100">{!! trans('textos.estado') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection