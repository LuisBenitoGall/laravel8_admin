@extends('admin.partials.layout')

@section('content')
{{-- Avisos --}}
{{-- Riesgo crediticio --}}
@if($customer_settings->credit_risk && $order_amount > $customer_settings->credit_risk)
	<div class="alert alert-danger" role="alert">
        {!! trans('textos.riesgo_crediticio_alerta', ['riesgo' => $customer_settings->credit_risk.$currency->symbol]) !!} <a target="_blank" href="{{ route('customers.index') }}">{{ trans('textos.cliente_configuraciones') }}</a>.
    </div>
@endif

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Acciones agrupaciones --}}
		@if($order->is_group)
			{{-- Listado agrupaciones --}}
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
				@if(in_array($order->order_status, config('constants.ORDER_GROUPS_OPENED_')))
					<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-orders">
						<i class="fa fa-plus"></i>
						{!! trans('textos.pedidos_agregar') !!}
					</a>
				@endif
			@endcan

			{{-- Imprimir --}}
			<a href="{{ route('order-groups.print', $order) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
				<i class="fa fa-print"></i>
			</a>

			{{-- Facturar - condiciones: 
				1. Disponer de permisos para generar facturas.
				2. Estar activo el módulo de contabilidad.
				3. Status apto para facturación: abierto (10)
				...
			--}}
			@can('invoices.create')
				@if($module_accounting 
					&& in_array($order->order_status, config('constants.ORDER_GROUPS_OPENED_'))
				)
					<a href="{{ route('order-groups.to-invoice', $order) }}" class="btn btn-warning" rel="tooltip" title="{{ ucfirst(trans('textos.agrupacion_facturar')) }}"><i class="fa fa-file-invoice-dollar"></i></a>
				@endif
			@endcan

			{{-- Facturas de la agrupación - condiciones: 
				1. Permisos de listar facturas.
				2. Existen facturas declaradas.
			--}}
			@can('invoices.index')
				@if($invoices->count())	
					<a href="{{ route('invoices.by-order-group', $order) }}" class="btn btn-info" rel="tooltip">
						<i class="fa fa-file-invoice-dollar"></i>
						{{ ucfirst(trans('textos.facturas_agrupacion')) }}
					</a>	
				@endif
			@endcan

		{{-- Acciones pedidos simples --}}
		@else
			{{-- Listado pedidos --}}
			@can('orders.index')
				<a href="{{ route('orders.index') }}" class="btn btn-info">
					<i class="fa fa-angle-left"></i>
					{!! trans('textos.volver_pedidos') !!}
				</a>
			@endcan

			{{-- Nuevo pedido --}}
			@can('orders.create')
				<a href="{{ route('orders.create', $order_type) }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					@if($order_type == 'sale') {!! ucfirst(trans('textos.venta_nueva')) !!} @else {!! ucfirst(trans('textos.compra_nueva')) !!} @endif
				</a>
			@endcan

			{{-- Factura - condiciones: 
				1. Pedido facturado.
				2. Facturas declaradas.
			--}}
			{{-- @if($order->invoiced && $invoices->count())
				@if($invoices->count() == 1)
					<a target="_blank" href="{{ route('invoices.edit', $invoices[0]->id) }}" class="btn btn-info">
						<i class="fa fa-file-invoice-dollar"></i>
						{{ trans('textos.factura') }}
					</a>

				@elseif($invoices->count() > 1)
					<div class="several-invoices">
						<select class="form-control select2">
							<option value="">
								{!! ucfirst(trans('textos.facturas')) !!}
							</option>

							@foreach($invoices as $inv)
								<option value="{{ $inv->id }}">@if($inv->num) {{ $inv->num }} @else {{ $inv->id }} @endif</option>
							@endforeach
						</select>
					</div>
				@endif
			@endif --}}

			@can('orders.edit')
				@if(!$order->invoiced)
					{{-- Nueva línea --}}
					<a href="#" class="btn btn-info btnAddItem" data-toggle="modal" data-target="#modal-new-line">
						<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.linea_nueva')) !!}
					</a>

					{{-- Importar líneas --}}
			    	<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-import-items">
		    			<i class="fa fa-file-import"></i>
		    			{!! ucfirst(trans('textos.lineas_importar')) !!}
		    		</a>
		    	@endif
			@endcan

			{{-- Imprimir --}}
			<a href="{{ route('orders.print', $order) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
				<i class="fa fa-print"></i>
			</a>

			{{-- Descuento - condiciones: 
				1. Permitted = true.
				2. No facturado.
			--}}
			@can('orders.edit')
				@if($permitted && !$order->invoiced && !$order->order_parent)
					@if($order->provider_id == session('currentCompany') && !$order->discount)
						<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-budget-discount" rel="tooltip" title="{{ trans('textos.descuento_pedido') }}">
			    			<i class="fa fa-percent"></i>
			    		</a>
			    	@endif
			    @endif
		    @endcan

		    {{-- Generar presupuesto - condiciones 
		    	1. Estar activo el módulo de Presupuestos.
				2. Disponer de permisos para generar presupuestos.
				3. No debe existir un presupuesto previo.
				4. Exclusivo del proveedor.
				5. El pedido no está declarado como presupuesto.
				...
			--}}
			@if($module_budgets && $order->provider_id == session('currentCompany') && !$order->is_budget)
				@can('budgets.create')
					<a href="{{ route('budgets.budget-from-order', $order) }}" class="btn btn-warning btn-confirm" data-msg="{!! trans('textos.presupuesto_confirm_generar') !!}">
						<i class="fa fa-file-alt"></i>
						{!! ucfirst(trans('textos.presupuesto_generar')) !!}
					</a>
				@endcan
			@endif

			{{-- Acceso a presupuesto - condiciones 
				1. Activado el módulo de presupuestos.
				2. Exclusivo del proveedor.
			--}}
			@if($module_budgets && $order->provider_id == session('currentCompany'))
				@can('budgets.edit')
					@if($order->is_budget)
						<a target="_blank" href="{{ route('budgets.edit', $order) }}" class="btn btn-info">
							<i class="fa fa-file-alt"></i>
							{!! ucfirst(trans('textos.presupuesto')) !!}
						</a>
					@endif
				@endcan
			@endif

			{{-- Generar proyecto / link a proyecto - condiciones: 
				1. Estar activo el módulo de Proyectos.
				2. Tratarse de una venta.
				3. Disponer de permisos para generar proyectos.
				...
			--}}
			@if($module_projects && $order->provider_id == session('currentCompany'))
				{{-- Acceso a proyecto --}}
				@if($project)
					@can('projects.edit')
						<a target="_blank" href="{{ route('projects.edit', $project->id) }}" class="btn btn-info">
							<i class="fa fa-rocket"></i>
							{!! ucfirst(trans('textos.proyecto')) !!}
						</a>
					@endcan

				{{-- Vincular proyecto --}}
				@else
					@can('projects.create')
						<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-project">
							<i class="fa fa-rocket"></i>
							{!! ucfirst(trans('textos.proyecto_vincular')) !!}
						</a>
					@endcan
				@endif
			@endif

			{{-- Vincular compra a un proyecto - condiciones: 
				1. Estar activo el módulo de Proyectos.
				2. Tratarse de una compra. 
				3. Disponer de permisos para editar proyectos.
				...
			--}}
			@if($module_projects && $order->customer_id == session('currentCompany'))
				@can('projects.edit')
					{{-- Enlace a proyectos ya vinculados --}}
					@if(count($project_purchases))
						@foreach($project_purchases as $pp)
							<a target="_blank" href="{{ route('projects.edit', [$pp->projectId, 'purchases']) }}" class="btn btn-info">
								<i class="fa fa-rocket"></i>
								{!! ucfirst(trans('textos.proyecto')) !!}
							</a>	
						@endforeach
					@else
						<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-purchase-to-project">
							<i class="fa fa-rocket"></i>
							{!! ucfirst(trans('textos.proyecto_vincular_compra')) !!}
						</a>
					@endif	
				@endcan
			@endif

			{{-- Agrupación de pedidos - condiciones 
				1. Disponer de permisos de edición de pedidos.
				2. Exclusivo de proveedores.
				...
			--}}
			@can('orders.edit')
				@if($order->provider_id == session('currentCompany'))
					{{-- Link agrupación --}}
					@if($order->order_parent)
						<a target="_blank" href="{{ route('orders.edit', $order->order_parent) }}" class="btn btn-info">
							<i class="fa fa-object-group"></i>
							{!! ucfirst(trans('textos.agrupacion_pedidos')) !!}
						</a>

						{{-- Desvincular de agrupación - condiciones: 
							1. Pedido no facturado.
						--}}
						@if(!$order->invoiced)
							<form method="POST" action="{{ route('order-groups.destroy', $order->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		            			<input name="_method" type="hidden" value="DELETE">
		            			<input type="hidden" name="return" value="order-single">
		            			{{ csrf_field() }}
		            			<button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_desvincular')) !!}" data-msg="{!! ucfirst(trans('textos.pedido_desvincular_confirm')) !!}"><i class="fa fa-unlink"></i></button>
		        			</form>
		        		@endif
						
					{{-- Vincular a agrupación - condiciones: 
						1. Pedido no facturado.
					--}}
					@else
						@if(!$order->invoiced && !$invoices->count())
							<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-link-order-group">
								<i class="fa fa-object-group"></i>
								{!! ucfirst(trans('textos.agrupacion_vincular')) !!}
							</a>
						@endif
					@endif
				@endif
			@endcan

			{{-- Preparar entrega - condiciones: 
				1. Estar activo el módulo de Logística.
				2. Pedido sin preparar.
				3. Exclusivo para proveedor.
				4. Disponer de permisos para editar pedidos.
				...
			--}}
			@if($module_logistics)
				{{-- Iniciar preparación de la entrega del pedido --}}
				@if($order->prepare_delivery == 0 && $order->provider_id == session('currentCompany'))
					@can('orders.edit')
						<a href="{{ route('orders.set-delivery', $order) }}" class="btn btn-info btn-confirm" data-msg="{{ trans('textos.pedido_preparar_entrega_confirm') }}">
							<i class="fa fa-truck-loading"></i>
							{!! ucfirst(trans('textos.entrega_preparar')) !!}
						</a>	
					@endcan
				@endif

				{{-- Pedido en preparación para entregar --}}
				@if($order->prepare_delivery == 1)
					<a target="_blank" href="" class="btn btn-warning btn-confirm" data-msg="{{ trans('textos.entrega_preparar_confirm') }}">
						<i class="fa fa-tasks"></i>
						{!! ucfirst(trans('textos.entrega_en_preparacion')) !!}
					</a>	
				@endif

				{{-- Entregas del pedido: 
					1. Existir entregas vinculadas.
					2. El pedido debe estar validado para su preparación.
					...
				--}}
				@if($deliveries->count() && $order->prepare_delivery)
					@php
					$deliveries_ = [];
					foreach($deliveries as $dlv){
						$num = $dlv->delivery_num? $dlv->delivery_num:($dlv->delivery_old_num? $dlv->delivery_old_num:$dlv->deliveryId);
						$deliveries_[$dlv->deliveryId] = $num;
					}	
					@endphp

					<div class="d-inline-block align-top">
	        			<x-select name="delivery_id" class="select2" id="linkDelivery" label="entrega_ir" :options="$deliveries_"/>
	        		</div>
				@endif
			@endif

			{{-- Generar recepción - condiciones: 
				1. Estar activo el módulo de Logística.
				2. Exclusivo para cliente.
				3. Disponer de permisos para editar pedidos.
				...
			--}}
			@if($module_logistics)
				@if($order->customer_id == session('currentCompany'))
					@can('orders.edit')
						<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-set-receivings">
							<i class="fa fa-receipt"></i>
							{!! ucfirst(trans('textos.recepcion_aviso')) !!}
						</a>	
					@endcan
				@endif
			@endif

			{{-- Facturar - condiciones: 
				1. Disponer de permisos para generar facturas.
				2. Estar activo el módulo de contabilidad.
				3. Status apto para facturación: aprobado (10), entregado (40), finalizado (70)
				4. El importe facturado debe ser menor al importe total del pedido.
				5. No debe pertenecer a ninguna agrupación de pedidos.
				...
			--}}
			@can('invoices.create')
				@if($module_accounting 
					&& in_array($order->order_status, config('constants.ORDER_STATUS_INVOICED_'))
					&& (!$invoiced || round($order_amount, 2) > round($invoiced['invoiced_amount'], 2))
					&& !$order->order_parent
				)
					<a href="#" class="btn btn-warning" rel="tooltip" title="{{ ucfirst(trans('textos.facturar')) }}" data-toggle="modal" data-target="#modal-generate-invoice"><i class="fa fa-file-invoice-dollar"></i></a>
				@endif
			@endcan

			{{-- Facturas del pedido - condiciones: 
				1. Permisos de listar facturas.
				2. Existen facturas declaradas.
				3. Pedido facturado.
			--}}
			@can('invoices.index')
				@if($invoices->count() && $order->invoiced)	
					<a href="{{ route('invoices.by-order', $order) }}" class="btn btn-info" rel="tooltip">
						<i class="fa fa-file-invoice-dollar"></i>
						{{ ucfirst(trans('textos.facturas_pedido')) }}
					</a>	
				@endif
			@endcan

			{{-- Recalcular previsiones de facturación. Cuando se ha modificado el importe del pedido y los porcentajes de previsión de facturación no se ajustan al planteamiento original - condiciones:
				1. No quedan previsiones de facturación pendientes de facturar.
				2. El importe del pedido supera al facturado.
			--}}
			@can('invoices.edit')
				{{-- Controlamos que no existan previsiones sin facturar --}}
				@php
				$no_previsions = true;
				foreach($previsions as $prv){
					if(!$prv->invoice_id){
						$no_previsions = false;
						break;
					}
				}
				@endphp

				@if($no_previsions && $invoiced && round($order_amount, 2) > round($invoiced->invoiced_amount, 2))
					<a href="{{ route('order-previsions.recalculate-previsions', $order) }}" class="btn btn-info btn-confirm" data-msg="{{ ucfirst(trans('textos.facturaciones_previstas_recalculo_texto')) }}">
						<i class="fa fa-calculator"></i>
						{{ ucfirst(trans('textos.facturaciones_previstas_recalculo')) }}
					</a>
				@endif	
			@endcan
		@endif
		{{-- / Acciones pedidos simples --}}

		{{-- Eliminar pedido - condiciones 
			1. Disponer de permisos para eliminar facturas.
			2. Que se cumplan las condiciones: $permitted = true.
			3. Pedido no facturado.
			4. Exclusivo para proveedor.
		--}}
        @can('orders.destroy')
        	@if($permitted && !$order->invoiced && $order->provider_id == session('currentCompany'))
	        	<form method="POST" action="{{ route('orders.destroy', $order) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                <input name="_method" type="hidden" value="DELETE">
	                {{ csrf_field() }}
	                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.pedido_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.pedido_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	            </form>
	        @endif
        @endcan
	</div>
</div>

<input type="hidden" id="dtController" value="items">
<input type="hidden" id="myEnvironment" value="orders">
<input type="hidden" id="myOrder" value="{{ $order->id }}">
@php
//Indica si es vista de vendedor o comprador:
$perspective = session('currentCompany') == $order->provider_id? 'sale':'purchase';
@endphp
<input type="hidden" id="myPerspective" value="{{ $perspective }}">

@php
//Customer Team:
$customer_team = $order->customer_manager_id? [$order->customer_manager_id]:[];
if($order->customer_team){
	$customer_team = array_merge($customer_team, unserialize($order->customer_team));
}

//Provider Team:
$provider_team = $order->provider_manager_id? [$order->provider_manager_id]:[];
if($order->provider_team){
	$provider_team = array_merge($provider_team, unserialize($order->provider_team));
}
@endphp

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe pedido flotante: --}}
				{{-- Pedido simple --}}
				@if(!$order->is_group)
					@livewire('budget-amount', ['budget_amount' => $order_amount, 'currency' => $currency->symbol, 'budget_amount_without_discount' => $order_amount_without_discount, 'environment' => 'orders'])

				{{-- Agrupación --}}
				@else
					@livewire('budget-amount', ['budget_amount' => $group_amount, 'currency' => $currency->symbol, 'budget_amount_without_discount' => '', 'environment' => 'groups', 'bag_amount' => $order->bag_amount])	
				@endif

				<h3 class="mb-3">
					@if($order->is_group)
						{!! ucfirst(trans('textos.agrupacion')) !!}
					@else
						@if($order->provider_id == session('currentCompany'))
							{!! ucfirst(trans('textos.venta')) !!} 
						@elseif($order->customer_id == session('currentCompany'))
							{!! ucfirst(trans('textos.compra')) !!}
						@endif
					@endif
					<u>
						@if($order->order_num)
							{{ $order->order_num }}
						@else
							{{ $order->id }}
						@endif

						@if($order->name)
							- {{ $order->name }}
						@endif
					</u>

					{{-- Pedido en agrupación --}}
					@if($order->parent && !$order->is_group)
						<small class="ml-3 text-warning">
							({{ trans('textos.agrupacion_en') }})
						</small>	
					@endif
				</h3>

				{{-- Info --}}
				<div class="mb-3" style="width: 90%;">
					{{-- Cliente --}}
					@include('admin.orders.partials.data-customer')

					{{-- Proveedor --}}
					@include('admin.orders.partials.data-provider')

					{{-- Cliente Final --}}
					@if($final_customer)
						<label class="mr-5">
						{!! ucfirst(trans('textos.cliente_final')) !!}: 
						<b>{{ $final_customer->name }}</b>
					</label>
					@endif

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $order->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							@if($order->updated_at) {{ $order->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }} @endif
						</b>
					</label>

					{{-- Autor --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.autor')) !!}: 
						<b>
							@if($author)
								{{ ucfirst($author->name) }} {{ ucfirst($author->surname) }}
							@endif
						</b>
					</label>

					{{-- Propietario --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.propietario')) !!}: 
						<b>
							@if($owner)
								{{ ucfirst($owner->name) }} {{ ucfirst($owner->surname) }}
							@endif
						</b>
					</label>

					{{-- Confirmación pedido --}}
					@if($confirmed_by)
						@php
						$customer_confirmation = $order->customer_confirmation != ''? \Carbon\Carbon::parse($order->customer_confirmation)->format($languages[session()->get('locale')][4]):'';
						@endphp
						<label class="mr-5 bg-success p-1">
							{!! ucfirst(trans('textos.confirmado_por')) !!}:
							<b>
								{{ $confirmed_by->name }} {{ $confirmed_by->surname }}, {{ $customer_confirmation }} 	
							</b>
						</label>
					@endif

					{{-- Bolsa --}}
					@if($order->is_group && $order->bag_amount > 0)
						<label class="mr-5">
							{!! ucfirst(trans('textos.bolsa')) !!}:
							<b>
								{{ round($order->bag_amount, 2) }}{{ $currency->symbol }} 	
							</b>
						</label>
					@endif

					{{-- Importe --}}
					<label class="mr-5">
						@php
						$this_amount = $order->is_group? $group_amount:$order_amount;
						@endphp
						@livewire('budget-amount-mini', ['budget_amount' => $this_amount, 'currency' => $currency->symbol])
					</label>

					{{-- Descuento aplicado - condiciones: 
						1. El pedido no debe estar facturado.
						...
					--}}
					@php
					$remove_discount = !$order->invoiced? true:false;
					@endphp
					@include('admin.orders.partials.data-discount-applied', ['return' => 'orders', 'remove_discount' => $remove_discount])

					{{-- Presupuesto --}}
					@if($order->provider_id == session('currentCompany'))
						@include('admin.orders.partials.link-budget')
					@endif

					{{-- Proyecto --}}
					@include('admin.orders.partials.link-project')
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedido_cabecera')) !!}</a>
			  		</li>

			  		@if(!$order->is_group)
				  		<li class="nav-item @if($tab == 'items') active @endif">
				  			<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">{{ ucfirst(trans('textos.lineas')) }}</a>	
				  		</li>
				  	@endif

			  		@if($order->is_group)
			  			<li class="nav-item @if($tab == 'orders') active @endif">
				  			<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos')) !!}</a>	
				  		</li>
				  	@endif

			  		<li class="nav-item @if($tab == 'teams') active @endif">
			  			<a class="nav-link @if($tab == 'teams') active @endif" href="#tab-teams" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.equipos')) !!}</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'docs') active @endif">
			  			<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.documentos')) !!}</a>	
			  		</li>

			  		{{-- Template - condiciones: 
						1. Para pedidos no grupales.
						2. Exclusivo para proveedor.
			  		--}}
			  		@if(!$order->is_group && $order->provider_id == session('currentCompany'))
				  		<li class="nav-item @if($tab == 'template') active @endif">
				  			<a class="nav-link @if($tab == 'template') active @endif" href="#tab-template" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.plantilla')) !!}</a>	
				  		</li>
				  	@endif

			  		@if(count($project_purchases))
			  			<li class="nav-item @if($tab == 'projects') active @endif">
			  				<a class="nav-link @if($tab == 'projects') active @endif" href="#tab-projects" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.proyectos_compra')) !!}</a>	
			  			</li>
			  		@endif
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.orders.partials.order-info')	
					</div>

					{{-- Items --}}
					@if(!$order->is_group)
						<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
							@include('admin.orders.partials.order-items')	
						</div>
					@endif

					{{-- Equipos --}}
					<div class="tab-pane fade @if($tab == 'teams') show active @endif" role="tabpanel" id="tab-teams">
						@include('admin.orders.partials.order-teams')	
					</div>

					{{-- Pedidos --}}
					@if($order->is_group && $order->provider_id == session('currentCompany'))
						<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-orders">
							@include('admin.orders.partials.order-orders')	
						</div>
					@endif

					{{-- Documentos --}}
					<div class="tab-pane fade @if($tab == 'docs') show active @endif" role="tabpanel" id="tab-docs">
						@include('admin.orders.partials.order-docs')	
					</div>

					{{-- Plantilla --}}
					@if(!$order->is_group)
						<div class="tab-pane fade @if($tab == 'template') show active @endif" role="tabpanel" id="tab-template">
							@include('admin.orders.partials.order-template')	
						</div>
					@endif

					{{-- Proyectos de la compra --}}
					@if(count($project_purchases))
						<div class="tab-pane fade @if($tab == 'projects') show active @endif" role="tabpanel" id="tab-projects">
							@include('admin.orders.partials.order-purchase-projects')	
						</div>
					@endif
				</div>

























			</div>
		</div>
	</div>
</div>		

{{-- HTML para clonar y añadir a previsions --}}
<div class="clones" style="display: none;">
	<div class="form-group row prevision">
		{{-- Año --}}
		<label for="year" class="col-md-2 col-lg-1 col-form-label mb-2">{!! ucfirst(trans('textos.any')) !!}</label>
		<div class="col-md-4 col-lg-2 sinpadding">
			<select name="year[]" class="form-control">
				@for($y=date('Y'); $y<=(date('Y')+3); $y++)
					<option value="{{ $y }}">{{ $y }}</option>
				@endfor
			</select>

			@if($errors->has('year'))
		        <span class="invalid-feedback" role="alert">
		          <strong>{{ $errors->first('year') }}</strong>
		        </span>
		    @endif
		</div>

		{{-- Mes --}}
		<label for="month" class="col-md-2 col-lg-1 col-form-label">{!! ucfirst(trans('textos.mes')) !!}</label>
		<div class="col-md-4 col-lg-2 sinpadding">
			@php
			$months = App\Helpers\Helpers::yearMonths();
			@endphp

			<select name="month[]" class="form-control" required>
				<option value="">{{ trans('textos.mes_selec') }}</option>

				@foreach($months as $i => $month)
					<option value="{{ $i }}">{{ $month }}</option>
				@endforeach
			</select>

			@if($errors->has('month'))
		        <span class="invalid-feedback" role="alert">
		          <strong>{{ $errors->first('month') }}</strong>
		        </span>
		    @endif
		</div>

		{{-- Porcentaje --}}
		<label for="percent" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.porcentaje')) !!}</label>
		<div class="col-md-4 col-lg-2 sinpadding">
			<x-input type="text" name="percent[]" class="text-right set-decimals percent" autocomplete="off" maxlength="5" group="right" groupvalue="%" required/>
		</div>

		<div class="col-md-1 col-lg-1 pt-2">
			<button class="btn btn-danger btn-circle btn-circle-mini btnRemovePrevision" rel="tooltip" title="{{ trans('textos.prevision_eliminar') }}" data-msg="{{ trans('textos.prevision_eliminar_aviso') }}">
				<i class="fa fa-times"></i>
			</button>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Discount --}}
@include('admin.orders.partials.modal-discount', ['return' => 'orders'])

{{-- Add Project --}}
@can('projects.create')
	@include('admin.projects.partials.modal-add-project')
@endcan

{{-- Modify customer --}}
@if($order->percentage_invoiced == 0)
	@include('admin.orders.partials.modal-set-customer', ['environment_' => 'orders'])	
@endif

{{-- Add member customer --}}
@include('admin.orders.partials.modal-add-member-customer')

{{-- Add member provider --}}
@include('admin.orders.partials.modal-add-member-provider')

{{-- Add item --}}
@include('admin.orders.partials.modal-add-item', ['environment_' => 'orders', 'modality' => $order->modality])

{{-- Edit item --}}
@include('admin.orders.partials.modal-edit-item', ['environment_' => 'orders'])

{{-- See catalogue --}}
@include('admin.orders.partials.modal-see-catalogue')

{{-- Add purchase to project --}}
@include('admin.orders.partials.modal-add-purchase-to-project')

{{-- Modal set receivings --}}
@include('admin.receivings.partials.modal-set-receivings')

{{-- Modal edit receivings --}}
@include('admin.receivings.partials.modal-edit-receivings')

{{-- Generate invoice --}}
@php
$next_prevision = false;
foreach($previsions as $prev_){
	if(!$prev_->invoice_id){
		$next_prevision = $prev_;
		break;
	}	
}
@endphp
@include('admin.orders.partials.modal-generate-invoice', ['next_prevision' => $next_prevision])

{{-- Import items --}}
@include('admin.orders.partials.modal-import-items')

{{-- Link to order group --}}
@if(!$order->parent)
	@include('admin.order_groups.partials.modal-link-order-group')
@endif

{{-- Add orders to group --}}
@if($order->is_group && in_array($order->order_status, config('constants.ORDER_GROUPS_OPENED_')))
	@can('orders.edit')
		@include('admin.orders.partials.modal-group-add-orders')
	@endcan
@endif

{{-- Ronda de cambios --}}
@include('admin.orders.partials.modal-changes-round')

@push('scripts')
<script>
//Control del porcentaje de facturación:
var percent = '0';
function percentController(){
	var total = '0';
	var min = 0.02;
	var diff = '0';
	$('.previsions .prevision .percent').each(function(){
    	var percent = $(this).val();
    	if(percent > 0){
    		total = (parseFloat(total) + parseFloat(percent)).toFixed(2);
    	}
    });	

	diff = 100 - total;

	//Bloqueamos o habilitamos la inclusión de nuevas previsiones de facturación en función del porcentaje alcanzado:
    if(diff < min || parseFloat(total) >= 100){
    	$('.btnAddPrevision').fadeOut();
    }else if(diff >= min && parseFloat(total) < 100){
    	$('.btnAddPrevision').fadeIn();	
    }
    
    if(total > 100){
    	$('.previsions .prevision .percent:last').val('');
    	swal.fire({
	        title: txtFacturacion100,
	        text: "",
	        icon: 'warning',
	        iconColor: color_warning,
	        showCancelButton: false,
	        confirmButtonColor: color_danger,
	        cancelButtonColor: color_danger,
	        confirmButtonText: txtCancelar,
	        cancelButtonText: txtCancelar
	    });

	    return 'delete';
    }
}

$(function(){
	var customer_team = {!! json_encode($customer_team) !!};
	var provider_team = {!! json_encode($provider_team) !!};

	//Añadir previsión de facturación:
	$('.btnAddPrevision').on('click', function(e){
        e.preventDefault(); 
        var prev = $('.clones').html();
        $('.previsions').append(prev); 
        $('.fa-spinner').remove(); 
    });
    $(document).on('click', '.btnRemovePrevision', function(){
        $(this).closest('.prevision').remove();
    });

    //Control previsión de facturación:
    percentController();
    $(document).on('change', '.percent', function(){
   		var action = percentController();
   		if(action == 'delete'){
   			$(this).val('');
   		}
    });

    //Validación previsiones de facturación:
    //26/10/2022: se omite -por ahora- la validación de las previsiones de facturación. Se considera que hay pedidos que deben quedar abiertos y sin mes concreto para su facturación pues distorsiona la previsión para ese mes. Se modifica selector: $('#frmOrderInfo') por $('#frmOrderInfo_')
    $('#frmOrderInfo_').on('submit', function(){
    	var validate_previsions = true;		
    	$('.prevision-data').each(function(){
    		if($(this).val() == ''){
    			validate_previsions = false;
    		}
    	});

    	if(!validate_previsions){
    		swal.fire({
	            title: txtFacturacionPrevisionesAviso, 
	            text: "",
	            icon: 'warning',
	            iconColor: color_warning,
	            showCancelButton: false,
	            confirmButtonColor: color_primary,
	            cancelButtonColor: color_danger,
	            confirmButtonText: txtAceptar,
	            //cancelButtonText: txtCancelar
	        }).then((result) => {
	            if(result.isConfirmed){
	                
	            }
	        });       
    		return false;
    	}
    });

    //Link a entregas.
    $('#linkDelivery').on('change', function(event){
    	var id = event.target.value;
    	window.open("/admin/deliveries/"+id+"/edit", "_blank");
    });

    //Modal añadir miembro.
    $('.btnAddMember').on('click', function(e){
        e.preventDefault(); 
        var company = $(this).data('company');
        var environment = $(this).data('environment');
        $('#member_id').empty();
        $('#myEnvironment').val('');

        $.get("/admin/employees/"+company+"/company-employees/true", function(response, state){
            if(response){
            	$('#myEnvironment').val(environment);
        		for(i=0; i<response.length; i++){	
        			//Excluimos los valores existentes:
        			if(environment == 'customer'){
	        			if(!customer_team.includes(response[i].id)){
	             			$('#member_id').append('<option value="'+response[i].id+'">'+response[i].name+' '+response[i].surname+'</option>');
	             		}
	             	}else{
	             		if(!provider_team.includes(response[i].id)){
             				$('#member_id').append('<option value="'+response[i].id+'">'+response[i].name+' '+response[i].surname+'</option>');
             			}
	             	}
             	}    	
            }
        }); 
    });

    //Selector de factura.
    $('.several-invoices select').on('change', function(event){
        var id = event.target.value;
        if(id != ''){
        	window.location.href = '/admin/invoices/'+id+'/edit';
        }else{
        	return false;
        }
    });

    //Modal editar recepción.
    $('.btnEditReceiving').on('click', function(){
    	var id = $(this).data('id');
    	var receptiondate = $(this).data('receptiondate');
    	var observations = $(this).data('observations');
    	var origin = $(this).data('origin');

    	$('#editReceivingId').val(id);
    	$('#editCustomerObservations').val(observations);
    	$('#editEstimatedReceptionDate').val(receptiondate);
    	//Sólo edita la fecha si la recepción ha sido creada por el cliente:
    	if(origin == 'c'){
    		$('#editEstimatedReceptionDate').prop('disabled', false);	
    		$('#estimatedReceptionDateText').hide();	
    	}else{
    		$('#editEstimatedReceptionDate').prop('disabled', true);
    		$('#estimatedReceptionDateText').show();
    	}  	
    });

    //Modal ronda de cambios.
    $(document).on('click', '.btnChangesRound', function(){
        var id = $(this).data('id');
        var line = $(this).data('line');

        $('#ChangesRound b').text(line);
        $('#itemId').val(id);

        $.get("/admin/item-status/"+id, function(response, state){
            if(response){
                var html = '';
                for(i=0; i<response.length; i++){
                    html += '<tr>';
                        html += '<td>'+response[i].created_at+'</td>';
                        var pr_ob = response[i].provider_observations? response[i].provider_observations:'';
                        html += '<td>'+pr_ob+'</td>';
                        var cu_ob = response[i].customer_observations? response[i].customer_observations:'';
                        html += '<td>'+cu_ob+'</td>';
                        html += '<td class="text-right">';
                            //Estado
                            var checked = response[i].status == 1? 'checked':'';
                            var title = response[i].status == 1? txtCambiosCompletados:txtCambiosEnCurso;
                            html += '<label class="custom-chk-rdb" rel="tooltip" title="'+title+'" style="margin-top: 5px;">';
                                html += '<input type="checkbox" value="'+response[i].id+'" '+checked+'>';
                                html += '<span class="chk1 chkChangesRound"></span>';
                            html += '</label>';

                            //Eliminar:
                            html += '<a href="/admin/item-status/'+response[i].id+'/delete" class="btn btn-xs btn-danger btn-confirm" data-msg="'+txtRondaCambiosEliminarConfirm+'" rel="tooltip" title="'+txtEliminar+'"><i class="fa fa-trash"></i></a>';
                        html += '</td>';
                    html += '</tr>';
                }

                $('#tblChangesRound tbody').html(html);    
            }
        });
    });

    //Actualizar estado de ronda de cambios.
    $(document).on('click', '.chkChangesRound', function(){
        var status = '';
        var elem = $(this);
        //Atención!!: detecta el estado previo a la acción, de modo que :checked es si queda deseleccionado:
        if(elem.siblings('input').is(':checked')){
            status = 0;
        }else{
            status = 1;
        }  
        var id = elem.siblings('input').val();
        $.get("/admin/item-status/"+id+"/"+status+"/status", function(response, state){
            if(response){
                $('.check-ko').remove();
                elem.parent('label').before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            }else{
                elem.parent('label').before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        });   
    });

    //Mostrar formulario nueva ronda de cambios.
    $('.btnAddChangeRound').on('click', function(e){
        e.preventDefault();  
        $('#addChangeRound').slideToggle('slow'); 
    });
});
</script>
@endpush

@endsection