@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="dtController" value="deliveries">
<input type="hidden" id="myCustomer" value="@if($customer) {{ $customer->id }} @endif">
<input type="hidden" id="myYear" value="{{ $year }}">
<input type="hidden" id="myStatus" value="@if($status){{ $status }}@else{{ 0 }}@endif">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('deliveries.create')
			<a class="btn btn-info" href="{{ route('deliveries.create') }}"><i class="fa fa-plus"></i> {!! ucfirst(trans('textos.entrega_nueva')) !!}</a>
		@endcan
	</div>
</div>

{{-- Filtros --}}
<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('deliveries.list') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Selector de cliente --}}
						<div class="col-md-10 offset-md-2">
							<p class="text-warning">{!! trans('textos.albaranes_filtro_texto') !!}</p>
						</div>
						
						@include('admin.partials.selector-customer', ['all' => true, 'selected' => $customer? $customer->id:false])
					</div>

					<div class="form-group row">
						{{-- Año --}}
						@php
						$selected_year = $year? $year:date('Y');
						@endphp
						@include('admin.partials.selector-year', ['selected_year' => $selected_year])

						{{-- Estado --}}
						<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!} </label>
						<div class="col-lg-2">
							@php
							//Rehacemos el array de estados para incluir en primer lugar la opción de Todos. Descartamos el uso de la función array_unshift pues resetea los índices del resto de opciones, lo cual no interesa.
							$delivery_status_[0] = trans('textos.todos');
							foreach($delivery_status as $k => $os){
								$delivery_status_[$k] = $os;
							}
							@endphp

							<x-select name="status" class="select2" :options="$delivery_status_" label="estado_selec" value="{{ $status }}"/>
						</div>

						<x-btn-submit name="submit_filter" inline label="{{ trans('textos.buscar') }}"/>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

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