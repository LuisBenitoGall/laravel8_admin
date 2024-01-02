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
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblIvaAccountsSales">	
			<thead>
				<tr>
					<th class="wdt50">{!! ucfirst(trans('textos.iva')) !!}</th>
					<th class="wdt50">{!! ucfirst(trans('textos.ventas')) !!}</th>
					<th class="text-center wdt100"></th>
					<th class="wdt150">{!! ucfirst(trans('textos.compras')) !!}</th>
					<th class="text-center wdt100"></th>
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
							@endphp
							@foreach($accounts as $a)
								@if($a->iva_type_id == $it->id)
									@php
									$iva_sales_account = $a->name.' - '.$a->account;
									@endphp
								@endif
							@endforeach
							{!! $iva_sales_account !!}
						</td>
						{{-- Gestionar cuenta ventas --}}
						<td class="text-right">
							@if($iva_sales_account == '')
								<a href="#" class="btn btn-info btn-xs btnAddAccount" data-concept="{{ trans('textos.ventas') }} {{ $it->iva }}%" data-toggle="modal" data-target="#modal-add-account" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_agregar')) }}">
									<i class="fa fa-plus"></i>
								</a>
							@else
								<a href="" class="btn btn-danger btn-xs btn-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.cuenta_eliminar')) }}" data-msg="confirmaaaaaa">
									<i class="fa fa-trash"></i>
								</a>
							@endif	
						</td>
						{{-- Cuenta compras --}}
						<td></td>
						{{-- Gestionar cuenta compras --}}
						<td class="text-right">
							
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
		$('#addAccount').text(concept);
	});
});
</script>
@endpush

@endsection