@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		<a href="{{ route('dealers.index') }}" class="btn btn-info">
			<i class="fa fa-angle-left"></i>
			{!! trans('textos.volver_repartidores') !!}
		</a>
	</div>
</div>

<div class="row mb-3">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-4">{!! trans('textos.repartidor') !!} <u>{{ ucwords($user->name) }} {{ ucwords($user->surname) }}</u></h3>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
			  		<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-shipments" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.repartos')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'vehicles') active @endif">
				    	<a class="nav-link @if($tab == 'vehicles') active @endif" href="#tab-vehicles" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.vehiculos')) !!}</a>
				  	</li>
				</ul>

				{{-- Panels --}}
				<div class="tab-content pt-5">
					{{-- Repartos: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-shipments">
						@include('admin.dealers.partials.dealer-shipments')	
					</div>	

					{{-- Vehículos: --}}
					<div class="tab-pane fade @if($tab == 'vehicles') show active @endif" role="tabpanel" id="tab-vehicles">
						@include('admin.dealers.partials.dealer-vehicles')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection