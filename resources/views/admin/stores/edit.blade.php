@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de almacenes --}}
		<a href="{{ route('stores.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_almacenes') !!}
		</a>

		{{-- Nuevo almacén --}}
		@can('stores.create')
    		<a href="{{ route('stores.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.almacen_nuevo')) !!}
    		</a>
        @endcan
	</div>
</div>
		
<div class="row mb-3">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('stores.update', $store) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					@method('PUT')
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Nombre almacén --}}
						<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
						<div class="col-lg-4">
							<x-input type="text" name="name" id="name" value="{{ $store->name }}" placeholder="{!! ucfirst(trans('textos.almacen')) !!}" autocomplete="off" maxlength="75" required/>
						</div>

						{{-- Tipo --}}
						<label for="type" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.tipo')) !!} *</label>
						<div class="col-lg-3">
							@php
							$types = App\Helpers\helpers::storeTypes();
							@endphp

							<x-select name="type" class="select2" label="almacen_tipo_selec" value="{{ $store->type }}" :options="$types" info="stores-type" disabled/>
						</div>
						
						{{-- Status --}}
						<label for="status" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
						<div class="col-lg-1">
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="status" @if($store->status == 1) checked @endif>
				                <span class="chk" style="top: 5px;"></span>
				            </label>
						</div>
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
						<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-lg-10">
							<x-input-textarea name="observations" value="{!! $store->observations !!}"/>
						</div>
					</div>
					
					<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>	
	</div>
</div>

@endsection