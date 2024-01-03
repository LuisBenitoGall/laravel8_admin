@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('accounting-accounts.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_cuentas_contables') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<p class="text-warning">{{ trans('textos.cuentas_iva_texto') }}</p>
	</div>
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblIvaAccountsSales">	
			<thead>
				<tr>
					<th class="wdt100">{!! ucfirst(trans('textos.iva')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.ventas')) !!} ({!! ucfirst(trans('textos.repercutido')) !!})</th>
					<th class="text-center wdt50"></th>
					<th class="wdt150">{!! ucfirst(trans('textos.compras')) !!} ({!! ucfirst(trans('textos.soportado')) !!})</th>
					<th class="text-center wdt50"></th>
				</tr>
			</thead>

			<tbody>
				@foreach($iva_types as $it)
					<tr>
						{{-- IVA --}}
						<td>{{ $it->name }} - {{ $it->iva }}%</td>
						{{-- Cuenta ventas --}}
						<td>
							@php
							$iva_sales_account = '';
							$iva_sales_account_id = false; 
							@endphp
							@foreach($accounts as $a)
								@if($a->iva_type_id == $it->id)
									@php
									$iva_sales_account = $a->name.' - '.$a->account;
									$iva_sales_account_id = $a->id;
									@endphp
								@endif
							@endforeach
							{!! $iva_sales_account !!}
						</td>
						{{-- Gestionar cuenta ventas --}}
						<td class="text-center">
							@if($iva_sales_account == '')
								<a href="#" class="btn btn-info btn-xs btnAddAccount" data-concept="{{ trans('textos.ventas') }} {{ $it->iva }}%" data-prefix="4770/{{ $it->iva }}" data-ivatype="{{ $it->id }}" data-toggle="modal" data-target="#modal-add-account" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_agregar')) }}">
									<i class="fa fa-plus"></i>
								</a>
							@elseif($iva_sales_account_id)
								<a href="{{ route('accounting-accounts.delete-iva', $iva_sales_account_id) }}" class="btn btn-danger btn-xs btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_eliminar')) }}" data-msg="{{ trans('textos.cuenta_confirm_eliminar') }}">
									<i class="fa fa-trash"></i>
								</a>
							@endif	
						</td>
						{{-- Cuenta compras --}}
						<td></td>
						{{-- Gestionar cuenta compras --}}
						<td class="text-center">
							
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

{{-- Modals: --}}
{{-- Set account --}}
@include('admin.accounting_accounts.partials.modal-add-account')

@push('scripts')
<script>
$(function(){
	//Modal agregar cuenta:
	$('.btnAddAccount').on('click', function(e){
		e.preventDefault();
		var concept = $(this).data('concept');
		var prefix = $(this).data('prefix');
		var iva_type = $(this).data('ivatype');
		$('#addAccount').text(concept);
		$('#accountPrefix').val(prefix);
		$('#accountIvaType').val(iva_type);
	});
});
</script>
@endpush

@endsection