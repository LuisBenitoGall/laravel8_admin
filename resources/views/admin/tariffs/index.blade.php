@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myCompany" value="{{ $company->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Nueva tarifa --}}
		@can('tariffs.create')
			<a href="{{ route('tariffs.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.tarifa_nueva')) !!}
			</a>
		@endcan
	</div>
</div>

<div class="row mb-3">
	<div class="col-12 mb-3">
		<h3>
			{{ ucfirst(trans('textos.tarifas')) }}
			<u>{{ $company->name }}</u>
		</h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla dataTable no-footer" id="tblTariffs">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.tarifa')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.descuento')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Tariffs:
	if($('#tblTariffs').length > 0){
		var tokenDT = $('meta[name="csrf-token"]').attr('content');

		var tableTariffs = $('#tblTariffs').DataTable({
			serverSide: true,
			processing: true,
			ajax: {
				"type": "GET",
				"url": "/api/admin/tariffs",
				"error": function(reason){
					//console.log(reason);
				}
			},
			columns: [
				{data: 'name', name: 'tariffs.name'},
				{data: 'discount', name: 'tariffs.discount', className: 'text-right'},
				{data: 'description'},
				{data: 'created_at', className: 'text-right'},
				{data: 'acciones', className: 'text-right'}
			]
		});
	}
});
</script>
@endpush

@endsection