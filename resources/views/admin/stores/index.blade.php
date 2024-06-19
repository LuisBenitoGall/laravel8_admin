@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
        {{-- Nuevo almacén --}}
		@can('stores.create')
    		<a href="{{ route('stores.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.almacen_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

<input type="hidden" id="dtController" value="stores">

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblStores">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.almacen')) !!}</th>
					<th>{!! ucfirst(trans('textos.almacen_tipo')) !!}</th>
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
	//Tabla Stores:
    if($('#tblStores').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        
        $('#tblStores').DataTable({
            serverSide: false,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/stores",
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name'},
                {data: 'tipo'},
                {data: 'alta', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection