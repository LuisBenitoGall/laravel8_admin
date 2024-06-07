@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="protocols">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('protocols.create')
    		<a href="{{ route('protocols.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.protocolo_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProtocols">
			<thead>
				<tr>
					<td>{{ ucfirst(trans('textos.protocolo')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.fecha_alta')) }}</td>
					<td>{{ ucfirst(trans('textos.autor')) }}</td>
					<td>{{ ucfirst(trans('textos.estado')) }}</td>
					<td class="text-center">{{ ucfirst(trans('textos.acciones')) }}</td>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Protocols:
    if($('#tblProtocols').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');

        var tableProtocols = $('#tblProtocols').DataTable({
            serverSide: true,
            processing: true,
            stateSave: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/protocols",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'protocols.name'},
                {data: 'created_at', className: 'text-right'},
                {data: 'author'},
                {data: 'estado', name: 'estado'},
                {data: 'acciones', orderable: false, searchable: false, className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection