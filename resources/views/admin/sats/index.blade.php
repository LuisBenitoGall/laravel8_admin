@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="sat">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo servicio --}}
		@can('sat.create')
			<a href="{{ route('sat.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.sat_nuevo')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblSats">
			<thead>
				<tr>
					{{-- Num --}}
					<th class="text-center wdt50">#</th>
					{{-- Contratante --}}
					<th class="text-center wdt150">{!! ucfirst(trans('textos.contratante')) !!}</th>
					{{-- Proveedor --}}
					<th class="text-center wdt150">{!! ucfirst(trans('textos.proveedor')) !!}</th>
					{{-- Fecha solicitado --}}
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_inicio')) !!}</th>
					{{-- Fecha entrega --}}
					<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					{{-- Importe --}}
					<th class="text-center wdt100 text-right">{!! ucfirst(trans('textos.importe')) !!}</th>
					{{-- Status --}}
					<th class="wdt50">{!! trans('textos.estado') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection
