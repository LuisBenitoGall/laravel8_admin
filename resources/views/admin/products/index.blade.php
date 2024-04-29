@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myCompany" value="{{ $company->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Toggle vista - cuadrícula --}}
        @include('admin.partials.toggle-views', ['cookie' => 'products'])

        {{-- Nuevo producto - condiciones 
			1. Disponer de permisos de creación de producto.
			...
        --}}
		@can('products.create')
    		<a href="{{ route('products.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.producto_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>

{{-- Vista listado --}}
<div class="row">
	{{-- Catálogo de empresa --}}
	@if($company)
		<div class="col-12 mb-3">
			<h3>
				{{ ucfirst(trans('textos.catalogo')) }}
				<u>{{ $company->name }}</u>
			</h3>
		</div>	
	@endif

	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblProducts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.referencia')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th>{!! ucfirst(trans('textos.centro_coste')) !!}</th>
					<th>{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.producto_venta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Vista cuadrícula --}}
<div class="toggle-view-grid">
	{{-- Componente Products Grid --}}
	@livewire('products-grid', ['currency' => $currency, 'order_' => false, 'company' => $company, 'customer' => $company->id])

	@include('admin.partials.back-to-top')
</div>

{{-- Modals: --}}
{{-- Translations: --}}
@include('admin.languages.partials.modal-translations')

@push('scripts')
<script>
$(function(){
	 //Tabla Products:
    if($('#tblProducts').length > 0){
        var tokenDT = $('meta[name="csrf-token"]').attr('content');
        var company = $('#myCompany').val();

        //Filters:
        $('#tblProducts thead tr').clone(true).appendTo('#tblProducts thead');
        $('#tblProducts thead tr:eq(1) th').each(function(i){
            var title = $(this).text();
            if(i == 5 || i == 7){
                $(this).html('');
            }else{
                $(this).html('<input type="text" class="DTsearch" placeholder="'+txtBuscar+' '+title+'"/>');
            }
     
            $('input', this).on('keyup change', function(){
                if(tableProducts.column(i).search() !== this.value){
                    tableProducts
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });
        
        var tableProducts = $('#tblProducts').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/products/"+company,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'product', name: 'products.name'},
                {data: 'ref', name: 'products.ref'},
                {data: 'description'},
                {data: 'price', className: 'text-right', name: 'products.price'},
                {data: 'cost_center', name: 'cost_centers.name'},
                {data: 'image', className: 'text-center'},
                {data: 'on_sale', className: 'text-center'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }	
});
</script>
@endpush

@endsection