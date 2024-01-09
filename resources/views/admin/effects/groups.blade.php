@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myCustomer" value="{{ $customer_id }}">
<input type="hidden" id="myProvider" value="{{ $provider_id }}">
<input type="hidden" id="myFrom" value="{{ $from }}">
<input type="hidden" id="myTo" value="{{ $to }}">
<input type="hidden" id="myPaymentMethod" value="{{ $payment_method }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('effects.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_efectos') !!}
		</a>

		@can('effects.create')
			{{-- Nuevo efecto --}}
			<a href="{{ route('effects.create') }}" class="btn btn-primary">
				<i class="fa fa-plus"></i>
				{{ trans('textos.efecto_nuevo') }}
			</a>
		@endcan

		@can('effects.edit')
			{{-- Agrupar efectos --}}
			<a href="#" class="btn btn-primary disabled btnSetGroup" data-toggle="modal" data-target="#modal-set-group">
				<i class="fa fa-object-group"></i>
				{{ trans('textos.efectos_agrupar') }}
			</a>
		@endcan
	</div>
</div>

{{-- Formulario filtro --}}
@include('admin.effects.partials.form-filter-groups')

<div class="row">
	@if($customer_id || $provider_id)
		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblEffectsGroups">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">
							{{ trans('textos.fecha_vencimiento') }}
						</th>
						<th>
							@php
							$company_label = $customer_id == session('currentCompany')? 'proveedor':'cliente';
							@endphp

							{{ ucfirst(trans('textos.'.$company_label)) }}
						</th>
						<th>
							{{ trans('textos.facturas') }}
						</th>
						{{-- <th>
						@lang('crud.effects.inputs.date_invoice')
						</th> --}}
						<th class="text-center">
							{{ ucfirst(trans('textos.importe')) }}
						</th>
						<th class="text-center">
							{{ ucfirst(trans('textos.pago_metodo')) }}
						</th>
						<th>
							{{ ucfirst(trans('textos.observaciones')) }}
						</th>
						<th>
							{{ ucfirst(trans('textos.estado')) }}
						</th>
						<th class="text-center">
							{{ ucfirst(trans('textos.acciones')) }}
						</th>
					</tr>
				</thead>
			</table>
		</div>
	@else
		<div class="col-12 text-center py-5">
			<h2>{{ trans('textos.filtra_agrupaciones') }}</h2>
		</div>
	@endif
</div>

{{-- Modals --}}
{{-- Set effect group --}}
@include('admin.effects.partials.modal-set-group')

@push('scripts')
<script>
$(function(){
	//Selector clientes y proveedores.
	$('#customer-selector, #provider-selector').on('change', function(){
		var id = $(this).val();
		var role_ = $(this).attr('name');

		//Limpiamos el concepto no seleccionado:
		if(role_ == 'customer_id'){
			$('#provider-selector').val('').trigger('change.select2');
			$('#accountOf').text('cliente');
		}else{
			$('#customer-selector').val('').trigger('change.select2');
			$('#accountOf').text('proveedor');
		}
	});

	//Selección de efectos:
	$(document).on('change', '.chkEffectGroup', function(){
		var id = $(this).data('id');
		$('.btnSetGroup').removeClass('disabled');
		if($(this).is(':checked')) {
			$('#frmEffectsGroups').append('<input type="hidden" name="effects[]" value="'+id+'" class="effect_'+id+'">');
		}else{
			$("#frmEffectsGroups .effect_"+id).remove();	
		}
	});
});
</script>
@endpush

@endsection