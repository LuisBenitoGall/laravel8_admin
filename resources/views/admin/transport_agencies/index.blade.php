@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Nueva agencia --}}
		@can('banks.create')
			<a class="btn btn-info" href="{{ route('transport-agencies.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.agencia_nueva')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblTransportAgencies">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.agencia')) !!}</th>
					<th>{!! ucfirst(trans('textos.nombre_comercial')) !!}</th>
					<th>NIF</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Transport Agencies:
    if($('#tblTransportAgencies').length > 0){
        $('#tblTransportAgencies').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/transport-agencies",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'tradename'},
                {data: 'nif'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection