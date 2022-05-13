@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="workOrderId" value="{{ $work_order->id }}">
<input type="hidden" id="dtController" value="work-order-products">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('work-orders.index')
			<a href="{{ route('work-orders.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_trabajo_ordenes') !!}
			</a>
		@endcan

		{{-- Crear orden de trabajo --}}
		@can('work-orders.create')
			<a href="{{ route('work-orders.create') }}" class="btn btn-info">
				<i class="fa fa-plus"></i>
				{!! trans('textos.trabajo_orden_nueva') !!}
			</a>
		@endcan

		{{-- Nuevo producto --}}
		@can('work-orders.edit')
			@if($permitted)
				<a href="#" class="btn btn-info btnAddItem" data-toggle="modal" data-target="#modal-new-product">
					<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.producto_add')) !!}
				</a>
			@endif
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.trabajo_orden')) !!} 
					<u>
						@if($work_order->work_order_num)
							{{ $work_order->work_order_num }}
						@else
							{{ $work_order->id }}
						@endif
					</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $work_order->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $work_order->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.creada_por')) !!}: 
						<b>
							{{ $author->name }} {{ $author->surname }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.informacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'products') active @endif">
			  			<a class="nav-link @if($tab == 'products') active @endif" href="#tab-products" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.productos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.work_orders.partials.work-order-info')	
					</div>

					{{-- Productos --}}
					<div class="tab-pane fade @if($tab == 'products') show active @endif" role="tabpanel" id="tab-products">
						@include('admin.work_orders.partials.work-order-products')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Add product --}}
@include('admin.work_orders.partials.modal-add-product')

{{-- Edit product --}}
@include('admin.work_orders.partials.modal-edit-product')

{{-- See catalogue --}}
@include('admin.budget_order_shared_fields.partials.modal-see-catalogue')

@endsection
