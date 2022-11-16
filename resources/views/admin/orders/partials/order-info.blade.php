@php
//Acceso exclusivo proveedor:
$read_only_provider = $order->customer_id == session('currentCompany')? true:false;
@endphp

<form method="post" autocomplete="off" id="frmOrderInfo" action="{{ route('orders.update', $order) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{ $order->id }}">

	<div class="form-group row">
		{{-- Nº de pedido --}}
		<label for="pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_num')) !!}</label>
		<div class="col-md-3 col-lg-2">
			{{-- Patrón de numeración - condiciones:
			 	1. Si existen patrones.
			 	2. No se trata de una versión.	02/07/22: condición omitida por el momento: && !$order->recid
				3. Exclusivo para proveedor.
				4. No existe order_num.
				...
			--}}
			@if($patterns && $order->provider_id == session('currentCompany') && !$order->order_num)
				@if($patterns->count())
					@php
					$patterns_ = [];
					foreach($patterns as $ptn){
						$patterns_[$ptn->id] = $ptn->name;
					}
					@endphp

					<x-select name="pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="order-pattern-num"/>
				@else
					<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>
					@if($order->order_num)
						{{ $order->order_num }}
					@else
						{{ $order->id }}
					@endif
				</u></b></label>
			@endif

			{{-- Reset de número - condiciones:
				1. Sólo si los pedidos vinculados no están facturados
				2. Sólo para el original, no para las versiones.  Estas se actualizan si cambia el número del original.		&& !$order->recid
				3. Exclusivo para proveedor.
			--}}
			@if(!$order->invoiced && $order->order_num && $order->provider_id == session('currentCompany'))
				<a href="{{ route('orders.reset-num', $order->id) }}" class="ml-3 btn-confirm" rel="tooltip" title="{{ trans('textos.numero_reset') }}" data-msg="{{ trans('textos.numero_reset_confirm') }}">
					<i class="fa fa-redo"></i>
				</a>
			@endif
		</div>

		{{-- Pedido nº manual --}}
		<label for="order_old_num" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_num_manual')) !!}</label>
		<div class="col-md-3 col-lg-2">
			<x-input type="text" name="order_old_num" value="{{ $order->order_old_num }}" autocomplete="off" maxlength="150" info="order-old-num" readonly="{{ $read_only_provider }}"/>
		</div>

		{{-- Importe de la bolsa - condiciones:
			1. Si es agrupación.
		--}}
		@if($order->is_group)
			<label for="bag_amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe_bolsa')) !!}</label>
			<div class="col-lg-2">
				<x-input type="text" name="bag_amount" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" value="{{ $order->bag_amount }}" groupvalue="{{ $currency->symbol }}" info="order-group-bag"/>
			</div>
		@endif

		{{-- Presupuesto --}}
		{{-- @if($budget)
			<label class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.presupuesto')) !!}</label>
			<div class="col-md-3 col-lg-2">
				<label class="text-warning mt-2"><b><u>@if($budget->budget_num) {{ $budget->budget_num }} @else {{ $budget->id }} @endif</u></b></label>
				<a href="{{ route('budgets.edit', $budget->id) }}" class="ml-2" rel="tooltip" title="{{ ucfirst(trans('textos.presupuesto_editar')) }}">
					<i class="fa fa-edit"></i>
				</a>

				@if($budget->modality == config('constants.BUDGET_FREE_MODALITY_'))
					<small class="text-warning ml-2">{{ trans('textos.bolsa_pedidos') }}</small>
				@endif
			</div>
		@endif --}}
	</div>

	<div class="form-group row">
		{{-- Orden de compra --}}
		<label for="purchase_pattern_id" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_compra')) !!}</label>
		<div class="col-md-3 col-lg-2">
			@if(!$order->purchase_num)
				{{-- El número de orden de compra lo genera el cliente --}}
				@if($order->customer_id == session('currentCompany'))
					@if(count($purchase_patterns))
						<x-select name="purchase_pattern_id" class="select2" label="patron_selecciona" :options="$purchase_patterns"/>
					@else
						<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
					@endif
				@else
					<label class="text-warning mt-2">{{ trans('textos.cliente_genera_orden_compra') }}</label>
				@endif

			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>{{ $order->purchase_num }}</u></b></label>
			@endif
		</div>

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

	<div class="form-group row">
		{{-- Confirmación de pedido, exclusivamente por el cliente: --}}
		@if($order->customer_id == session('currentCompany') && !$order->customer_confirmation)
			<label for="customer_confirmation" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_confirmar')) !!}</label>
			<div class="col-lg-1">
				<label class="custom-chk-rdb" rel="tooltip">
	                <input type="checkbox" name="customer_confirmation">
	                <span class="chk" style="top: 5px;"></span>
	            </label>

	            <div class="position-absolute" style="left: 65px; top: 0;">
	            	{!! App\Helpers\helpers::info('order-purchase-confirm') !!}
	            </div>
			</div>
		@endif

		{{-- Orden de pago --}}
		<label for="pay_order" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.orden_pago')) !!}</label>
		<div class="col-lg-2">
			@php
			$readonly = !in_array($order->order_status, config('constants.ORDER_GROUPS_OPENED_'))? true:false;
			@endphp

			<x-input type="text" name="pay_order" value="{{ $order->pay_order }}" autocomplete="off" maxlength="100" readonly="{{ $readonly }}" info="order-group-pay-order"/>
		</div>

		{{--
		Fecha orden de pago
		$order->pay_order_received && $order->pay_order_received > 0? \Carbon\Carbon::parse($order->pay_order_received)->format($languages[session()->get('locale')][4]):'';

		--}}
		@if($order->pay_order_received)
			@php
			$pay_order_received = optional($order->pay_order_received)->format($languages[session()->get('locale')][4]) ?? '';
			@endphp

			<label class="col-lg-2 col-form-label">{{ trans('textos.recibida') }}</label>
			<div class="col-lg-2">
				<x-input type="text" name="pay_order_received" class="text-right" value="{{ $pay_order_received }}" autocomplete="off" readonly="true" info="order-group-pay-order-received"/>
			</div>
		@endif
	</div>

	<div class="form-group row">
		{{-- Título del pedido --}}
		<label for="name" class="col-md-3 col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_titulo')) !!}</label>
		<div class="col-md-5 col-lg-6">
			<x-input type="text" name="name" value="{!! $order->name !!}" autocomplete="off" maxlength="255" readonly="{{ $read_only_provider }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Fecha entrega prevista --}}
		<label for="estimated_delivery_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_entrega_prevista')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$estimatedDeliveryDate = $order->estimated_delivery_date != ''? \Carbon\Carbon::parse($order->estimated_delivery_date)->format($languages[session()->get('locale')][4]):'';
			@endphp

			@if($order->provider_id == session('currentCompany'))
				<x-date-picker name="estimated_delivery_date" id="estimated_delivery_date" value="{{ $estimatedDeliveryDate }}" :format="$languages[config('app.locale')][4]"/>
			@else
				<x-input type="text" name="estimated_delivery_date" value="{{ $estimatedDeliveryDate }}" autocomplete="off" maxlength="25" readonly/>
			@endif
		</div>

		{{-- Fecha entrega --}}
		<label for="delivery_date" class="col-md-2 col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_entrega')) !!}</label>
		<div class="col-md-4 col-lg-2">
			@php
			$deliveryDate = $order->delivery_date != ''? \Carbon\Carbon::parse($order->delivery_date)->format($languages[session()->get('locale')][4]):'';
			@endphp

			@if($order->provider_id == session('currentCompany'))
				<x-date-picker name="delivery_date" id="delivery_date" value="{{ $deliveryDate }}" :format="$languages[config('app.locale')][4]" readonly="{{ $read_only_provider }}"/>
			@else
				<x-input type="text" name="delivery_date" value="{{ $deliveryDate }}" autocomplete="off" maxlength="255" readonly/>
			@endif
		</div>

		{{-- Estado pedido - condiciones:
			1. Exclusivo proveedor
			2. Pedido no facturado
		--}}
		@if($order->provider_id == session('currentCompany'))
			<label for="order_status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
			<div class="col-lg-2">
				@php
				//Los estados se obtienen en función de si el pedido es simple o agrupación:
				$estados = $order->is_group? App\Helpers\helpers::orderGroupStatus():App\Helpers\helpers::orderStatus();
				$disabled = $order->invoiced? true:false;
				$name = !$disabled? 'status':'';	//En pedidos facturados no se modifica el status.
				@endphp

				{{-- 18/10/22: resolver estado de pedido para que quede disabled cuando estén todas las previsiones de facturación facturadas --}}
				{{-- disabled="{{ $disabled }}" --}}

				<x-select name="{{ $name }}" class="select2" label="indica_estado" :options="$estados" value="{{ $order->order_status }}" />
			</div>
		@else
			<input type="hidden" name="order_status" value="{{ $order->order_status }}">
		@endif
	</div>
	<br><hr><br>

	{{-- Facturación prevista - condiciones:
		1. Exclusivo para el proveedor.
		2. Para agrupaciones o pedidos simples, NO para pedidos vinculados a agrupación.
	--}}
	@if($order->provider_id == session('currentCompany') && !$order->order_parent)
		<div class="form-group row">
			<label class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.facturacion_prevista')) !!}</label>

			<div class="col-lg-1 pt-1">
				<button class="btn btn-warning btn-circle btnAddPrevision" rel="tooltip" title="{{ trans('textos.agregar_prevision') }}">
					<i class="fa fa-plus"></i>
				</button>
			</div>

			<div class="col-lg-9">
				<div class="previsions">
					@php
					$totalPercent = 0;
					@endphp

					@if($previsions->count())
						{{-- La sumas de los porcentajes de las previsiones de facturación no puede superar el 100% --}}

						@foreach($previsions as $prev)
							@php
							$totalPercent = $totalPercent + $prev->percent;
							$disabled = $prev->invoice_id? 'disabled':'';
							$disabled_ = $prev->invoice_id? true:false;
							@endphp

							<div class="form-group row prevision">
								@if(!$disabled_)
									<input type="hidden" name="op_id[]" value="{{ $prev->id }}">
								@endif

								{{-- Año --}}
								<label for="year" class="col-md-2 col-lg-1 col-form-label mb-2">{!! ucfirst(trans('textos.any')) !!}</label>
								<div class="col-md-4 col-lg-2 sinpadding">
									<select name="year[]" class="form-control select2 prevision-data" {{ $disabled }}>
										@for($y=config('constants.MIN_YEAR_'); $y<=(date('Y')+3); $y++)
											@php
											$selected = $prev->year == $y? 'selected':date('Y');
											@endphp

											<option value="{{ $y }}" {{ $selected }}>{{ $y }}</option>
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
									$months = App\Helpers\helpers::yearMonths();
									@endphp

									<select name="month[]" class="form-control select2 prevision-data" {{ $disabled }}>
										@if(!$prev->month)
											<option value="">{{ trans('textos.mes_selec') }}</option>
										@else
											<option value="">{{ trans('textos.sin_definir') }}</option>
										@endif

										@foreach($months as $i => $month)
											@php
											$selected = $prev->month == $i? 'selected':'';
											@endphp

											<option value="{{ $i }}" {{ $selected }}>{{ $month }}</option>
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
								<div class="col-md-4 col-lg-2 sinpadding text-right">
									@php
									$percent = round($totalPercent, 2) > 100? '':$prev->percent;

									// Porcentaje del monto total
									$orden_w =  round(($percent*$order_amount)/100, 3);
									$orden_wo = round(($percent*$order_amount_without_discount)/100, 3);
									
									@endphp

									<x-input type="text" name="percent[]" class="text-right set-decimals percent prevision-data" autocomplete="off" maxlength="5" value="{{ $percent }}" group="right" groupvalue="%" required disabled="{{ $disabled_ }}"/>

									{{-- Monto de la orden, aplicando el porcentaje de cobro --}}
									<small data-container="body" data-toggle="popover" data-placement="top" data-content="{{trans('textos.facturacion_prevista_info_percent')}}">
										{{$orden_w}}{{ $currency['symbol'] }} ({{$orden_wo}}{{ $currency['symbol'] }}) <li class="fa fa-info-circle"></li>
									</small>

						            @if(round($totalPercent, 2) > 100)
						            	<span class="invalid-feedback" role="alert">
						                  	<strong>{{ trans('textos.porcentaje_factura_error') }}</strong>
						                </span>
						            @endif
								</div>

								@if($previsions->count() > 1 && !$disabled_)
									<div class="col-md-1 col-lg-1 pt-2">
										<a href="{{ route('order-prevision.delete', $prev->id) }}" class="btn btn-danger btn-circle btn-circle-mini btn-confirm" rel="tooltip" title="{{ trans('textos.prevision_eliminar') }}" data-msg="{{ trans('textos.prevision_eliminar_aviso') }}">
											<i class="fa fa-times"></i>
										</a>
									</div>
								@endif

								{{-- Factura --}}
								@if($prev->invoiceId)
									<div class="col-md-1 col-lg-2 pt-2">
										{{ ucfirst(trans('textos.factura')) }}
										<a target="_blank" href="{{ route('invoices.edit', $prev->invoiceId) }}">
											@if($prev->invoice_num)
												{{ $prev->invoice_num }}
											@else
												{{ $prev->invoiceId }}
											@endif
										</a>
									</div>
								@endif
							</div>
						@endforeach
					@endif

					{{-- Indicador de porcentaje pendiente
						Pasamos como condición adicional que el porcentaje sea mayor a 0.02, para evitar decimales residuales.
					--}}
					@if($totalPercent < 100 && (100 - $totalPercent) > 0.02)
						<p class="text-warning">{!! trans('textos.facturacion_prevision_pendiente') !!}: <b>{{ round(100 - $totalPercent, 2) }} %</b></p>
					@endif
				</div>
			</div>
		</div>
		<hr><br>
	@endif

	<div class="row">
		{{-- Info cliente --}}
		<div class="col-lg-6">
			<h4 class="text-center">{{ ucfirst(trans('textos.cliente')) }}</h4>

			{{-- Manager cliente --}}
			<div class="form-group row">
				<label for="customer_manager_id" class="col-lg-3 col-form-label">Manager {!! trans('textos.cliente') !!}</label>
				<div class="col-lg-9">
					<x-select name="customer_manager_id" class="select2" label="manager_selec" :options="$customer_managers" value="{{ $order->customer_manager_id }}"/>
				</div>
			</div>

			{{-- Centros de trabajo cliente --}}
			<div class="form-group row">
				<label for="customer_workplace_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.centro_trabajo')) !!}</label>
				<div class="col-lg-9">
					@php
					$workplace_selected = $order->customer_workplace_id? $order->customer_workplace_id:$customer_settings->workplace_id;
					@endphp

					<x-select name="customer_workplace_id" class="select2" label="centro_trabajo_selec" :options="$customer_workplaces" value="{{ $workplace_selected }}"/>
				</div>
			</div>

			{{-- Área negocio cliente --}}
			<div class="form-group row">
				<label for="customer_business_area_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.area_negocio')) !!}</label>
				<div class="col-lg-9">
					@php
					$ba_selected = $order->customer_business_area_id? $order->customer_business_area_id:$customer_settings->ba_id;
					@endphp

					<x-select name="customer_business_area_id" class="select2" label="area_negocio_selec" :options="$customer_business_areas" value="{{ $ba_selected }}"/>
				</div>
			</div>

			{{-- Centros de coste cliente --}}
			<div class="form-group row">
				<label for="customer_cost_center_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.centro_coste')) !!}</label>
				<div class="col-lg-9">
					@php
					$cost_center_selected = $order->customer_cost_center_id? $order->customer_cost_center_id:$customer_settings->cc_id;
					@endphp

					<x-select name="customer_cost_center_id" class="select2" label="centro_coste_selec" :options="$customer_cost_centers" value="{{ $cost_center_selected }}"/>
				</div>
			</div>

			{{-- Producto del proveedor --}}
			<div class="form-group row">
				<label for="customer_products" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cliente_producto')) !!}</label>
				<div class="col-lg-9">
					@php
					$customerProducts = $order->customer_products? unserialize($order->customer_products):[];

					if(!is_array($customerProducts)){
						$customerProducts = [$customerProducts];
					}
					@endphp

					<x-select name="customer_products[]" class="select2" :options="$customer_products" :values="$customerProducts" multiple/>
				</div>
			</div>

			{{-- Referencia del cliente --}}
			<div class="form-group row">
				<label for="customer_items" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.cliente_referencia')) !!}</label>
				<div class="col-lg-9">
					<x-input type="text" name="customer_items" autocomplete="off" value="{{ $order->customer_items }}"/>
				</div>
			</div>
		</div>

		{{-- Info proveedor --}}
		<div class="col-lg-6">
			<h4 class="text-center">{{ ucfirst(trans('textos.proveedor')) }}</h4>

			{{-- Manager proveedor --}}
			<div class="form-group row">
				<label for="provider_manager_id" class="col-lg-3 col-form-label">Manager {!! trans('textos.proveedor') !!}</label>
				<div class="col-lg-9">
					@if($order->provider_id == session('currentCompany'))
						<x-select name="provider_manager_id" class="select2" label="manager_selec" :options="$provider_managers" value="{{ $order->provider_manager_id }}"/>
					@else
						@php
						$provider_manager_id = $order->provider_manager_id && isset($provider_managers[$order->provider_manager_id])? $provider_managers[$order->provider_manager_id]:'';
						@endphp
						<x-input type="text" name="" value="{{ $provider_manager_id }}" autocomplete="off" maxlength="255" disabled/>
					@endif
				</div>
			</div>

			{{-- Centros de trabajo proveedor --}}
			<div class="form-group row">
				<label for="provider_workplace_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.centro_trabajo')) !!}</label>
				<div class="col-lg-9">
					@if($order->provider_id == session('currentCompany'))
						<x-select name="provider_workplace_id" class="select2" label="centro_trabajo_selec" :options="$provider_workplaces" value="{{ $order->provider_workplace_id }}"/>
					@else
						@php
						$provider_workplace_id = $order->provider_workplace_id? $provider_workplaces[$order->provider_workplace_id]:'';
						@endphp
						<x-input type="text" name="" value="{{ $provider_workplace_id }}" autocomplete="off" maxlength="255" disabled/>
					@endif
				</div>
			</div>

			{{-- Área negocio proveedor --}}
			<div class="form-group row">
				<label for="provider_business_area_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.area_negocio')) !!}</label>
				<div class="col-lg-9">
					@if($order->provider_id == session('currentCompany'))
						<x-select name="provider_business_area_id" class="select2" label="area_negocio_selec" :options="$provider_business_areas" value="{{ $order->provider_business_area_id }}"/>
					@else
						@php
						$provider_business_area_id = $order->provider_business_area_id? $provider_workplaces[$order->provider_business_area_id]:'';
						@endphp
						<x-input type="text" name="" value="{{ $provider_business_area_id }}" autocomplete="off" maxlength="255" disabled/>
					@endif
				</div>
			</div>

			{{-- Centros de coste proveedor --}}
			<div class="form-group row">
				<label for="provider_cost_center_id" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.centro_coste')) !!}</label>
				<div class="col-lg-9">
					@if($order->provider_id == session('currentCompany'))
						<x-select name="provider_cost_center_id" class="select2" label="centro_coste_selec" :options="$provider_cost_centers" value="{{ $order->provider_cost_center_id }}"/>
					@else
						@php
						$provider_cost_center_id = $order->provider_cost_center_id? $provider_workplaces[$order->provider_cost_center_id]:'';
						@endphp
						<x-input type="text" name="" value="{{ $provider_cost_center_id }}" autocomplete="off" maxlength="255" disabled/>
					@endif
				</div>
			</div>

			@if($order->provider_id == session('currentCompany'))
				{{-- Horas --}}
				<div class="form-group row">
					<label for="hours" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.horas')) !!}</label>
					<div class="col-lg-9">
						<x-input type="text" name="hours" value="{{ $order->hours }}" autocomplete="off" maxlength="255"/>
					</div>
				</div>

				{{-- Horas extra --}}
				<div class="form-group row">
					<label for="hours_extra" class="col-lg-3 col-form-label">{!! ucfirst(trans('textos.horas_extra')) !!}</label>
					<div class="col-lg-9">
						<x-input type="text" name="hours_extra" value="{{ $order->hours_extra }}" autocomplete="off" maxlength="255"/>
					</div>
				</div>
			@endif
		</div>
	</div>
	<br>

	{{-- Opciones proveedor --}}
	@if($order->provider_id == session('currentCompany'))
		{{-- Mostrar observaciones del cliente al proveedor --}}
		<div class="form-group row">
			<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones_cliente')) !!}</label>
			<div class="col-lg-10">
				{!! $order->observations_customer !!}
			</div>
		</div>

		{{-- Briefing --}}
		<div class="form-group row">
			<label for="briefing" class="col-lg-2 col-form-label">Briefing</label>
			<div class="col-lg-10">
				<x-input-textarea name="briefing" class="editor" value="{{ $order->briefing }}"/>
			</div>
		</div>
		<br>

		{{-- Observaciones --}}
		<div class="form-group row">
			<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
			<div class="col-lg-10">
				<x-input-textarea name="observations" class="editor" value="{{ $order->observations }}"/>
			</div>
		</div>
	@endif

	{{-- Opciones cliente --}}
	@if($order->customer_id == session('currentCompany'))
		{{-- Observaciones --}}
		<div class="form-group row">
			<label for="observations_customer" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
			<div class="col-lg-10">
				<x-input-textarea name="observations_customer" class="editor" value="{{ $order->observations_customer }}"/>
			</div>
		</div>
	@endif

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>

