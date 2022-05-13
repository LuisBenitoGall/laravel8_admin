@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('vehicles.index')
			<a href="{{ route('vehicles.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_vehiculos') !!}
			</a>
		@endcan

		@can('vehicles.create')
			<a class="btn btn-info" href="{{ route('vehicles.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.vehiculo_nuevo')) !!}</a>
		@endcan

		{{-- Eliminar vehículo --}}
		@can('vehicles.destroy')
			<form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                <input name="_method" type="hidden" value="DELETE">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.vehiculo_eliminar')) }}" data-msg="{{ trans('textos.vehiculo_confirm_eliminar') }}"><i class="fa fa-trash"></i></button>
            </form>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.vehiculo')) !!} 
					<u>
						{{ $vehicle->name }}
					</u>
				</h3>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.informacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'images') active @endif">
			  			<a class="nav-link @if($tab == 'images') active @endif" href="#tab-images" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.imagenes')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Info: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.vehicles.partials.vehicle-info')	
					</div>

					{{-- Imágenes: --}}
					<div class="tab-pane fade @if($tab == 'images') show active @endif" role="tabpanel" id="tab-images">
						@include('admin.vehicles.partials.vehicle-images')	
					</div>
				</div>



				
			</div>
		</div>
	</div>
</div>

@endsection