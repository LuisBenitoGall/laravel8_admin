@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="myShared" value="{{ $shared->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('budgets.index')
			<a href="{{ route('budgets.index') }}" class="btn btn-info">
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
			@endif
		@endcan

		{{-- Cambio proveedor - condiciones: 
			1. Con permisos de edición de pedidos.
			2. provider_id = empresa en sesión.
			3. El usuario está vinculado a varias empresas.
			4. El presupuesto no tiene pedido generado.
		--}}
		@can('budgets.edit')
			@if($shared->provider_id == session('currentCompany') && count(session('companies')) > 1 && !$shared->order_id)
				<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-set-provider">
					<i class="fa fa-exchange-alt"></i> {!! ucfirst(trans('textos.proveedor_cambio')) !!}
				</a>	
			@endif
		@endcan

		{{-- Nueva línea - condiciones:
			1. Permisos de edición.
			2. Según modalidad de formulario.
			3. Sólo si no está facturado 100%.  PENDIENTE AÑADIR CONDICIÓN
		--}}
		@can('budgets.edit')
			@if($budget->modality == config('constants.BUDGET_CATALOGUE_MODALITY_') || $budget->modality == config('constants.BUDGET_BASIC_MODALITY_'))
				<a href="#" class="btn btn-info btnAddItem" data-toggle="modal" data-target="#modal-new-line">
					<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.linea_nueva')) !!}
				</a>
			@endif
		@endcan
		
		{{-- Versiones --}}
		@can('budgets.edit')
			{{-- Crear versión --}}
			<a href="{{ route('budgets.modify', $budget->id) }}" class="btn btn-info btn-confirm" data-msg="{!! trans('textos.version_crear_confirm') !!}">
				<i class="fa fa-clone"></i>
				{!! ucfirst(trans('textos.version_crear')) !!}
			</a>

			{{-- Acceso a versiones: --}}
			@if($versions->count())
				@php
				$origBudget = (!$budget->recid)? $budget->id:$budget->recid;
				@endphp

				<div class="budget-versions">
					<select class="form-control select2">
						<option value="">
							{!! ucfirst(trans('textos.versiones')) !!}
						</option>

						<option value="{{ $origBudget }}" @if($origBudget == $budget->id) selected @endif>
							{!! ucfirst(trans('textos.presupuesto_original')) !!}
						</option>

						@foreach($versions as $vs)
							<option value="{{ $vs->id }}" @if($vs->id == $budget->id) selected @endif>{{ $vs->name }} {{ $vs->bkey }}</option>
						@endforeach		
					</select>
				</div>
			@endif
		@endcan

		{{-- Imprimir --}}
		<a href="{{ route('budgets.print', $budget->id) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
			<i class="fa fa-print"></i>
		</a>

		{{-- Descuento --}}
		@can('budgets.edit')
			@if($shared->provider_id == session('currentCompany') && !$shared->discount)
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
		@if($module_orders && !$shared->order_id)
			@can('orders.create')
				@if(in_array($budget->status, config('constants.BUDGET_STATUS_APPROVED_')) && !$shared->order_id)
					<a href="{{ route('orders.order-from-budget', $budget) }}" class="btn btn-warning btn-confirm" data-msg="{!! trans('textos.pedido_confirm_generar') !!}">
						<i class="fa fa-shopping-cart"></i>
						{!! ucfirst(trans('textos.pedido_generar')) !!}
					</a>
				@endif
			@endcan
		@endif

		{{-- Acceso a pedido --}}
		@if($module_orders)
			@can('orders.edit')
				@if($shared->order_id)
					<a href="{{ route('orders.edit', $shared->order_id) }}" class="btn btn-info">
						<i class="fa fa-shopping-cart"></i>
						{!! ucfirst(trans('textos.pedido')) !!}
					</a>
				@endif
			@endcan
		@endif

		{{-- Generar proyecto / link a proyecto - condiciones: 
			1. Estar activo el módulo de Proyectos.
			2. Disponer de permisos para generar proyectos.
			...
		--}}
		@if($module_projects)
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
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe presupuesto flotante: --}}
				@php
				$budget_amount_without_discount = $shared->discount > 0? number_format($budget_amount_without_discount, 2):false;
				@endphp

				@livewire('budget-amount', ['budget_amount' => number_format($budget_amount, 2), 'currency' => $currency->symbol, 'budget_amount_without_discount' => $budget_amount_without_discount, 'environment' => 'budgets'])

				<h3 class="mb-3">
					{!! ucfirst(trans('textos.presupuesto')) !!} 
					<u>
						@if($budget->budget_num)
							{{ $budget->budget_num }}
						@else
							{{ $budget->id }}
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
							{{ $budget->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $budget->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Importe --}}
					<label class="mr-5">
						@livewire('budget-amount-mini', ['budget_amount' => $budget_amount, 'currency' => $currency->symbol])
					</label>

					{{-- Modalidad --}}
					<label class="mr-5">
						{{ ucfirst(trans('textos.modalidad')) }}:
  					    <b>@if($budget->modality) {!! App\Helpers\Helpers::budgetModalities($budget->modality) !!} @endif</b>
					</label>

					{{-- Descuento aplicado --}}
					@include('admin.budget_order_shared_fields.partials.data-discount-applied', ['return' => 'budgets'])

					{{-- Pedido --}}
					@include('admin.budget_order_shared_fields.partials.link-order')

					{{-- Proyecto --}}
					@include('admin.budget_order_shared_fields.partials.link-project')
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.presupuesto_cabecera')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			  			<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">Items</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'comments') active @endif">
			  			<a class="nav-link @if($tab == 'comments') active @endif" href="#tab-comments" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.notas')) !!}</a>	
			  		</li>

			  		@if($shared->provider_id == session('currentCompany'))
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

					{{-- Notas: --}}
					<div class="tab-pane fade @if($tab == 'comments') show active @endif" role="tabpanel" id="tab-comments">
						@include('admin.budgets.partials.budget-comments')	
					</div>

					{{-- Plantilla del presupuesto: --}}
					@if($shared->provider_id == session('currentCompany'))
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

{{-- Discount --}}
@include('admin.budget_order_shared_fields.partials.modal-discount', ['return' => 'budgets'])

{{-- Add Project --}}
@can('projects.create')
	@include('admin.projects.partials.modal-add-project')
@endcan

{{-- Modify customer --}}
@if(!$order || $order->percentage_invoiced == 0)
	@include('admin.budget_order_shared_fields.partials.modal-set-customer', ['environment_' => 'budgets'])	
@endif

{{-- Add item --}}
@include('admin.budget_order_shared_fields.partials.modal-add-item', ['environment_' => 'budgets', 'modality' => $budget->modality])

{{-- Edit item --}}
@include('admin.budget_order_shared_fields.partials.modal-edit-item', ['environment_' => 'budgets'])

{{-- See catalogue --}}
@include('admin.budget_order_shared_fields.partials.modal-see-catalogue')

{{-- Change provider --}}
@include('admin.budgets.partials.modal-change-provider')

{{-- Budget modality --}}
@if(!$budget->modality)
	@include('admin.budgets.partials.modal-modality')	
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
});
</script>
@endpush

@endsection