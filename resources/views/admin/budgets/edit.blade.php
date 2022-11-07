@extends('admin.partials.layout')

@section('content')

@php
//Comprobamos que las líneas de presupuesto tipo formulario pertenezcan a algún bloque:
$setBlock = true;
// if($order->modality == config('constants.BUDGET_FORM_MODALITY_') && $itemsSelected->count() > 0){
// 	foreach($itemsSelected as $is_){
// 		if(!$is_->block_id){
// 			$setBlock = false;
// 			break;
// 		}
// 	}
// }
@endphp

<input type="hidden" id="myOrder" value="{{ $order->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado de presupuestos --}}
		@can('budgets.index')
			<a href="{{ route('budgets.index') }}" class="btn btn-info" id="teeest">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_presupuestos') !!}
			</a>
		@endcan

		{{-- Nuevo presupuesto --}}
		@can('budgets.create')
			@if($budget_from == 'customers')
				<a href="{{ route('budgets.create') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.presupuesto_nuevo')) !!}
				</a>
			@else
				<a target="_blank" href="{{ route('orders.create', 'purchase') }}" class="btn btn-info">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.compra_nueva')) !!}
				</a>
			@endif
		@endcan

		{{-- Aceptación presupuesto - condiciones:
			1. Exclusivo cliente.
			2. Sin fecha de aceptación.
		--}}
		@if($order->customer_id == session('currentCompany'))
			@if(!$order->accepted_date && $order->customer_id == session('currentCompany'))
				<a href="{{ route('budgets.accept', $order->id) }}" class="btn btn-warning btn-confirm" data-msg="{!! ucfirst(trans('textos.aceptar_presupuesto_confirm')) !!}">
					<i class="fa fa-check"></i>
					{!! trans('textos.aceptar_presupuesto') !!}
				</a>
			@else
				<a href="{{ route('budgets.cancel', $order->id) }}" class="btn btn-danger btn-confirm" data-msg="{!! ucfirst(trans('textos.rechazar_presupuesto_confirm')) !!}">
					<i class="fa fa-ban"></i>
					{!! trans('textos.rechazar_presupuesto') !!}
				</a>	
			@endif
		@endif

		{{-- Importar presupuesto - condiciones: 
			1. Exclusivo cliente.
			2. Para presupuestos con cliente final.	
			...
		--}}
		@if($order->customer_id == session('currentCompany') && $order->final_customer_id)
			<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-import-budget">
				<i class="fa fa-file-import"></i>
				{!! trans('textos.presupuesto_importar') !!}
			</a>
		@endif

		{{-- Vincular presupuesto de proveedor primero - condiciones: 
			1. Permisos de edición de presupuestos.
			2. Exclusivo proveedor.
			3. Que no exista un presupuesto vinculado previamente.
			4. Que existan pedidos vinculables.
			...
		--}}
		@can('budgets.edit')
			@if($order->provider_id == session('currentCompany') && !$order->first_provider_order && count($linkable_budgets))
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-first-provider">
					<i class="fa fa-link"></i>
					{!! trans('textos.presupuesto_proveedor_vincular') !!}
				</a>
			@endif
		@endcan

		{{-- Cambio proveedor - condiciones: 
			1. Con permisos de edición de pedidos.
			2. provider_id = empresa en sesión.
			3. El usuario está vinculado a varias empresas.
			4. El presupuesto no tiene pedido generado.
		--}}
		@can('budgets.edit')
			@if($order->provider_id == session('currentCompany') && count(session('companies')) > 1 && !$order->is_order)
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-set-provider">
					<i class="fa fa-exchange-alt"></i> {!! ucfirst(trans('textos.proveedor_cambio')) !!}
				</a>	
			@endif
		@endcan

		{{-- Nueva línea - condiciones:
			1. Permisos de edición.
			2. Según modalidad de formulario.
			...
		--}}
		@can('budgets.edit')
			@if($order->modality == config('constants.BUDGET_CATALOGUE_MODALITY_') || $order->modality == config('constants.BUDGET_BASIC_MODALITY_'))
				<a href="#" class="btn btn-info btnAddItem" data-toggle="modal" data-target="#modal-new-line">
					<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.linea_nueva')) !!}
				</a>
			@endif
		@endcan
		
		{{-- Versiones - condiciones: 
			1. Permisos de edición.
			2. Sólo proveedor.	
		--}}
		@can('budgets.edit')
			@if($order->provider_id == session('currentCompany'))
				{{-- Crear versión --}}
				<a href="{{ route('budgets.modify', $order->id) }}" class="btn btn-info btn-confirm" data-msg="{!! trans('textos.version_crear_confirm') !!}">
					<i class="fa fa-clone"></i>
					{!! ucfirst(trans('textos.version_crear')) !!}
				</a>

				{{-- Acceso a versiones: --}}
				@if($versions->count())
					@php
					$origBudget = (!$order->recid)? $order->id:$order->recid;
					@endphp

					<div class="budget-versions">
						<select class="form-control select2">
							<option value="">
								{!! ucfirst(trans('textos.versiones')) !!}
							</option>

							<option value="{{ $origBudget }}" @if($origBudget == $order->id) selected @endif>
								{!! ucfirst(trans('textos.presupuesto_original')) !!}
							</option>

							@foreach($versions as $vs)
								<option value="{{ $vs->id }}" @if($vs->id == $order->id) selected @endif>{{ $vs->name }} {{ $vs->bkey }}</option>
							@endforeach		
						</select>
					</div>
				@endif
			@endif
		@endcan

		{{-- Copiar presupuesto - condiciones: 
			1. Permisos creación presupuestos.
			2. Sólo proveedor.
		--}}
		@can('budgets.create')
			@if($order->provider_id == session('currentCompany'))
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-copy">
					<i class="fa fa-clone"></i>
					{!! ucfirst(trans('textos.copiar')) !!}
				</a>
			@endif
		@endcan

		{{-- Imprimir --}}
		<a href="{{ route('budgets.print', $order->id) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
			<i class="fa fa-print"></i>
		</a>

		{{-- Descuento - condiciones: 
			1. Disponer de permisos de edición de presupuestos.
			2. No existir pedido o no facturado ni vinculado a una agrupación.
			3. Exclusivo del proveedor.
			4. No debe existir ya el descuento.
			5. Sólo proveedor.
			...
		--}}	
		@can('budgets.edit')
			@if( (!$order->is_order || ($order && !$order->invoiced && !$order->order_parent)) && $order->provider_id == session('currentCompany') && !$order->discount && $order->provider_id == session('currentCompany'))
				<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-budget-discount" rel="tooltip" title="{{ trans('textos.descuento_presupuesto') }}">
	    			<i class="fa fa-percent"></i>
	    		</a>
	    	@endif
	    @endcan

		{{-- Generar pedido - condiciones: 
			1. Estar activo el módulo de Pedidos.
			2. Disponer de permisos para generar pedidos.
			3. El presupuesto debe encontrarse en algún estado apto para generar pedidos.
			4. No debe existir un pedido previo.
			...
		--}}
		@if($module_orders)
			@can('orders.create')
				@if(in_array($order->budget_status, config('constants.BUDGET_STATUS_APPROVED_')) && !$order->is_order)
					<a href="{{ route('orders.order-from-budget', $order) }}" class="btn btn-warning btn-confirm" data-msg="{!! trans('textos.pedido_confirm_generar') !!}">
						<i class="fa fa-shopping-cart"></i>
						{!! ucfirst(trans('textos.pedido_generar')) !!}
					</a>
				@endif
			@endcan
		@endif

		{{-- Acceso a pedido --}}
		@if($module_orders)
			@can('orders.edit')
				@if($order->is_order)
					<a target="_blank" href="{{ route('orders.edit', $order) }}" class="btn btn-info">
						<i class="fa fa-shopping-cart"></i>
						{!! ucfirst(trans('textos.pedido')) !!}
					</a>
				@endif
			@endcan
		@endif

		{{-- Generar proyecto / link a proyecto - condiciones: 
			1. Estar activo el módulo de Proyectos.
			2. Disponer de permisos para generar proyectos.
			3. Sólo proveedor.
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

		{{-- Eliminar presupuesto - condiciones: 
			1. Disponer de permisos.
			2. Pedido facturado.
			3. Sólo proveedor.
		--}}
		@can('budgets.destroy')
        	@if(!$invoiced && $order->provider_id == session('currentCompany'))
	        	<form method="POST" action="{{ route('budgets.destroy', $order) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                <input name="_method" type="hidden" value="DELETE">
	                {{ csrf_field() }}
	                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.presupuesto_eliminar')) }}" data-msg="{{ ucfirst(trans('textos.presupuesto_confirm_eliminar')) }}"><i class="fa fa-trash"></i></button>
	            </form>
	        @endif
        @endcan
	</div>
</div>

<input type="hidden" id="dtController" value="items">
<input type="hidden" id="myEnvironment" value="budgets">

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe presupuesto flotante: --}}
				@livewire('budget-amount', ['budget_amount' => $order_amount, 'currency' => $currency->symbol, 'budget_amount_without_discount' => $order_amount_without_discount, 'environment' => 'budgets'])

				<h3 class="mb-3">
					{!! ucfirst(trans('textos.presupuesto')) !!} 
					<u>
						@if($order->budget_num)
							{{ $order->budget_num }}
						@else
							{{ $order->id }}
						@endif

						@if($order->name)
							- {{ $order->name }}
						@endif
					</u>
				</h3>	

				{{-- Info --}}
				<div class="mb-3" style="max-width: 95%;">
					{{-- Cliente --}}
					@include('admin.orders.partials.data-customer')

					{{-- Proveedor --}}
					@include('admin.orders.partials.data-provider')

					{{-- Proveedor primero - condiciones: 
						1. Exclusivo proveedor.
						2. Debe existir.
					--}}
					@if($order->provider_id == session('currentCompany') && $first_provider)
						<label class="mr-5">
							{!! ucfirst(trans('textos.proveedor_primero')) !!}: 
							<b>
								{{ ucfirst($first_provider->name) }}
							</b>
							<a target="_blank" href="{{ route('budgets.edit', $first_provider->id) }}" class="ml-1" rel="tooltip" title="{{ trans('textos.presupuesto_editar') }}">
								<i class="fa fa-edit"></i>
							</a>
						</label>
					@endif

					{{-- Cliente Final --}}
					@if($final_customer)
						<label class="mr-5">
							{!! ucfirst(trans('textos.cliente_final')) !!}: 
							<b>{{ $final_customer->name }}</b>
						</label>
					@endif

					{{-- Presupuesto vinculado --}}
					@if($my_linked_budget)
						<label class="mr-5">
							{!! ucfirst(trans('textos.presupuesto_vinculado_mi')) !!}:
							<b><a target="_blank" href="{{ route('budgets.edit', $my_linked_budget->id) }}" class="text-warning">
								@if($my_linked_budget->budget_num) {{ $my_linked_budget->budget_num }} @else {{ $my_linked_budget->id }} @endif
							</a></b>
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
							{{ $order->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Importe --}}
					<label class="mr-5">
						@livewire('budget-amount-mini', ['budget_amount' => $order_amount, 'currency' => $currency->symbol])
					</label>

					{{-- Modalidad --}}
					@if($order->provider_id == session('currentCompany'))
						<label class="mr-5">
							{{ ucfirst(trans('textos.modalidad')) }}:
	  					    <b>@if($order->modality) {!! App\Helpers\helpers::budgetModalities($order->modality) !!} @endif</b>
						</label>
					@endif

					{{-- Descuento aplicado --}}
					@if(!$order || ($order && !$order->invoiced))
						@php
						$remove_discount = !$invoiced? true:false;
						@endphp
						@include('admin.orders.partials.data-discount-applied', ['return' => 'budgets', 'remove_discount' => $remove_discount])
					@endif

					{{-- Pedido --}}
					@include('admin.orders.partials.link-order')

					{{-- Proyecto --}}
					@include('admin.orders.partials.link-project')

					{{-- Acceso cliente - condiciones: 
						1. Exclusivo proveedor.
					--}}
					@if($order->provider_id == session('currentCompany'))
						<label class="mr-5 position-relative">
							{!! ucfirst(trans('textos.acceso_cliente')) !!}:
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="budget_shared" @if($order->budget_shared) checked @endif>
				                <span class="chk chksmall btnSharedBudget mt-1 ml-2" data-order="{{ $order->id }}"></span>
				            </label>

				            <div class="position-absolute" style="right: -10px; top: -3px;">
			            		{!! App\Helpers\helpers::info('budget-customer-access') !!}
			            	</div>
						</label>
					@endif
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.presupuesto_cabecera')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			  			<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.lineas')) !!}</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'docs') active @endif">
			  			<a class="nav-link @if($tab == 'docs') active @endif" href="#tab-docs" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.documentos')) !!}</a>	
			  		</li>

			  		@if($order->provider_id == session('currentCompany'))
				  		<li class="nav-item @if($tab == 'comments') active @endif">
				  			<a class="nav-link @if($tab == 'comments') active @endif" href="#tab-comments" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.notas')) !!}</a>	
				  		</li>
			  		
				  		<li class="nav-item @if($tab == 'template') active @endif">
				    		<a class="nav-link @if($tab == 'template') active @endif" href="#tab-budget-template" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.plantilla')) !!}</a>
				  		</li>
				  	@endif
				</ul>

				<div class="tab-content pt-4">
					{{-- Cabecera: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.budgets.partials.budget-info')	
					</div>

					{{-- Items: --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
						@include('admin.budgets.partials.budget-items')	
					</div>

					{{-- Documentos --}}
					<div class="tab-pane fade @if($tab == 'docs') show active @endif" role="tabpanel" id="tab-docs">
						@include('admin.orders.partials.order-docs')	
					</div>

					@if($order->provider_id == session('currentCompany'))
						{{-- Notas: --}}
						<div class="tab-pane fade @if($tab == 'comments') show active @endif" role="tabpanel" id="tab-comments">
							@include('admin.budgets.partials.budget-comments')	
						</div>

						{{-- Plantilla del presupuesto: --}}
						<div class="tab-pane fade @if($tab == 'template') show active @endif" role="tabpanel" id="tab-budget-template">
							@include('admin.budgets.partials.budget-template')	
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- New manager: --}}
@include('admin.budgets.partials.modal-new-customer-manager', ['return_view' => 'edit'])

{{-- New items block --}}
@include('admin.budgets.partials.modal-add-block')

{{-- Edit items block --}}
@include('admin.budgets.partials.modal-edit-block')

{{-- Discount --}}
@include('admin.orders.partials.modal-discount', ['return' => 'budgets'])

{{-- Add Project --}}
@can('projects.create')
	@include('admin.projects.partials.modal-add-project')
@endcan

{{-- Copy budget --}}
@can('budgets.create')
	@include('admin.budgets.partials.modal-copy')
@endcan

{{-- Modify customer --}}
@if(!$order || $order->percentage_invoiced == 0)
	@include('admin.orders.partials.modal-set-customer', ['environment_' => 'budgets'])	
@endif

{{-- Modify provider --}}
@include('admin.budgets.partials.modal-change-provider')

{{-- Add item --}}
@include('admin.orders.partials.modal-add-item', ['environment_' => 'budgets', 'modality' => $order->modality])

{{-- Edit item --}}
@include('admin.orders.partials.modal-edit-item', ['environment_' => 'budgets'])

{{-- See catalogue --}}
@include('admin.orders.partials.modal-see-catalogue')

{{-- Budget modality --}}
@if(!$order->modality)
	@include('admin.budgets.partials.modal-modality')	
@endif

{{-- Set items without form block --}}
@if($blocks)
	@include('admin.budgets.partials.modal-set-block')
@endif

{{-- Import budget --}}
@if($customer_budgets)
	@include('admin.budgets.partials.modal-import-budget')
@endif

{{-- Link first provider --}}
@if(count($linkable_budgets))
	@include('admin.budgets.partials.modal-first-provider')
@endif

@push('scripts')
<script>
$(function(){
	//Condición sobre aplicación de descuento.
	$('.discount-amount').on('change', function(){
		$('.discount-percent').val('');	
	});
	$('.discount-percent').on('change', function(){
		$('.discount-amount').val('');	
	});

	//Selector de versión de presupuesto.
    $('.budget-versions select').on('change', function(event){
        var id = event.target.value;
        if(id != ''){
        	window.location.href = '/admin/budgets/'+id+'/edit';
        }else{
        	return false;
        }
    });

    //Activar - desactivar acceso de cliente:
    $('.btnSharedBudget').on('click', function(){
        var status = '';
        var elem = $(this);
        var order = elem.data('order');
        //Atención!!: detecta el estado previo a la acción, de modo que :checked es si queda deseleccionado:
        if(elem.siblings('input').is(':checked')){
            status = 0;
        }else{
            status = 1;
        }  
        $.get("/admin/budgets/"+order+"/customer-access/"+status, function(response, state){});
    });

    //Selector condicional de modalidad de presupuesto:
    $('#selectBudgetModality').on('change', function(event){
        var option = event.target.value;

        if(option == 10){
        	//Comprobamos que el cliente no tenga una formulario vinculado por defecto:
            var customer_id = '{!! $order->final_customer_id !!}';
            if(customer_id == '') customer_id = false;
            var provider_id = {!! $order->customer_id !!};

            if(customer_id != '' && provider_id != ''){
                $.get("/admin/customers/own-budget-form/"+customer_id+"/"+provider_id, function(response, state){
                    if(response.budget_form_id){
                    	console.log(response);
                        //Marcamos formulario por defecto:
                        var arr = [response.budget_form_id];

                        //Atención al modo de marcar como selected las opciones en un Select2:
                        $('#budgetForm').val(arr);
                        $('#budgetForm').trigger('change');
                    } 

                    var numforms = $('#numForms').val();
                    if(numforms > 0){
                        $('#budgetForms').slideDown(500);
                            
                    }else{
                        $('#sbtNewBudget').prop('disabled', true);
                        $('#noBudgetForms').fadeIn();    
                    }
                }); 
            }

        	$('#budgetForms').slideDown();
        }
    });   

    //Modal edición nombre de bloque:
    $('.editBlockName').on('click', function(){
    	var id = $(this).data('id');
    	var name = $(this).data('name');

    	$('#bfiId').val(id);
    	$('#bfiName').val(name);
    }); 
});
</script>
@endpush

@endsection