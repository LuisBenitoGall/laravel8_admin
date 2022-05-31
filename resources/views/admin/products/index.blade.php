@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Toggle vista - cuadrícula --}}
        @include('admin.partials.toggle-views', ['cookie' => 'products'])

        {{-- Nuevo producto --}}
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
	<div class="table-responsive toggle-view-list">
		<table class="table table-hover dtabla" id="tblProducts">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.producto')) !!}</th>
					<th>{!! ucfirst(trans('textos.descripcion')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.precio')) !!}</th>
					<th>{!! ucfirst(trans('textos.imagen')) !!}</th>
					<th class="wdt150">{!! ucfirst(trans('textos.producto_venta')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

{{-- Vista cuadrícula --}}
<div class="toggle-view-grid">
	{{-- Componente Products Grid --}}
	@livewire('products-grid', ['currency' => $currency, 'order_' => false])

	@include('admin.partials.back-to-top')
</div>

{{-- Modals: --}}
{{-- Translations: --}}
@include('admin.languages.partials.modal-translations')

@endsection