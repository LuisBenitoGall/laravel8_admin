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
		<p class="text-warning">{!! trans('textos.cuentas_iva_texto') !!}</p>
	</div>
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblIvaAccountsSales">	
			<thead>
				<tr>
					<th class="wdt100 border-right3">{!! ucfirst(trans('textos.iva')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.ventas')) !!} ({!! ucfirst(trans('textos.repercutido')) !!})</th>
					<th class="wdt100">{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th class="text-center wdt50 border-right3">{!! ucfirst(trans('textos.acciones')) !!}</th>

					<th class="wdt100">{!! ucfirst(trans('textos.compras')) !!} ({!! ucfirst(trans('textos.soportado')) !!})</th>
					<th class="wdt100">{!! ucfirst(trans('textos.tipo')) !!}</th>
					<th class="text-center wdt50">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($iva_types as $it)
					<tr>
						{{-- IVA --}}
						<td class="border-right3">{{ $it->name }} - {{ $it->iva }}%</td>
						{{-- Cuenta ventas --}}
						<td>
							@php
							$iva_sales_account = '';
							$iva_sales_account_id = false; 
							$iva_sales_type = false;
							@endphp
							@foreach($accounts as $a)
								@if($a->iva_type_id == $it->id && strpos($a->account, config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_SALES_')) === 0)
									@php
									$iva_sales_account = $a->name.' - '.$a->account;
									$iva_sales_account_id = $a->id;
									$iva_sales_type = $a->type;
									@endphp
								@endif
							@endforeach
							{!! $iva_sales_account !!}
						</td>
						{{-- Tipo --}}
						<td>
							@if($iva_sales_type) {!! $tipos[$iva_sales_type] !!} @endif
						</td>
						{{-- Acciones cuenta ventas --}}
						<td class="text-right border-right3">
							@if($iva_sales_account == '')
								{{-- Crear cuenta --}}
								<a href="#" class="btn btn-info btn-xs btnAddAccount" data-concept="{{ trans('textos.ventas') }} {{ $it->iva }}%" data-prefix="{{ config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_SALES_') }}/{{ $it->iva }}" data-ivatype="{{ $it->id }}" data-toggle="modal" data-target="#modal-add-account" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_agregar')) }}">
									<i class="fa fa-plus"></i>
								</a>

								{{-- Seleccionar cuenta --}}
								<a href="#" class="btn btn-info btn-xs btnSelectAccount" data-concept="{{ trans('textos.ventas') }} {{ $it->iva }}%" data-prefix="{{ config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_SALES_') }}/{{ $it->iva }}" data-ivatype="{{ $it->id }}" data-toggle="modal" data-target="#modal-select-account" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_selec')) }}">
									<i class="fa fa-filter"></i>
								</a>

							@elseif($iva_sales_account_id)
								{{-- Editar --}}
								<a target="_blank" href="{{ route('accounting-accounts.edit', $iva_sales_account_id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

								{{-- Eliminar --}}
								<a href="{{ route('accounting-accounts.delete-iva', $iva_sales_account_id) }}" class="btn btn-danger btn-xs btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_eliminar')) }}" data-msg="{{ trans('textos.cuenta_confirm_eliminar') }}">
									<i class="fa fa-trash"></i>
								</a>
							@endif	
						</td>

						{{-- Cuenta compras --}}
						<td>
							@php
							$iva_purchases_account = '';
							$iva_purchases_account_id = false; 
							$iva_purchases_type = false;
							@endphp
							@foreach($accounts as $a_)
								@if($a_->iva_type_id == $it->id && strpos($a_->account, config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_PURCHASES_')) === 0)
									@php
									$iva_purchases_account = $a_->name.' - '.$a_->account;
									$iva_purchases_account_id = $a_->id;
									$iva_purchases_type = $a_->type;
									@endphp
								@endif
							@endforeach
							{!! $iva_purchases_account !!}
						</td>
						{{-- Tipo --}}
						<td>
							@if($iva_purchases_type) {!! $tipos[$iva_purchases_type] !!} @endif
						</td>
						{{-- Gestionar cuenta compras --}}
						<td class="text-right">
							@if($iva_purchases_account == '')
								{{-- Crear cuenta --}}
								<a href="#" class="btn btn-info btn-xs btnAddAccount" data-concept="{{ trans('textos.compras') }} {{ $it->iva }}%" data-prefix="{{ config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_PURCHASES_') }}/{{ $it->iva }}" data-ivatype="{{ $it->id }}" data-toggle="modal" data-target="#modal-add-account" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_agregar')) }}">
									<i class="fa fa-plus"></i>
								</a>

								{{-- Seleccionar cuenta --}}
								<a href="#" class="btn btn-info btn-xs btnSelectAccount" data-concept="{{ trans('textos.compras') }} {{ $it->iva }}%" data-prefix="{{ config('constants.ACCOUNTING_ACCOUNT_IVA_PREFIX_PURCHASES_') }}/{{ $it->iva }}" data-ivatype="{{ $it->id }}" data-toggle="modal" data-target="#modal-select-account" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_selec')) }}">
									<i class="fa fa-filter"></i>
								</a>

							@elseif($iva_purchases_account_id)
								{{-- Editar --}}
								<a target="_blank" href="{{ route('accounting-accounts.edit', $iva_purchases_account_id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

								{{-- Eliminar --}}
								<a href="{{ route('accounting-accounts.delete-iva', $iva_purchases_account_id) }}" class="btn btn-danger btn-xs btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_eliminar')) }}" data-msg="{{ trans('textos.cuenta_confirm_eliminar') }}">
									<i class="fa fa-trash"></i>
								</a>
							@endif		
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

{{-- Select account --}}
@include('admin.accounting_accounts.partials.modal-select-account')

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

	//Modal seleccionar cuenta:
	$('.btnSelectAccount').on('click', function(e){
		e.preventDefault();
		var concept = $(this).data('concept');
		var prefix = $(this).data('prefix');
		var iva_type = $(this).data('ivatype');
		$('#selectAccount').text(concept);
		$('#accountPrefix_').val(prefix);
		$('#accountIvaType_').val(iva_type);
	});
});
</script>
@endpush

@endsection