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

{{-- Filtros --}}
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('seats.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])

						{{-- Cuenta contable --}}
						@php
						$ac = $accounting_account? $accounting_account->account.' - '.$accounting_account->name:'';
						@endphp
						<label for="accounting_account_" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_contable')) !!}</label>
						<div class="col-lg-6 position-relative">
							<x-input type="text" name="accounting_account_" id="search_accounting_account" placeholder="{!! ucfirst(trans('textos.cuenta_contable')) !!}" maxlength="150" value="{{ $ac }}"/>
						</div>
	
						<input type="hidden" name="accounting_account_id" id="accountingAccountId" value="@if($accounting_account) {{ $accounting_account->id }} @endif">
					</div>

					<div class="form-group row">
						{{-- Desde --}}
						<label for="from" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.desde')) !!}</label>
						<div class="col-lg-2">
							@php
							$from_ = $from != ''? \Carbon\Carbon::parse($from)->format($languages[session()->get('locale')][4]):\Carbon\Carbon::parse(date('Y').'-01-01')->format($languages[session()->get('locale')][4]);
							@endphp

							<x-date-picker name="from" value="{{ $from_ }}" id="accountFrom" :format="$languages[config('app.locale')][4]"/>	
						</div>

						{{-- Hasta --}}
						<label for="to" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hasta')) !!}</label>
						<div class="col-lg-2">
							@php
							$to_ = $to != ''? \Carbon\Carbon::parse($to)->format($languages[session()->get('locale')][4]):\Carbon\Carbon::parse(date('Y').'-12-31')->format($languages[session()->get('locale')][4]);
							@endphp

							<x-date-picker name="to" value="{{ $to_ }}" id="accountTo" :format="$languages[config('app.locale')][4]"/>	
						</div>	
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
{{-- / Filtro --}}

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
						<th class="text-center wdt50">#</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.any')) !!}</th>
						<th class="wdt150">{!! ucfirst(trans('textos.concepto')) !!}</th>
						<th class="wdt50">{!! ucfirst(trans('textos.empresa')) !!}</th>
						<th class="text-center wdt50">{!! ucfirst(trans('textos.documento')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>
			</table>
		@endif
	</div>
</div>

@endsection