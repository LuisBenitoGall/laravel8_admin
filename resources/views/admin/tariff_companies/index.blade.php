@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a tarifa --}}
		<a href="{{ route('tariffs.edit', $tariff) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_tarifa') !!} {{ $tariff->name }}
		</a>

		{{-- Agregar clientes --}}
		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-customer">
			<i class="fa fa-plus"></i>
			{!! trans('textos.clientes_agregar') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			{{ ucfirst(trans('textos.tarifa_clientes')) }}
			<u>{{ $tariff->name }} ({{ $tariff->discount }}%)</u>
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla dataTable no-footer" id="tblTariffCompanies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.empresa')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Modals --}}
{{-- New company --}}
@include('admin.tariff_companies.partials.modal-add-customer')

@push('scripts')
<script>
$(function(){
	//Tabla Tariff Companies:
	if($('#tblTariffCompanies').length > 0){
		var tokenDT = $('meta[name="csrf-token"]').attr('content');
		var tariff_id = {{ $tariff->id }};

		var tableTariffCompanies = $('#tblTariffCompanies').DataTable({
			serverSide: true,
			processing: true,
			ajax: {
				"type": "GET",
				"url": "/api/admin/tariff-companies/"+tariff_id,
				"error": function(reason){
					//console.log(reason);
				}
			},
			columns: [
				{data: 'company', name: 'companies.name'},
				{data: 'created_at', className: 'text-right'},
				{data: 'acciones', className: 'text-right'}
			]
		});
	}
});
</script>
@endpush

@endsection
