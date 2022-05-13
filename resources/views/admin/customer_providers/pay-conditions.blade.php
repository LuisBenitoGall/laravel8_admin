@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-sm-12">
		<a href="{{ route('customers.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_clientes') !!}
		</a>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-4">{{ trans('textos.condiciones_pago_de') }} <u>{{ $customer->name }}</u></h3>	

				<form method="post" autocomplete="off" action="{{ route('customers.update-conditions', $conditions->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}
					<input type="hidden" name="customer_id" value="{{ $customer->id }}">

					<div class="form-group row">
						{{-- Días para vencimiento --}}
						<label for="pay_after_days" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.dias_vencimiento')) !!}</label>
						<div class="col-lg-1">
							<x-input type="text" name="pay_after_days" class="text-right" autocomplete="off" maxlength="3" required info="pay-conditions-days" value="{{ $conditions->pay_after_days }}"/>
						</div>

						{{-- Fecha de pago --}}
						<label for="payday" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.dia_mes')) !!}</label>
						<div class="col-lg-1">
							<x-input type="text" name="payday" class="text-right max-value" datas="maxvalue:31" autocomplete="off" maxlength="3" required info="pay-conditions-pay-day" value="{{ $conditions->payday }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Método envío de las facturas --}}
						<label for="billing_comunication" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.envio_facturacion')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="billing_comunication" value="{{ $conditions->billing_comunication }}"/>
						</div>
					</div>

					<x-btn-submit name="submit_update_conditions" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Facturación por Áreas de negocio --}}
@if($areas->count() > 0)
	<div class="row">
		<div class="col-12">
			<div class="card mb-3">
				<div class="card-body">	
					<h3>{{ trans('textos.facturas_area_negocio') }} <u>{{ $customer->name }}</u></h3>	
					<p class="text-warning">{{ trans('textos.facturas_area_negocio_aviso') }}</p>
					<br>

				</div>
			</div>
		</div>
	</div>
@endif

@endsection