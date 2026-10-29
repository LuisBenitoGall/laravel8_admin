@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Volver a producto referencia --}}
		@can('products.edit')
    		<a href="{{ route('products.edit', $product) }}" class="btn btn-info">
    			<i class="fa fa-angle-left"></i>
    			{!! ucfirst(trans('textos.volver_producto')) !!}
    		</a>
        @endcan

        {{-- Nueva variante - condiciones 
			1. Disponer de permisos de edición de producto.
			...
        --}}
		@can('products.edit')
    		<a href="{{ route('products.create-variant', $product->id) }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.variante_nueva')) !!}
    		</a>
        @endcan
	</div>
</div>

{{-- Vista listado --}}
<div class="row">
	@if($product)
		<div class="col-12 mb-3">
			<h3>
				{{ ucfirst(trans('textos.producto_variantes')) }}
				<u>{{ $product->name }}</u>
			</h3>
		</div>	
	@endif

	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblProductVariants">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
                    <th>{!! ucfirst(trans('textos.atributos')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th>{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	 //Tabla ProductVariants:
    if($('#tblProductVariants').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var product_id = {{ $product->id }};

        //Filters:
        $('#tblProductVariants thead tr').clone(true).appendTo('#tblProductVariants thead');
        $('#tblProductVariants thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 5 || i == 7){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableProductVariants.column(i).search() !== this.value){
                    tableProductVariants
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableProductVariants = $('#tblProductVariants').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/product-variants/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'product', name: 'products.name'},
                {data: 'ref', name: 'products.ref'},
                {data: 'attributes'},
                {data: 'price', className: 'text-right', name: 'products.price'},
                {data: 'image', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }	
});
</script>
@endpush

@endsection