@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="shipments">
<input type="hidden" id="seeShipments" value="{{ $see_shipments }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nuevo envío --}}
		@can('shipments.create')
			<a class="btn btn-info" href="{{ route('shipments.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.reparto_nuevo')) !!}</a>
		@endcan
	</div>
</div>

{{-- Filtro --}}
<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('shipments.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Filtro por estado --}}
						<label for="shipments_selec" class="col-lg-2 col-form-label">
							{!! ucfirst(trans('textos.reparto_estado')) !!}
						</label>
						<div class="col-lg-4">
							@foreach($shipment_status as $idx => $ds)
								<label class="custom-chk-rdb mr-4" style="margin-bottom: 0; margin-top: 7px;">
									<input type="radio" name="shipments_selec" value="{{ $idx }}" @if($see_shipments == $idx) checked @endif required>
									<span class="rdb chksmall"></span>
									{{ $ds }}
								</label>
							@endforeach
						</div>
 
						{{-- Clientes --}}
						{{-- <label for="customer" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente')) !!}</label>

						<x-customer-select2 /> --}}
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>
{{-- / Filtro --}}

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblShipments">
			<thead>
				<tr>
					<th class="text-center">{!! ucfirst(trans('textos.reparto')) !!}</th>
					<th>{!! ucfirst(trans('textos.vehiculo')) !!}</th>
					<th>{!! ucfirst(trans('textos.matricula')) !!}</th>
					<th>{!! ucfirst(trans('textos.repartidor')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th>{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Shipments:
	if($('#tblShipments').length > 0){
		var tokenDT = $('meta[name="csrf-token"]').attr('content');
		var see_shipments = $('#seeShipments').val();
		//var customer_id = $('#customerId_').val()? $('#customerId_').val():'x';

		$('#tblShipments').DataTable({
			serverSide: true,
			processing: true,
			ajax: {
				"type": "GET",
				"url": "/api/admin/shipments/"+see_shipments,
				"error": function(reason){
					console.log(reason);
				}	
			},
			columns: [
				{data: 'num', className: 'capitalize'},
				{data: 'vehicle'},
				{data: 'car_license'},
				{data: 'delivery_man'},
				{data: 'date', className: 'text-right'},
				{data: 'estado'},
				{data: 'acciones', className: 'text-right'}
			]
		});
	}
});
</script>
@endpush

@endsection
