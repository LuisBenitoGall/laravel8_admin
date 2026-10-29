@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de tarifas --}}
		<a href="{{ route('tariffs.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_tarifas') !!}
		</a>

		{{-- Nueva tarifa --}}
		@can('tariffs.create')
			<a href="{{ route('tariffs.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.tarifa_nueva')) !!}
			</a>
		@endcan

		{{-- Clientes de la tarifa --}}
		<a href="{{ route('tariff-companies.index', $tariff) }}" class="btn btn-info btn-module">
			<i class="fa fa-building"></i>
			{!! ucfirst(trans('textos.tarifa_clientes')) !!}
		</a>

		{{-- Obras de la tarifa --}}
		{{-- <a href="{{ route('tariff-companies.index', $tariff) }}" class="btn btn-info btn-module">
			<i class="fa fa-warehouse"></i>
			{!! ucfirst(trans('textos.tarifa_obras')) !!}
		</a> --}}

		{{-- Productos premium --}}
		{{-- <a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-product">
			<i class="fa fa-plus"></i>
			{!! trans('textos.producto_premium_add') !!}
		</a> --}}

		{{-- Copiar productos premium a otras tarifas --}}
		{{-- @if($products_excluded->count())
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-reply-products">
				<i class="fa fa-copy"></i>
				{!! trans('textos.productos_premium_replicar') !!}
			</a>
		@endif --}}
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<h3 class="mb-4">{{ ucfirst(trans('textos.tarifa')) }} <u>{{ $tariff->name }}</u></h3>

				{{-- Info --}}
				<div class="mb-4">
					Creada: <b>{{ $tariff->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}</b>
					|
					Actualizada: <b>{{ $tariff->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}</b>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
					<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.info_general')) !!}</a>
					</li>

					{{-- <li class="nav-item @if($tab == 'products') active @endif">
						<a class="nav-link @if($tab == 'products') active @endif" href="#tab-products" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.productos_premium')) !!}</a>
					</li> --}}
				</ul>

				{{-- Panels --}}
				<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.tariffs.partials.tariff-info')
					</div>

					{{-- Productos excluidos --}}
					<div class="tab-pane fade @if($tab == 'products') show active @endif" role="tabpanel" id="tab-products">
						{{-- @include('admin.tariffs.partials.tariff-products-excluded') --}}
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>

{{-- Modals: --}}
{{-- Añadir producto excluido --}}
@include('admin.tariffs.partials.modal-add-product')

{{-- Editar margen del producto excluido --}}
{{-- @include('admin.tariffs.partials.modal-edit-product') --}}

{{-- Replicar productos premium a otras tarifas --}}
@include('admin.tariffs.partials.modal-reply-products')

@push('scripts')
<script>
$(function(){
	//Modal edición producto:
	$(document).on('click', '.btnEdit', function(e){
		e.preventDefault();
		var id = $(this).data('pe');
		var product = $(this).data('product');
		var discount = $(this).data('discount');

		$('#editProduct b').text(product);
		$('#peId').val(id);
		$('#peDiscount').val(discount);
	});
});
</script>
@endpush

@endsection