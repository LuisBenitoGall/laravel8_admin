<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblInvoiceOrders">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.n_manual')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
					<th class="wdt200">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
					<!-- <th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th> -->
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="wdt100">{!! trans('textos.centro_coste') !!}</th>
					<th class="wdt100">{!! trans('textos.estado') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			@php
			$orderStatus = App\Helpers\helpers::orderStatus();
			@endphp

			<tbody>
				@foreach($suborders as $so)
					<tr>
						{{-- Número pedido --}}
						<td class="text-right">
							@can('orders.edit')
								<a target="_blank" href="{{ route('orders.edit', $so->id) }}"> 
							@endcan
								@if($so->order_num)
									{{ $so->order_num }}
								@else
									{{ $so->id }}
								@endif
							@can('orders.edit')
								</a> 
							@endcan
						</td>

						{{-- Nº manual --}}
						<td class="text-right">
							{{ $so->order_old_num }}
						</td>

						{{-- Presupuesto --}}
						<td class="text-right">
							@can('budgets.edit')
								@if($so->id)
									<a target="_blank" href="{{ route('budgets.edit', $so->id) }}"> 
								@endif
							@endcan
								@if($so->budget_num)
									{{ $so->budget_num }}
								@else
									{{ $so->id }}
								@endif
							@can('budgets.edit')
								</a> 
							@endcan
						</td>

						{{-- Orden de compra --}}
						<td class="text-right">
							@if($so->purchase_num)
								{{ $so->purchase_num }}
							@elseif($so->purchase_old_num)
								{{ $so->purchase_old_num}}
							@endif
						</td>

						{{-- Título pedido --}}
						<td>
							{!! $so->name !!}
						</td>

						{{-- Importe --}}
						@php
						$amount = $so->getAmount();
						@endphp

						<td class="text-right">
							{{ round($amount['amount_with_discount'], 2) }}{{ $currency->symbol }}
						</td>

						{{-- Fecha entrega prevista --}}
						@php
						$estimated_delivery_date = Carbon\Carbon::parse($so->estimated_delivery_date)->format($languages[session()->get('locale')][4]);
						@endphp

						<td class="text-right">
							{!! $estimated_delivery_date !!}
						</td>

						{{-- Centro de coste --}}
						<td>
							{{ $so->costCenter }}
						</td>

						{{-- Estado --}}
						<td>
							@if(array_key_exists($so->order_status, $orderStatus)) 
								{!! ucfirst($orderStatus[$so->order_status]) !!}
							@endif
						</td>

						{{-- Acciones --}}
						<td class="text-right">
							@can('orders.edit')
								{{-- Editar --}}
								<a target="_blank" href="{{ route('orders.edit', $so->id) }}" class="btn btn-xs btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_editar')) !!}">
                            		<i class="fa fa-edit"></i>
                        		</a>
		                    @endcan

		                    @can('invoices.edit')
		                    	{{-- Eliminar pedido de la factura --}}
		                    	@if(!$invoice->blocked)
			                    	<a target="_blank" href="{{ route('invoices.remove-from-invoice', [$invoice->id, $so->id]) }}" class="btn btn-xs btn-danger close-animation btn-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.factura_agrupacion_eliminar_pedido')) !!}" data-msg="{!! ucfirst(trans('textos.factura_agrupacion_eliminar_pedido_confirm')) !!}">
	                            		<i class="fa fa-trash"></i>
	                        		</a>
	                        	@endif
		                    @endcan
						</td>
					</tr>
				@endforeach
			</tbody>	
		</table>
	</div>
</div>