<form method="post" autocomplete="off" action="{{ route('budgets.update', $budget) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de presupuesto --}}
		<label for="pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.presupuesto_num')) !!}</label>
		<div class="col-lg-2">
			{{-- Patrón de numeración si existen patrones y no se trata de una versión --}}
			@if($patterns && !$budget->recid)
				@if($patterns->count())
					@php
					$patterns_ = [];
					foreach($patterns as $ptn){
						$patterns_[$ptn->id] = $ptn->name;
					}
					@endphp

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="budget-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>{{ $budget->budget_num }}</u></b></label>
			@endif

			{{-- Reset de número: 
				1. Sólo si los pedidos vinculados no están facturados 
				2. Sólo para el original, no para las versiones.  Estas se actualizan si cambia el número del original.
			--}}
			@if($budget->budget_num && !$budget->recid)
				<a href="{{ route('budgets.reset-num', $budget->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.numero_reset_confirm') }}">
					<i class="fa fa-redo"></i>
				</a>
			@endif
		</div>

		{{-- Presupuesto nº manual --}}
		<label for="budget_old_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.presupuesto_num_manual')) !!}</label>
		<div class="col-lg-2">
			<x-input type="text" name="budget_old_num" value="{{ $budget->budget_old_num }}" autocomplete="off" maxlength="150" info="budget-old-num"/>
		</div>

		{{-- Pedido --}}
		@if($order)
			<label class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido')) !!}</label>
			<div class="col-md-3 col-lg-2">
				<label class="text-warning mt-2"><b><u>@if($order->order_num) {{ $order->order_num }} @else {{ $order->id }} @endif</u></b></label>
				<a href="{{ route('orders.edit', $order->id) }}" class="ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.pedido_editar')) }}">
					<i class="fa fa-edit"></i>
				</a>

				{{-- @if($order->modality == config('constants.BUDGET_FREE_MODALITY_'))
					<small class="text-warning ml-2">{{ trans('textos.bolsa_pedidos') }}</small>
				@endif --}}
			</div>
		@endif

		{{-- Importe para presupuestos libres --}}
		{{-- @if($budget->modality == config('constants.BUDGET_BASIC_MODALITY_'))
			<label for="budget_free_amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe_bolsa')) !!}</label>
			<div class="col-lg-2">
				<x-input type="text" name="budget_free_amount" class="text-right set-decimals" value="{{ $shared->budget_free_amount }}" autocomplete="off" maxlength="11" group="right" groupvalue="{{ $currency->symbol }}" info="budget-free-amount"/>
			</div>	
		@endif --}}
	</div>

	<div class="form-group row">
		{{-- Presupuesto título --}}
		<label for="name" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.presupuesto_titulo')) }}</label>
		<div class="col-lg-4">
			<x-input type="text" name="name" value="{{ $budget->name }}" autocomplete="off" maxlength="255"/>
		</div>

		{{-- Estado --}}
		<label for="status" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.estado')) }}</label>
		<div class="col-lg-4">
			@php
			$status = App\Helpers\Helpers::budgetStatus();
			@endphp

			<x-select name="status" class="select2" :options="$status" label="estado_selec" value="{{ $budget->status }}"/>
		</div>
	</div>

	{{-- Orden de compra si el presupuesto está aprobado --}}
	@if($budget->status >= config('constants.BUDGET_STATUS_APPROVED_'))
		<div class="form-group row">
			{{-- Orden de compra --}}
			<label for="customer_order" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra')) !!}</label>
			<div class="col-lg-2">
				<x-input type="text" name="customer_order" value="{{ $shared->customer_order }}" autocomplete="off" maxlength="50" info="budget-customer-order"/>
			</div>

			{{-- Documento de compra --}}
			<label for="customer_order_doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra_doc')) !!}</label>
			<div class="col-lg-4">
				@if($shared->customer_order_doc)
					{{-- Descargar archivo --}}
					<a href="{{ asset('img/purchase-orders/'.$shared->customer_order_doc) }}" download="{{ trans('textos.documento_compra') }}" class="btn-download" rel="tooltip" title="{{ trans('textos.archivo_descargar') }}">
						<i class="fa fa-file"></i>
					</a>

					{{-- Eliminar archivo --}}
					<a href="{{ route('budgets.delete-doc', $budget) }}" rel="tooltip" title="{{ trans('textos.archivo_eliminar') }}" class="text-danger ml-3 btn-confirm" data-msg="{{ trans('textos.documento_eliminar_confirm') }}">
						<i class="fa fa-trash"></i>
					</a>
				@else
					<x-input-file name="customer_order_doc"/>

					{!! App\Helpers\Helpers::info('budget-customer-order-doc') !!}

					@if($errors->has('customer_order_doc'))
		                <span class="invalid-feedback" role="alert">
		                  	<strong>{{ $errors->first('customer_order_doc') }}</strong>
		                </span>
		            @endif
		        @endif
			</div>
		</div>
	@endif

	<div class="form-group row">
		{{-- Project Manager --}}
		<label for="provider_manager_id" class="col-lg-2 col-form-label">Project Manager</label>
		<div class="col-lg-4">	
			<x-select name="provider_manager_id" class="select2" :options="$provider_managers" label="manager_selec" value="{{ $shared->provider_manager_id }}"/>
		</div>

		{{-- Manager del cliente --}}
		<label for="provider_manager_id" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.responsable_cliente')) }}</label>
		<div class="col-lg-3">	
			<x-select name="customer_manager_id" class="select2" :options="$customer_managers" label="manager_selec" value="{{ $shared->customer_manager_id }}"/>
		</div>

		{{-- Añadir nuevo responsable de cliente --}}
		<div class="col-md-1 text-center pt-1">
			<a href="#" class="btn btn-warning btn-circle" data-toggle="modal" data-target="#modal-new-customer-manager" rel="tooltip" title="{!! trans('textos.add_nuevo_responsable') !!}">
				<i class="fa fa-plus"></i>
			</a>
		</div>
	</div>

	<div class="form-group row">
		{{-- Sede del proveedor --}}
		<label for="provider_workplace_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa_facturacion')) !!}</label>
		<div class="col-lg-4">
			@if($provider_workplaces->count() > 1)
				@php
				$provider_workplaces_ = [];
				@endphp

				@foreach($provider_workplaces as $pw)
					@php
					$provider_workplaces_[$pw->id] = $pw->name;
					@endphp	
				@endforeach

				<x-select name="provider_workplace_id" class="select2" :options="$provider_workplaces_" label="centro_trabajo_selec" value="{{ $shared->provider_workplace_id }}"/>

			@elseif($provider_workplaces->count() == 1)
				<input type="hidden" name="provider_workplace_id" value="{{ $provider_workplaces[0]->id }}">
				<x-input type="text" name="" value="{{ $provider_workplaces[0]->name }}" disabled autocomplete="off" maxlength="255"/>
			@endif
		</div>

		{{-- Sede del cliente --}}
		<label for="customer_workplace_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.empresa_cliente')) !!}</label>
		<div class="col-lg-4">
			@if($customer_workplaces->count() > 1)
				@php
				$customer_workplaces_ = [];
				@endphp

				@foreach($customer_workplaces as $cw)
					@php
					$customer_workplaces_[$cw->id] = $cw->name;
					@endphp	
				@endforeach

				<x-select name="customer_workplace_id" class="select2" :options="$customer_workplaces_" label="centro_trabajo_selec" value="{{ $shared->customer_workplace_id }}"/>

			@elseif($customer_workplaces->count() == 1)
				<input type="hidden" name="customer_workplace_id" value="{{ $customer_workplaces[0]->id }}">
				<x-input type="text" name="" value="{{ $customer_workplaces[0]->name }}" disabled autocomplete="off" maxlength="255"/>
			@endif
		</div>
	</div>

	<div class="form-group row">
		{{-- Centro de coste del proveedor --}}
		@if($provider_cost_centers->count())
			<label for="provider_cost_center_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_coste_facturacion')) !!}</label>
			<div class="col-lg-4">
				@php
				$provider_cost_centers_ = [];
				@endphp

				@foreach($provider_cost_centers as $pcc)
					@php
					$provider_cost_centers_[$pcc->id] = $pcc->name;
					@endphp	
				@endforeach

				<x-select name="provider_cost_center_id" class="select2" :options="$provider_cost_centers_" label="centro_coste_selec" value="{{ $shared->provider_cost_center_id }}"/>
			</div>
		@endif

		{{-- Centro de coste del cliente --}}
		@if($customer_cost_centers->count())
			<label for="customer_cost_center_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.centro_coste_cliente')) !!}</label>
			<div class="col-lg-4">
				@php
				$customer_cost_centers_ = [];
				@endphp

				@foreach($customer_cost_centers as $ccc)
					@php
					$customer_cost_centers_[$ccc->id] = $ccc->name;
					@endphp	
				@endforeach

				<x-select name="customer_cost_center_id" class="select2" :options="$customer_cost_centers_" label="centro_coste_selec" value="{{ $shared->customer_cost_center_id }}"/>
			</div>
		@endif
	
		{{-- Área de negocio del proveedor --}}
		@if($provider_business_areas->count())
			<label for="provider_business_area_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.area_negocio_proveedor')) !!}</label>
			<div class="col-lg-4">
				@php
				$provider_business_areas_ = [];
				@endphp

				@foreach($provider_business_areas as $pba)
					@php
					$provider_business_areas_[$pba->id] = $pba->name;
					@endphp	
				@endforeach

				<x-select name="provider_business_area_id" class="select2" :options="$provider_business_areas_" label="area_negocio_selec" value="{{ $shared->provider_business_area_id }}"/>
			</div>
		@endif

		{{-- Área de negocio del cliente --}}
		@if($customer_business_areas->count())
			<label for="customer_business_area_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.area_negocio_cliente')) !!}</label>
			<div class="col-lg-4">
				@php
				$customer_business_areas_ = [];
				@endphp

				@foreach($customer_business_areas as $cba)
					@php
					$customer_business_areas_[$cba->id] = $cba->name;
					@endphp	
				@endforeach

				<x-select name="customer_business_area_id" class="select2" :options="$customer_business_areas_" label="area_negocio_selec" value="{{ $shared->customer_business_area_id }}"/>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Cliente final --}}
		<label for="final_customer_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente_final')) !!}</label>
		<div class="col-lg-4">
			@php
			$final_customers_ = [];
			@endphp

			@foreach($final_customers as $fc)
				@php
				$final_customers_[$fc->id] = $fc->name;
				@endphp	
			@endforeach

			<x-select name="final_customer_id" class="select2" :options="$final_customers_" label="cliente_selec" value="{{ $shared->final_customer_id }}"/>
		</div>
	</div>

	{{-- Nombre del archivo --}}
	<div class="form-group row">
		<label for="file_name" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.nombre_archivo')) }}</label>
		<div class="col-lg-4">
			<x-input type="text" name="file_name" value="{{ $budget->file_name }}" autocomplete="off" maxlength="255" info="budget-file-name"/>
		</div>
	</div>

	{{-- Descripción --}}
	<div class="form-group row">
		<label for="description" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.descripcion')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{{ $budget->description }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>