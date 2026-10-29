@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myProduct" value="@if($product) {{ $product->id }} @endif">

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		@can('batches.create')
			<a class="btn btn-info" href="{{ route('batches.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.lote_nuevo')) !!}</a>
		@endcan
	</div>
</div>

{{-- Filtros --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('batches.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Producto --}}
						<label for="product_" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}</label>
						<div class="col-lg-5 position-relative">
							<x-input type="text" name="product_" id="search_product" placeholder="{!! ucfirst(trans('textos.producto')) !!}" maxlength="150"/>
						</div>

						<input type="hidden" name="product_id" id="productId">
					</div>

					<x-btn-submit name="submit_filter" label="{{ trans('textos.buscar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	@if($product)
		<div class="col-12 mb-3">
			<h3>
				{!! trans('textos.prooucto_lotes') !!} <u>{{ $product->name }}</u>
			</h3>
		</div>
	@endif

	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblBatches">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.lote')) !!}</th>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_alta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_caducidad')) !!}</th>
					<th>{!! ucfirst(trans('textos.produccion_orden')) !!}</th>
					<th>{!! ucfirst(trans('textos.cantidad_inicial')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Tabla Batches:
    if($('#tblBatches').length > 0){
        var product_id = $('#myProduct').val();

        $('#tblBatches').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                "type": "GET",
                "url": "/api/admin/batches/"+product_id,
                "error": function(reason){
                    console.log(reason);
                }
            },
            columns: [
                {data: 'batch'},
                {data: 'product'},
                {data: 'fecha', className: 'text-right'},
                {data: 'expiry_date', className: 'text-right'},
                {data: 'work_order'},
                {data: 'initial_qty', className: 'text-right'},
                {data: 'acciones', className: 'text-right'}
            ]
        });
    }
});
</script>
@endpush

@endsection