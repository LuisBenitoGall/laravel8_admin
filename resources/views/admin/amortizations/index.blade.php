@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('amortizations.create')
			<a class="btn btn-info" href="{{ route('amortizations.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.amortizacion_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			{!! trans('textos.amortizaciones') !!} 
			<b>{{ $company->name }}</b> 
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblAmortizations">
			<thead>
				<tr>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.codigo')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.any')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.importe_pendiente')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.porcentaje')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>

@endsection