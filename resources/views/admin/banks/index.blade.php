@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Nuevo banco --}}
		@can('banks.create')
			<a class="btn btn-info" href="{{ route('banks.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.banco_nuevo')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBanks">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.banco')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th>SWIFT / BIC</th>
					<th>{!! ucfirst(trans('textos.codigo_europeo')) !!}</th>
					<th>LEI</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Banks:
    if($('#tblBanks').length > 0){
        $('#tblBanks').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/banks",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'swift'},
                {data: 'eu_code'},
                {data: 'lei'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection