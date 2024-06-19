@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="productId" value="{{ $product->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Regresar a edición producto --}}
		<a href="{{ route('products.edit', $product) }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! ucfirst(trans('textos.volver_producto')) !!}
		</a>

		{{-- Nueva vinculación --}}
		@can('providers.edit')
			<a href="{{ route('product-providers.create', $product) }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.proveedor_nuevo_add')) !!}
    		</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<h3>{{ ucfirst(trans('textos.producto_proveedores')) }} <u>{{ $product->name }}</u></h3>
	</div>

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblProductProviders">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.proveedor')) !!}</th>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Product Providers:
    if($('#tblProductProviders').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = $('#productId').val();
        
        $('#tblProductProviders').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-providers/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'name', name: 'companies.name', className: 'capitalize'},
                {data: 'product', name: 'products.name'},
                {data: 'ref', name: 'products.ref'},
                {data: 'price', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection