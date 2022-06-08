@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
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

		@can('orders.edit')
			{{-- Nueva línea --}}
			<a href="#" class="btn btn-info btnAddItem" data-toggle="modal" data-target="#modal-new-line">
				<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.linea_nueva')) !!}
			</a>

			{{-- Importar líneas --}}
	    	<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-import-items">
    			<i class="fa fa-file-import"></i>
    			{!! ucfirst(trans('textos.lineas_importar')) !!}
    		</a>
		@endcan

		{{-- Imprimir --}}
		<a href="{{ route('orders.print', $order) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
			<i class="fa fa-print"></i>
		</a>

		{{-- Descuento --}}
		@can('orders.edit')
			@if($permitted)
				@if($shared->provider_id == session('currentCompany') && !$shared->discount)
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
			...
		--}}
		@if($module_budgets && !$shared->budget_id && $shared->provider_id == session('currentCompany'))
			@can('budgets.create')
				<a href="{{ route('budgets.budget-from-order', $order) }}" class="btn btn-warning btn-confirm" data-msg="{!! trans('textos.presupuesto_confirm_generar') !!}">
					<i class="fa fa-file-alt"></i>
					{!! ucfirst(trans('textos.presupuesto_generar')) !!}
				</a>
			@endcan
		@endif

		{{-- Acceso a presupuesto --}}
		@if($module_budgets)
			@can('budgets.edit')
				@if($shared->budget_id)
					<a target="_blank" href="{{ route('budgets.edit', $shared->budget_id) }}" class="btn btn-info">
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
		@if($module_projects && $shared->provider_id == session('currentCompany'))
			{{-- Acceso a proyecto --}}
			@if($project_linked)
				@can('projects.edit')
					<a target="_blank" href="{{ route('projects.edit', $project_linked->project_id) }}" class="btn btn-info">
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

		{{-- Acceso a proyecto --}}
		@if($module_projects)
			@can('projects.edit')
				@if($shared->project_id)
					<a href="{{ route('projects.edit', $shared->project_id) }}" class="btn btn-info">
						<i class="fa fa-rocket"></i>
						{!! ucfirst(trans('textos.proyecto')) !!}
					</a>
				@endif
			@endcan
		@endif

		{{-- Vincular compra a un proyecto - condiciones: 
			1. Estar activo el módulo de Proyectos.
			2. Tratarse de una compra. 
			3. Disponer de permisos para editar proyectos.
			...
		--}}
		@if($module_projects && $shared->customer_id == session('currentCompany'))
			@can('projects.edit')
				<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-purchase-to-project">
					<i class="fa fa-rocket"></i>
					{!! ucfirst(trans('textos.proyecto_vincular_compra')) !!}
				</a>	
			@endcan
		@endif

		{{-- Agrupación de pedidos --}}
		@can('orders.edit')
			{{-- Link agrupación --}}
			@if($in_group)
				<a target="_blank" href="{{ route('order-groups.edit', $in_group->id) }}" class="btn btn-info">
					<i class="fa fa-object-group"></i>
					{!! ucfirst(trans('textos.agrupacion_pedidos')) !!}
				</a>

				{{-- Desvincular de agrupación --}}
				<a href="" class="btn btn-danger btn-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_desvincular')) !!}" data-msg="{{ trans('textos.pedido_desvincular_confirm') }}">
					<i class="fa fa-unlink"></i>
				</a>
				
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
			@if($order->prepare_delivery == 0 && $shared->provider_id == session('currentCompany'))
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
				&& in_array($order->status, config('constants.ORDER_STATUS_INVOICED_'))
				&& (!$invoiced || round($order_amount, 2) > round($invoiced['invoiced_amount'], 2))
				&& !$in_group
			)
				<a href="#" class="btn btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.facturar')) }}" data-toggle="modal" data-target="#modal-generate-invoice"><i class="fa fa-file-invoice-dollar"></i></a>
			@endif
		@endcan

		{{-- Facturas del pedido --}}
		@can('invoices.index')
			@if($invoices->count())	
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

			@if($no_previsions && round($order_amount, 2) > round($invoiced->invoiced_amount, 2))
				<a href="{{ route('order-previsions.recalculate-previsions', $order) }}" class="btn btn-info btn-confirm" data-msg="{{ ucfirst(trans('textos.facturaciones_previstas_recalculo_texto')) }}">
					<i class="fa fa-calculator"></i>
					{{ ucfirst(trans('textos.facturaciones_previstas_recalculo')) }}
				</a>
			@endif	
		@endcan

		{{-- Eliminar pedido - condiciones 
			1. Disponer de permisos para eliminar facturas.
			2. Que se cumplan las condiciones: $permitted = true.
		--}}
        @can('orders.destroy')
        	@if($permitted)
	        	<form method="POST" action="{{ route('orders.destroy', $order) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                <input name="_method" type="hidden" value="DELETE">
	                {{ csrf_field() }}
	                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.pedido_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.pedido_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	            </form>
	        @endif
        @endcan
	</div>
</div>

<input type="hidden" id="myShared" value="{{ $shared->id }}">

@php
//Customer Team:
$customer_team = $shared->customer_manager_id? [$shared->customer_manager_id]:[];
if($shared->customer_team){
	$customer_team = array_merge($customer_team, unserialize($shared->customer_team));
}

//Provider Team:
$provider_team = $shared->provider_manager_id? [$shared->provider_manager_id]:[];
if($shared->provider_team){
	$provider_team = array_merge($provider_team, unserialize($shared->provider_team));
}
@endphp

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe pedido flotante: --}}
				@livewire('budget-amount', ['budget_amount' => number_format($order_amount, 2), 'currency' => $currency->symbol, 'budget_amount_without_discount' => number_format($order_amount_without_discount, 2), 'environment' => 'orders'])

				<h3 class="mb-3">
					{!! ucfirst(trans('textos.pedido')) !!} 
					<u>
						@if($order->order_num)
							{{ $order->order_num }}
						@else
							{{ $order->id }}
						@endif
					</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">
					{{-- Cliente --}}
					@include('admin.budget_order_shared_fields.partials.data-customer')

					{{-- Proveedor --}}
					@include('admin.budget_order_shared_fields.partials.data-provider')
					
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
							{{ $order->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
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
						<label class="mr-5">
							{!! ucfirst(trans('textos.confirmado_por')) !!}:
							<b>
								{{ $confirmed_by->name }} {{ $confirmed_by->surname }}, {{ $customer_confirmation }} 	
							</b>
						</label>
					@endif

					{{-- Importe --}}
					<label class="mr-5">
						@livewire('budget-amount-mini', ['budget_amount' => $order_amount, 'currency' => $currency->symbol])
					</label>

					{{-- Descuento aplicado --}}
					@php
					$remove_discount = !$invoiced? true:false;
					@endphp
					@include('admin.budget_order_shared_fields.partials.data-discount-applied', ['return' => 'orders', 'remove_discount' => $remove_discount])

					{{-- Presupuesto --}}
					@include('admin.budget_order_shared_fields.partials.link-budget')

					{{-- Proyecto --}}
					{{-- @include('admin.budget_order_shared_fields.partials.link-project') --}}
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedido_cabecera')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'teams') active @endif">
			  			<a class="nav-link @if($tab == 'teams') active @endif" href="#tab-teams" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.equipos')) !!}</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			  			<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">Items</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'docs') active @endif">
			  			<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.documentos')) !!}</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'template') active @endif">
			  			<a class="nav-link @if($tab == 'template') active @endif" href="#tab-template" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.plantilla')) !!}</a>	
			  		</li>

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

					{{-- Equipos --}}
					<div class="tab-pane fade @if($tab == 'teams') show active @endif" role="tabpanel" id="tab-teams">
						@include('admin.orders.partials.order-teams')	
					</div>

					{{-- Items --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
						@include('admin.orders.partials.order-items')	
					</div>

					{{-- Documentos --}}
					<div class="tab-pane fade @if($tab == 'docs') show active @endif" role="tabpanel" id="tab-docs">
						@include('admin.orders.partials.order-docs')	
					</div>

					{{-- Plantilla --}}
					<div class="tab-pane fade @if($tab == 'template') show active @endif" role="tabpanel" id="tab-template">
						@include('admin.orders.partials.order-template')	
					</div>

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
		<div class="col-md-4 col-lg-3 sinpadding">
			@php
			$months = App\Helpers\Helpers::yearMonths();
			@endphp

			<select name="month[]" class="form-control">
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
@include('admin.budget_order_shared_fields.partials.modal-discount', ['return' => 'orders'])

{{-- Add Project --}}
@can('projects.create')
	@include('admin.projects.partials.modal-add-project')
@endcan

{{-- Modify customer --}}
@if($order->percentage_invoiced == 0)
	@include('admin.budget_order_shared_fields.partials.modal-set-customer', ['environment_' => 'orders'])	
@endif

{{-- Add member customer --}}
@include('admin.orders.partials.modal-add-member-customer')

{{-- Add member provider --}}
@include('admin.orders.partials.modal-add-member-provider')

{{-- Add item --}}
@include('admin.budget_order_shared_fields.partials.modal-add-item', ['environment_' => 'orders', 'modality' => false])

{{-- Edit item --}}
@include('admin.budget_order_shared_fields.partials.modal-edit-item', ['environment_' => 'orders'])

{{-- See catalogue --}}
@include('admin.budget_order_shared_fields.partials.modal-see-catalogue')

{{-- Add purchase to project --}}
@include('admin.orders.partials.modal-add-purchase-to-project')

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
@if(!$in_group)
	@include('admin.order_groups.partials.modal-link-order-group')
@endif

@push('scripts')
<script>
//Control del porcentaje de facturación:
var percent = '0';
function percentController(){
	var total = '0';
	$('.previsions .prevision .percent').each(function(){
    	var percent = $(this).val();
    	if(percent > 0){
    		total = (parseFloat(total) + parseFloat(percent)).toFixed(2);
    	}
    });	

	//Bloqueamos o habilitamos la inclusión de nuevas previsiones de facturación en función del porcentaje alcanzado:
    if(total >= 100){
    	$('.btnAddPrevision').fadeOut();
    }else{
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

    //Link a entregas.
    $('#linkDelivery').on('change', function(event){
    	var id = event.target.value;
    	window.open("/admin/deliveries/"+id+"/edit", "_blank");
    });

    //Modal añadir miembro:
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
});
</script>
@endpush

@endsection