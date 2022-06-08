@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.index')
			<a href="{{ route('order-groups.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_agrupaciones') !!}
			</a>
		@endcan

		{{-- Nueva agrupación --}}
		@can('orders.create')
			<a href="{{ route('order-groups.create') }}" class="btn btn-info">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.agrupacion_nueva')) !!}
    		</a>
    	@endcan

    	{{-- Agregar pedidos - condiciones: 
			1. Permisos de edición de pedidos.
			2. El estado de la agrupación debe permitir su edición.
			...
    	--}}
    	@can('orders.edit')
			@if($group->status == config('constants.ORDER_GROUPS_OPENED_'))
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-orders">
					<i class="fa fa-plus"></i>
					{!! trans('textos.pedidos_agregar') !!}
				</a>
			@endif
		@endcan

		{{-- Imprimir --}}
	    <a href="{{ route('order-groups.print', $group->id) }}" class="btn btn-info close-animation" rel="tooltip" title="{{ trans('textos.pedido_imprimir') }}">
			<i class="fa fa-print"></i>
		</a>

		{{-- Facturar - condiciones:
			0. El botón se oculta inicialmente y se muestra al seleccionar pedidos a facturar.
			1. Activo el módulo de contabilidad.
			2. Permisos para generación de facturas.
			3. El estado de la agrupación debe permitir su facturación.
			...
		--}}
		@if($module_accounting)
			{{-- Generar facturas --}}
			@can('invoices.create')
				{{-- Debe quedar importe por facturar. Se oculta o muestra por .js, se ejecuta en el archivo views/admin/order_groups/partials/group-orders.blade.php --}}
				@if($group->status == config('constants.ORDER_GROUPS_OPENED_'))
					<form method="POST" action="{{ route('invoices.create-from-group') }}" class="frm-inline d-none" role="form" accept-charset="UTF-8" id="frmGroupInvoice">
	                	{{ csrf_field() }}
	                	<input type="hidden" name="group_id" value="{{ $group->id }}">
	                	<input type="hidden" name="orders" id="setOrders">
	                	<input type="hidden" name="submit_generate_by_group" value="true">

	                	<button type="submit"  class="btn btn-info frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.facturar')) }}" data-msg="{{ ucfirst(trans('textos.facturar_grupo_confirm')) }}"><i class="fa fa-file-invoice-dollar"></i></button>
	            	</form>
				@endif
			@endcan

			{{-- Facturas de la agrupación --}}
			{{-- @if(count($invoices->count)) --}}
				<a href="{{ route('invoices.by-order-group', $group->id) }}" class="btn btn-info">
	    			<i class="fa fa-file-invoice-dollar"></i>
					{{ trans('textos.facturas_agrupacion') }}
				</a>
			{{-- @endif --}}
		@endif
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<h3 class="mb-3">
					{!! ucfirst(trans('textos.agrupacion')) !!} 
					<u>
						@if($group->name)
							{{ $group->name }}
						@else
							{{ $group->id }}
						@endif
					</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Cliente --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.cliente')) !!}: 
						<b>
							{{ ucfirst($customer->name) }}
						</b>
					</label>

					{{-- Proveedor --}}
					@include('admin.budget_order_shared_fields.partials.data-provider')

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $group->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $group->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.agrupacion')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'orders') active @endif">
			  			<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-group-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Agrupación: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.order_groups.partials.group-info')	
					</div>

					{{-- Pedidos de la agrupación: --}}
					<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-group-orders">
						@include('admin.order_groups.partials.group-orders')	
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Add orders: --}}
@if($group->status == config('constants.ORDER_GROUPS_OPENED_'))
	@can('orders.edit')
		@include('admin.order_groups.partials.modal-add-orders')
	@endcan
@endif

@push('scripts')
<script>
$(function(){
	//Órdenes a facturar:
	var orders_arr = [];
	$('.addOrder input:checkbox').on('change', function(){
		var id = $(this).val();
		//Agregar pedido:
		if($(this).is(':checked')){
			orders_arr.push(id);

		//Quitar pedido:
		}else{
			orders_arr = orders_arr.filter(function(e){
        		return e !== id;
        	});
		}

		console.log(orders_arr);

		if(orders_arr.length > 0){
			$('#frmGroupInvoice').removeClass('d-none');
		}else{
			$('#frmGroupInvoice').addClass('d-none');
		}
		$('#setOrders').val(orders_arr);
	});
});
</script>
@endpush	

@endsection