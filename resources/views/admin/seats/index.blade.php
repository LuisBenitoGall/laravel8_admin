@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="seats">
<input type="hidden" id="seatsYear" value="{{ $year }}">
<input type="hidden" id="seatsAccountingAccount" value="@if($accounting_account){{ $accounting_account->id }}@endif">
<input type="hidden" id="seatsFrom" value="{{ $from }}">
<input type="hidden" id="seatsTo" value="{{ $to }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Ver todos los asientos --}}
		@if($accounting_account)
			<a href="{{ route('seats.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_asientos_contables') !!}
			</a>
		@endif

		{{-- Nuevo asiento  --}}
		@can('seats.create')
			<a class="btn btn-info" href="{{ route('seats.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.asiento_nuevo')) !!}</a>
		@endcan	

		{{-- Modelos --}}
		<a class="btn btn-info" href="{{ route('seat-models.index') }}"><i class="fa fa-list"></i> {!! ucfirst(trans('textos.modelos')) !!}</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			@if($accounting_account)
				{!! trans('textos.extracto') !!}	
				<b>{{ $accounting_account->account }} - {{ $accounting_account->name }}</b>
			@else
				{!! trans('textos.asientos_contables') !!} 
				<b>{{ $company->name }}</b>
			@endif 
		</h3>
	</div>

	<div class="table-responsive">
		{{-- Tabla extracto de líneas --}}
		@if($accounting_account)
			<table class="table table-hover dtabla" id="tblSeatLines">
				<thead>
					<tr>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.asiento')) !!}</th>
						<th class="wdt50">{!! ucfirst(trans('textos.descripcion')) !!}</th>
						<th class="wdt50">{!! ucfirst(trans('textos.documento')) !!}</th>
						<th class="wdt50 text-center">{!! ucfirst(trans('textos.debe')) !!}</th>
						<th class="wdt50 text-center">{!! ucfirst(trans('textos.haber')) !!}</th>
						<th class="wdt50 text-center">{!! ucfirst(trans('textos.saldo')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>

		{{-- Tabla asientos --}}
		@else
			<table class="table table-hover dtabla" id="tblSeats">
				<thead>
					<tr>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.any')) !!}</th>
						<th class="wdt150">{!! ucfirst(trans('textos.descripcion')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
						<th class="wdt50">{!! ucfirst(trans('textos.empresa')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.proveedor_documento')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		@endif
	</div>
</div>

@endsection