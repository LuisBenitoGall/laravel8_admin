@php
$readonly = $order->customer_id == session('currentCompany')? true:false;
@endphp

<form method="post" autocomplete="off" action="{{ route('budgets.update', $order) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nº de presupuesto --}}
		<label for="pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.presupuesto_num')) !!}</label>
		<div class="col-lg-2">
			@if($order->budget_num)
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>{{ $order->budget_num }}</u></b></label>

			@else
				{{-- Patrón de numeración si existen patrones y no se trata de una versión --}}
				@if($patterns && !$order->recid)
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
				@endif
			
				
			@endif

			{{-- Reset de número: 
				1. Sólo si los pedidos vinculados no están facturados 
				2. Sólo para el original, no para las versiones.  Estas se actualizan si cambia el número del original.
				3. El pedido no debe estar facturado.
				...
			--}}
			@if($order->budget_num && !$order->recid && ($order && !$order->invoiced))
				<a href="{{ route('budgets.reset-num', $order->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.numero_reset_confirm') }}">
					<i class="fa fa-redo"></i>
				</a>
			@endif
		</div>

		{{-- Presupuesto nº manual --}}
		<label for="budget_old_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.presupuesto_num_manual')) !!}</label>
		<div class="col-lg-2">
			<x-input type="text" name="budget_old_num" value="{{ $order->budget_old_num }}" autocomplete="off" maxlength="150" info="budget-old-num" readonly="{{ $readonly }}"/>
		</div>

		{{-- Pedido --}}
		{{-- @if($order)
			<label class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido')) !!}</label>
			<div class="col-md-3 col-lg-2">
				<label class="text-warning mt-2"><b><u>@if($order->order_num) {{ $order->order_num }} @else {{ $order->id }} @endif</u></b></label>
				<a href="{{ route('orders.edit', $order->id) }}" class="ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.pedido_editar')) }}">
					<i class="fa fa-edit"></i>
				</a>
			</div>
		@endif --}}

		{{-- Importe para presupuestos libres --}}
		{{-- @if($order->modality == config('constants.BUDGET_BASIC_MODALITY_'))
			<label for="budget_free_amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe_bolsa')) !!}</label>
			<div class="col-lg-2">
				<x-input type="text" name="budget_free_amount" class="text-right set-decimals" value="{{ $order->budget_free_amount }}" autocomplete="off" maxlength="11" group="right" groupvalue="{{ $currency->symbol }}" info="budget-free-amount"/>
			</div>	
		@endif --}}
	</div>

	@if($order->is_order)
		<div class="form-group row">
			{{-- Orden de compra --}}
			@if($order->purchase_num)
				<label for="purchase_pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra')) !!}</label>
				<div class="col-md-3 col-lg-2">
					<label class="text-warning mt-1" style="font-size: 16px;"><b><u>{{ $order->purchase_num }}</u></b></label>
				</div>
			@endif

			{{-- Orden compra manual --}}
			<label for="old_purchase" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra_antigua')) !!}</label>
			<div class="col-md-3 col-lg-2">
				<x-input type="text" name="purchase_old_num" value="{{ $order->purchase_old_num }}" autocomplete="off" maxlength="150" info="purchase-old-num"/>
			</div>

			{{-- Documento de compra --}}
			<label for="purchase_doc" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra_doc')) !!}</label>
			<div class="col-md-3 col-lg-2">
				@if(!$order->purchase_doc)
					<x-input-file name="doc"/>

					{!! App\Helpers\helpers::info('purchase-doc') !!}

					@if($errors->has('doc'))
		                <span class="invalid-feedback" role="alert">
		                  	<strong>{{ $errors->first('doc') }}</strong>
		                </span>
		            @endif
		        @else
		        	<div class="pt-2">
		        		{{-- Ver documento --}}
			        	<a target="_blank" class="text-info" href="{{ asset('img/purchase-orders/'.$order->purchase_doc) }}" rel="tooltip" title="{{ trans('textos.documento_ver') }}">
			        		<i class="fa fa-file"></i>
			        	</a>

			        	{{-- Eliminar documento --}}
			        	<a target="_blank" class="text-danger ml-3 btn-confirm" href="{{ route('orders.remove-purchase-doc', $order) }}" rel="tooltip" title="{{ trans('textos.documento_eliminar') }}" data-msg="{{ trans('textos.documento_eliminar_confirm') }}">
			        		<i class="fa fa-trash"></i>
			        	</a>
		        	</div>
		        @endif
			</div>
		</div>
	@endif

	<div class="form-group row">
		{{-- Presupuesto título --}}
		<label for="name" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.presupuesto_titulo')) }}</label>
		<div class="col-lg-4">
			<x-input type="text" name="name" value="{!! $order->name !!}" autocomplete="off" readonly="{{ $readonly }}" maxlength="255"/>
		</div>

		{{-- Estado - condiciones: 
			1. Pedido no facturado.
		--}}
		<label for="status" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.estado')) }}</label>
		<div class="col-lg-4">
			@php
			$estados = App\Helpers\helpers::budgetStatus();
			$disabled = $order->invoiced || $order->customer_id == session('currentCompany')? true:false;
			$name = !$disabled? 'status':'';	//En pedidos facturados no se modifica el status.
			@endphp

			<x-select name="{{ $name }}" class="select2" :options="$estados" label="estado_selec" value="{{ $order->budget_status }}" disabled="{{ $disabled }}"/>
		</div>
	</div>

	{{-- Orden de compra si el presupuesto está aprobado --}}
	@if($order->status >= config('constants.BUDGET_STATUS_APPROVED_'))
		<div class="form-group row">
			{{-- Orden de compra --}}
			<label for="customer_order" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra')) !!}</label>
			<div class="col-lg-2">
				<x-input type="text" name="customer_order" value="{{ $order->customer_order }}" autocomplete="off" maxlength="50" info="budget-customer-order"/>
			</div>

			{{-- Documento de compra --}}
			<label for="customer_order_doc" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra_doc')) !!}</label>
			<div class="col-lg-4">
				@if($order->customer_order_doc)
					{{-- Descargar archivo --}}
					<a href="{{ asset('img/purchase-orders/'.$order->customer_order_doc) }}" download="{{ trans('textos.documento_compra') }}" class="btn-download" rel="tooltip" title="{{ trans('textos.archivo_descargar') }}">
						<i class="fa fa-file"></i>
					</a>

					{{-- Eliminar archivo --}}
					<a href="{{ route('budgets.delete-doc', $order) }}" rel="tooltip" title="{{ trans('textos.archivo_eliminar') }}" class="text-danger ml-3 btn-confirm" data-msg="{{ trans('textos.documento_eliminar_confirm') }}">
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
			@php
			$disabled = $order->customer_id == session('currentCompany')? true:false;
			@endphp
			<x-select name="provider_manager_id" class="select2" :options="$provider_managers" label="manager_selec" value="{{ $order->provider_manager_id }}" disabled="{{ $disabled }}"/>
		</div>

		{{-- Manager del cliente --}}
		<label for="customer_manager_id" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.responsable_cliente')) }}</label>
		<div class="col-lg-3">	
			<x-select name="customer_manager_id" class="select2" :options="$customer_managers" label="manager_selec" value="{{ $order->customer_manager_id }}"/>
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
				$disabled = $order->customer_id == session('currentCompany')? true:false;
				$workplace_selected = $order->provider_workplace_id? $order->provider_workplace_id:($provider_workplace? $provider_workplace->workplace_id:'');
				@endphp

				@foreach($provider_workplaces as $pw)
					@php
					$provider_workplaces_[$pw->id] = $pw->name;
					@endphp	
				@endforeach

				<x-select name="provider_workplace_id" class="select2" :options="$provider_workplaces_" label="centro_trabajo_selec" value="{{ $workplace_selected }}" disabled="{{ $disabled }}"/>

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

				<x-select name="customer_workplace_id" class="select2" :options="$customer_workplaces_" label="centro_trabajo_selec" value="{{ $order->customer_workplace_id }}"/>

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
				$disabled = $order->customer_id == session('currentCompany')? true:false;
				@endphp

				@foreach($provider_cost_centers as $pcc)
					@php
					$provider_cost_centers_[$pcc->id] = $pcc->name;
					@endphp	
				@endforeach

				<x-select name="provider_cost_center_id" class="select2" :options="$provider_cost_centers_" label="centro_coste_selec" value="{{ $order->provider_cost_center_id }}" disabled="{{ $disabled }}"/>
			</div>
		@else
			<div class="col-lg-6"></div>	
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

				<x-select name="customer_cost_center_id" class="select2" :options="$customer_cost_centers_" label="centro_coste_selec" value="{{ $order->customer_cost_center_id }}"/>
			</div>
		@endif
	</div>
	
	<div class="form-group row">
		{{-- Área de negocio del proveedor --}}
		@if($provider_business_areas->count())
			<label for="provider_business_area_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.area_negocio_proveedor')) !!}</label>
			<div class="col-lg-4">
				@php
				$provider_business_areas_ = [];
				$disabled = $order->customer_id == session('currentCompany')? true:false;
				@endphp

				@foreach($provider_business_areas as $pba)
					@php
					$provider_business_areas_[$pba->id] = $pba->name;
					@endphp	
				@endforeach

				<x-select name="provider_business_area_id" class="select2" :options="$provider_business_areas_" label="area_negocio_selec" value="{{ $order->provider_business_area_id }}" disabled="{{ $disabled }}"/>
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

				<x-select name="customer_business_area_id" class="select2" :options="$customer_business_areas_" label="area_negocio_selec" value="{{ $order->customer_business_area_id }}"/>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Cliente final --}}
		<label for="final_customer_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.cliente_final')) !!}</label>
		<div class="col-lg-4">
			@if($final_customers->count())
				@php
				$final_customers_ = [];
				@endphp

				@foreach($final_customers as $fc)
					@php
					$final_customers_[$fc->id] = $fc->name;
					@endphp	
				@endforeach

				<x-select name="final_customer_id" class="select2" :options="$final_customers_" label="cliente_selec" value="{{ $order->final_customer_id }}"/>
			@else
				<p class="text-warning pt-2">{!! trans('textos.cliente_final_no_encontrados') !!}</p>
			@endif
		</div>
	</div>

	{{-- Nombre del archivo --}}
	<div class="form-group row">
		<label for="budget_file_name" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.nombre_archivo')) }}</label>
		<div class="col-lg-4">
			<x-input type="text" name="budget_file_name" value="{!! $order->budget_file_name !!}" autocomplete="off" maxlength="255" info="budget-file-name" readonly="{{ $readonly }}"/>
		</div>
	</div>

	{{-- Descripción --}}
	<div class="form-group row">
		<label for="description" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.descripcion')) }}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{{ $order->description }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>