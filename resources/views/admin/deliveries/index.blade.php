@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('deliveries.create')
			<a class="btn btn-info" href="{{ route('deliveries.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.entrega_nueva')) !!}</a>
		@endcan
	</div>
</div>

<input type="hidden" id="dtController" value="deliveries">

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-deliveries" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.entregas')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'orders') active @endif">
			  			<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
			  		{{-- Entregas --}}
			  		<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-deliveries">
						@include('admin.deliveries.partials.deliveries-deliveries')	
					</div>

					{{-- Pedidos --}}
					<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-orders">
						@include('admin.deliveries.partials.deliveries-orders')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection