@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a modelos --}}
		<a href="{{ route('seat-models.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_modelos') !!}
		</a>
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('seat-models.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="currency_id" value="{{ $currency->id }}">

					<div class="form-group row">
						{{-- Nombre --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" maxlength="255" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" autocomplete="off" required/>
						</div>	
					
						{{-- Diario --}}
						<label for="diary" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.diario')) !!}</label>
						<div class="col-lg-2">
							<x-select name="diary" class="select2" label="diario_selecciona" :options="$diaries"/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Descripción --}}
						<label for="description" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
						<div class="col-md-10">
							<x-input-textarea name="description"/>	
						</div>
					</div>
					<br>

					{{-- Añadir líneas de asientos contables --}}
					@include('admin.seats.partials.component-lines', ['title' => trans('textos.asiento_contable_modelo_lineas')])	

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

					<input type="hidden" id="debeTotal" value="@if(isset($debe_total)) {{ $debe_total }} @else 0 @endif">
					<input type="hidden" id="haberTotal" value="@if(isset($haber_total)) {{ $haber_total }} @else 0 @endif">

					<x-btn-submit name="submit_save" id="sbtSeat" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection