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

		{{-- Modelos --}}
		@if($models->count())
			<select name="model" class="form-control" id="selectModel" style="display: inline-block !important; width: 250px;">
				<option value="">{{ trans('textos.modelo_selecciona') }}</option>
				<option value="x">{{ trans('textos.modelo_sin') }}</option>
				@foreach($models as $m)
					@php
					$selected = $model && $model->id == $m->id? 'selected':'';
					@endphp  
					<option value="{{ $m->id }}" {{ $selected }}>{{ $m->name }}</option>
				@endforeach
			</select>
		@endif
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

						{{-- Concepto --}}
						<label for="concept" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
						<div class="col-lg-6">
							<x-input type="text" name="concept" maxlength="255" placeholder="{!! ucfirst(trans('textos.concepto')) !!}" autocomplete="off" required/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Empresa --}}
						<label for="company_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="company_id" id="search_company" placeholder="{!! ucfirst(trans('textos.empresa')) !!}" maxlength="150"/>
						</div>

						<input type="hidden" name="company_id" id="companyId">

						{{-- Diario --}}
						<label for="diary" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.diario')) !!}</label>
						<div class="col-lg-2">
							<x-select name="diary" class="select2" label="diario_selecciona" :options="$diaries"/>
						</div>	
					</div>

					<div class="form-group row">
						{{-- Nº documento --}}
						<label for="doc_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento_num')) !!}</label>
						<div class="col-lg-4 position-relative">
							<x-input type="text" name="doc_num" placeholder="{!! ucfirst(trans('textos.documento')) !!}" maxlength="150"/>
						</div>	

						{{-- Documento --}}
						<label for="doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
						<div class="col-lg-4">
							<x-input-file name="doc"/>
						</div>

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

					<div class="form-group row">
						{{-- Descripción --}}
	                    <label for="description" class="col-3 col-md-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
	                    <div class="col-9 col-md-10">
	                        <textarea name="description" class="form-control"></textarea>
	                    </div>
	                </div>
					<br>

					{{-- Añadir líneas de asientos contables --}}
					@include('admin.seats.partials.component-lines', ['title' => trans('textos.asiento_contable_lineas')])	

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

@push('scripts')
<script>
$(function(){
	//Selector de modelo:
	$('#selectModel').on('change', function(e){
		var model = e.target.value;
		if(model == 'x'){
        	window.location.href = '/admin/seats/create';
		}else{
			window.location.href = '/admin/seats/create/' + model;	
		}
	});
});
</script>
@endpush

@endsection