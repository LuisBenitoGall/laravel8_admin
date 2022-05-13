@extends('admin.partials.layout')

@section('content')

{{-- Filtros --}}
<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('invoices.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
				
					<div class="form-group row">
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.selec_cliente_proveedor_factura') !!}</p>
						</div>

						{{-- Selectores de cliente y proveedor --}}
						@include('admin.partials.selector-customer', ['all' => true, 'selected' => $customer_id])

						@include('admin.partials.selector-provider', ['all' => true, 'selected' => $provider_id])
					</div>

					<div class="form-group row">
						{{-- Año --}}
						@include('admin.partials.selector-year', ['selected_year' => $year])
					</div>

					<div class="form-group row">
						<label for="invoice_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="invoice_id" maxlength="50">
						</div>

						<label for="order_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_num')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="order_id" maxlength="50">
						</div>

						<label for="po" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra')) !!} </label>
						<div class="col-lg-2">
							<input class="form-control" type="text" name="po" maxlength="50">
						</div>
					</div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Tabla --}}
<div class="row">
	<div class="col-12 mb-3">
		<h3></h3>
	</div>

	<div class="table-responsive">


	</div>
</div>

@endsection