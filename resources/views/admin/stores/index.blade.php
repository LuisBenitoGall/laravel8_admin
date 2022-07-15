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

@endsection