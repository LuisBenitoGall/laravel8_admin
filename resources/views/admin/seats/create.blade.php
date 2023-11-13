@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a asientos contables --}}
		<a href="{{ route('seats.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_asientos_contables') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('seats.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Fecha asiento --}}
						<label for="seat_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha')) !!}*</label>
						<div class="col-lg-2">
							<x-date-picker name="seat_date" :format="$languages[config('app.locale')][4]" required value="{{ date('d/m/Y') }}"/>
						</div>	

						{{-- Descripción --}}
						<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="description" maxlength="255" placeholder="{!! ucfirst(trans('textos.descripcion')) !!}" autocomplete="off" required/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Empresa --}}
						<label for="company_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="company_id" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150" value="{{ $company->name }}"/>
						</div>

						<input type="hidden" name="company_id" id="companyId" value="{{ $company->id }}">

						{{-- Diario --}}
						<label for="diary" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.diario')) !!}</label>
						<div class="col-lg-2">
							<x-select name="diary" class="select2" label="diario_selecciona" :options="$diaries" value="DG"/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Documento --}}
						<label for="doc_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="doc_num" placeholder="{!! ucfirst(trans('textos.documento')) !!}" maxlength="150"/>
						</div>	
					
						{{-- Concepto --}}
						{{-- <label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}</label>
						<div class="col-lg-2">
							<x-select name="concept" class="select2" label="concepto_selec" :options="$concepts"/>
						</div> --}}	

						{{-- Serie --}}
						{{-- <label for="serie" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.serie')) !!}</label>
						<div class="col-lg-2">
							@php
							$series_ = [];
							foreach($series as $s){
								$series_[$s->code] = $s->code;
							}
							@endphp

							<x-select name="serie" class="select2" label="serie_selec" :options="$series_"/>
						</div>	 --}}
					</div>
					<br>

					{{-- <div class="form-group row"> --}}
						{{-- Fecha factura --}}
						{{-- <label for="invoice_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_fecha')) !!}</label>
						<div class="col-lg-2">
							<x-date-picker name="invoice_date" :format="$languages[config('app.locale')][4]"/>
						</div> --}}

						{{-- Nº de factura --}}
						{{-- <label for="invoice_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_num')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="invoice_num" maxlength="50" placeholder="{!! ucfirst(trans('textos.factura_num')) !!}" autocomplete="off"/>
						</div> --}}

						{{-- Documento proveedor --}}
						{{-- <label for="provider_doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proveedor_documento')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="provider_doc" maxlength="150" placeholder="{!! ucfirst(trans('textos.proveedor_documento')) !!}" autocomplete="off"/>
						</div>
					</div> --}}

					{{-- <div class="form-group row"> --}}
						{{-- Base 1 --}}
						{{-- <label for="base1" class="col-lg-2 col-form-label">Base 1</label>
						<div class="col-lg-2">
							<x-input type="text" name="base1" class="text-right set-decimals calc_amount" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}"/>
						</div> --}}

						{{-- IVA 1 --}}
						{{-- <label for="iva_percent1" class="col-lg-2 col-form-label">IVA 1</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva_percent1" class="text-right set-decimals calc_iva" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
						</div> --}}

						{{-- Importe + iva 1 --}}
						{{-- <label for="iva_amount1" class="col-lg-2 col-form-label">{{ trans('textos.subtotal') }} 1</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva_amount1" class="text-right set-decimals amount-here" autocomplete="off" maxlength="5" group="right" groupvalue="{{ $currency->symbol }}" readonly/>
						</div>
					</div> --}}

					{{-- <div class="form-group row"> --}}
						{{-- Base 2 --}}
						{{-- <label for="base2" class="col-lg-2 col-form-label">Base 2</label>
						<div class="col-lg-2">
							<x-input type="text" name="base2" class="text-right set-decimals calc_amount" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}"/>
						</div> --}}

						{{-- IVA 2 --}}
						{{-- <label for="iva_percent2" class="col-lg-2 col-form-label">IVA 2</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva_percent2" class="text-right set-decimals calc_iva" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
						</div> --}}

						{{-- Importe + iva 2 --}}
						{{-- <label for="iva_amount2" class="col-lg-2 col-form-label">{{ trans('textos.subtotal') }} 2</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva_amount2" class="text-right set-decimals amount-here" autocomplete="off" maxlength="5" group="right" groupvalue="{{ $currency->symbol }}" readonly/>
						</div>
					</div> --}}

					{{-- <div class="form-group row"> --}}
						{{-- Base 3 --}}
						{{-- <label for="base3" class="col-lg-2 col-form-label">Base 3</label>
						<div class="col-lg-2">
							<x-input type="text" name="base3" class="text-right set-decimals calc_amount" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}"/>
						</div> --}}

						{{-- IVA 3 --}}
						{{-- <label for="iva_percent3" class="col-lg-2 col-form-label">IVA 3</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva_percent3" class="text-right set-decimals calc_iva" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
						</div> --}}

						{{-- Importe + iva 3 --}}
						{{-- <label for="iva_amount3" class="col-lg-2 col-form-label">{{ trans('textos.subtotal') }} 3</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva_amount3" class="text-right set-decimals amount-here" autocomplete="off" maxlength="5" group="right" groupvalue="{{ $currency->symbol }}" readonly/>
						</div>
					</div> --}}

					{{-- Añadir líneas de asientos contables --}}
					@include('admin.seats.partials.component-lines')	

					<div class="form-group row mt-4 line-titles">
						<div class="col-1 text-right"><b>#</b></div>
						<div class="col-md-4 text-center"><b>{{ trans('textos.cuenta_contable') }}</b></div>
						<div class="col-md-1 text-center"><b>{{ ucfirst(trans('textos.tipo')) }}</b></div>
						<div class="col-md-2 text-center"><b>{{ trans('textos.debe') }}</b></div>
						<div class="col-md-2 text-center"><b>{{ trans('textos.haber') }}</b></div>
					</div>				

					<div id="seatLines">
					</div>

					<div class="form-group row" id="seatLinesTotals">
						<div class="col-6">&nbsp;</div>	
						<div class="col-md-2 text-right font-weight-bold pr-4" id="totalDebe"></div>
						<div class="col-md-2 text-right font-weight-bold pr-4" id="totalHaber"></div>
						<div class="col-md-2 font-weight-bold pr-4" id="totalDiff"></div>
					</div>

					<x-btn-submit name="submit_save" id="sbtSeat" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection