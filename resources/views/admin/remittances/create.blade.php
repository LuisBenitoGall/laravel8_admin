@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('remittances.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_remesas') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 col-sm-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('remittances.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<p class="text-warning">{{ trans('textos.efectos_selec_aviso') }}</p>

					<div class="form-group row">
						{{-- Cuenta bancaria --}}
						<label for="bank_account" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}*</label>
						<div class="col-lg-4">
							<x-select name="bank_account" class="select2" :options="$bank_accounts" label="cuenta_selec" required/>
						</div>

						{{-- Patrones --}}
						@if(count($patterns) == 1)
							<input type="hidden" name="pattern_id" value="{{ key($patterns) }}">

						@elseif(count($patterns) > 1)
							<label for="pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.patron')) !!}</label>
							<div class="col-lg-4">
								<x-select name="pattern_id" class="select2" :options="patterns" label="patron_selec"/>
							</div>	
						@endif
					</div>	 

					<div id="effects">
						{{-- Se almacenan los effects que se vayan seleccionando --}}
					</div>
		
					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/> 				
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row mb-3">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblAddEffectsToRemittance">
			<thead>
				<th class="wdt50">{!! ucfirst(trans('textos.cliente')) !!}</th>
				<th class="wdt150">{!! ucfirst(trans('textos.factura')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.fecha')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.importe')) !!}</th>
				<th class="text-center wdt50">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}</th>
				<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
			</thead>

			<tbody>
				@foreach($effects as $ef)
					<tr>
						{{-- Cliente --}}
						<td>
							{{ $ef->customer }}
						</td>
						{{-- Factura --}}
						<td class="text-right">
							@if($ef->invoice_id)
								@php
								$num = $ef->invoice_num? $ef->invoice_num:($ef->invoice_old_num? $ef->invoice_old_num:$ef->invoice_id);
								@endphp
								<a target="_blank" href="{{ route('invoices.edit', $ef->invoice_id) }}">{{ $num }}</a>
							@endif
						</td>
						{{-- Fecha --}}
						<td class="text-right">
							{!! Carbon\Carbon::parse($ef->date)->format($languages[session()->get('locale')][4]); !!}
						</td>
						{{-- Importe --}}
						<td class="text-right">
							{{ $ef->amount }}{{ $ef->symbol }}
						</td>
						{{-- Cuenta bancaria --}}
						<td class="text-right">
							{{ $ef->entity }} {{ $ef->iban }}-{{ $ef->office }}-{{ $ef->dc }}-{{ $ef->account }}	
						</td>
						{{-- Acciones --}}
						<td class="text-right">
							<label class="custom-chk-rdb" rel="tooltip" title="{{ trans('textos.efecto_on_off') }}">
						<input type="checkbox" name="effect" value="{{ $ef->id }}" class="chkEffect"><span class="chk"></span>
						</label>
						</td>
					</tr>
				@endforeach	
			</tbody>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Seleccionar efectos.
	$('.chkEffect').on('click', function(){
		var elem = $(this);
		var val = $(this).val();
		if (elem.siblings('input').is(':checked')) {
			var html = '<input type="hidden" class="ef-'+val+'" name="effects[]" value="'+val+'">';	
			('#effects').append(html);
		}else{
			$('.ef-'+val).remove();
		}		
	});
});
</script>
@endpush

@endsection