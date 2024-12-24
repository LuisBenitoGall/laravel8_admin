@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Abrir nueva incidencia --}}
		<a href="{{ route('incidences.create') }}" class="btn btn-info">
			<i class="fa fa-plus"></i>
			{!! ucfirst(trans('textos.incidencia_nueva')) !!}
		</a>
	</div>
</div>

{{-- Filtros --}}
<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('invoices.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}



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
		<table class="table table-hover dtabla" id="tblIncidences">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="wdt150">{{ ucfirst(trans('textos.concepto')) }}</th>
					<th class="text-center wdt50">{{ ucfirst(trans('textos.fecha')) }}</th>


					<th class="text-center wdt50">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Incidences:
    if($('#tblIncidences').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        $('#tblIncidences').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/incidences",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'num'},
                {data: 'concept'},
                {data: 'date', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection