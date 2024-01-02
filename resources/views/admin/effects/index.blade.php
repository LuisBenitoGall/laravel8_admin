@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="effects">
<input type="hidden" id="myCustomer" value="@if($customer){{ $customer->id }}@endif">
<input type="hidden" id="myProvider" value="@if($provider){{ $provider->id }}@endif">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('effects.create')
			{{-- Nuevo efecto --}}
			<a href="{{ route('effects.create') }}" class="btn btn-primary">
				<i class="fa fa-plus"></i>
				{{ trans('textos.efecto_nuevo') }}
			</a>

			{{-- Agrupaciones --}}
			<a href="{{ route('effects.groups') }}" class="btn btn-primary">
				<i class="fa fa-object-group"></i>
				{{ trans('textos.efectos_agrupados') }}
			</a>
		@endcan
	</div>
</div>

{{-- Formulario filtro --}}
@include('admin.effects.partials.form-filter')

@if($customer || $provider)
	<div class="row">
		<div class="table-responsive">
			<table class="table table-hover dtabla" id="tblEffects">
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th class="text-center">
							{{ trans('textos.fecha_vencimiento') }}
						</th>
						<th>
							@php
							$company_label = $customer && $customer->id == session('currentCompany')? 'proveedor':'cliente';
							@endphp

							{{ ucfirst(trans('textos.'.$company_label)) }}
						</th>
						{{-- <th>
						{{ ucfirst(trans('textos.comercial')) }}
						</th> --}}
						{{-- <th>
						@lang('crud.effects.inputs.payment_type_id')
						</th>
						<th>
						@lang('crud.effects.inputs.invoice_serie_id')
						</th> --}}
						<th class="text-center">
							{{ trans('textos.agrupacion') }}
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
						<th class="text-center">
							{{ ucfirst(trans('textos.remesa')) }}
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
	</div>
@else
	<div class="row">
		<div class="col-12 text-center pt-4">
			<h2>{!! trans('textos.efectos_filtra') !!}.</h2>
		</div>
	</div>
@endif

{{-- Modals --}}
{{-- Add remittance --}}
@include('admin.effects.partials.modal-add-remittance')

@push('scripts')
<script>
$(function(){
	//Modal vincular remesa:
	$(document).on('click', '.btnAddRemittance', function(){
		$('#effectId').val($(this).data('id')); 
	});

});
</script>
@endpush

@endsection
