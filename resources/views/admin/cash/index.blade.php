@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="cash">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Caja --}}
		<a href="{{ route('cash.create') }}" class="btn btn-info">
			<i class="fa fa-cash-register"></i>
			{!! ucfirst(trans('textos.caja')) !!}
		</a>
	</div>
</div>

{{-- Filtros --}}
{{-- <div class="row">
	<div class="col-12 mb-3">
		POR DESARROLLAR
	</div>
</div> --}}

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblCashes">
			<thead>
				<tr>
					<th class="text-center">{{ ucfirst(trans('textos.fecha')) }}</th>
					<th>{{ ucfirst(trans('textos.caja')) }}</th>
					<th>{{ ucfirst(trans('textos.cliente')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
					<th>{{ ucfirst(trans('textos.metodo_pago')) }}</th>
					<th class="wdt50">{{ ucfirst(trans('textos.estado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@endsection