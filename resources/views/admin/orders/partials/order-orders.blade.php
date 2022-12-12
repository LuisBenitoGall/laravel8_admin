<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblOrderGroupOrders">
			<thead>
				<tr>
					<th class="text-center wdt50">#</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.n_manual')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.presupuesto')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.orden_compra')) !!}</th>
					<th class="wdt200">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
					<!-- <th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th> -->
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.descuento')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.total')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.entrega_prevista')) !!}</th>
					<th class="wdt100">{!! trans('textos.centro_coste') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.factura')) !!}</th>
					<th class="wdt100">{!! trans('textos.estado') !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			@php
			$orderStatus = App\Helpers\helpers::orderStatus();
			$groupAmount = 0;
			@endphp

			<tbody>
				@foreach($ordersInGroup as $oig)
					<tr>
						{{-- Número pedido --}}
						<td class="text-right">
							@can('orders.edit')
								<a target="_blank" href="{{ route('orders.edit', $oig->id) }}"> 
							@endcan
								@if($oig->order_num)
									{{ $oig->order_num }}
								@else
									{{ $oig->id }}
								@endif
							@can('orders.edit')
								</a> 
							@endcan
						</td>

						{{-- Nº manual --}}
						<td class="text-right">
							{{ $oig->order_old_num }}
						</td>

						{{-- Presupuesto --}}
						<td class="text-right">
							@can('budgets.edit')
								@if($oig->id)
									<a target="_blank" href="{{ route('budgets.edit', $oig->id) }}"> 
								@endif
							@endcan
								@if($oig->budget_num)
									{{ $oig->budget_num }}
								@else
									{{ $oig->id }}
								@endif
							@can('budgets.edit')
								</a> 
							@endcan
						</td>

						{{-- Orden de compra --}}
						<td class="text-right">
							@if($oig->purchase_num)
								{{ $oig->purchase_num }}
							@elseif($oig->purchase_old_num)
								{{ $oig->purchase_old_num}}
							@endif
						</td>

						{{-- Título pedido --}}
						<td>
							{!! $oig->name !!}
						</td>

						{{-- Importe --}}
						@php
						$amount = $oig->getAmount();
						$groupAmount = $groupAmount + $amount['total_amount'];
						@endphp

						<td class="text-right">
							{{ round($amount['total_amount_without_discounts'], 2) }}{{ $currency->symbol }}
						</td>

						{{-- Descuento --}}
						<td class="text-right">
							@if($oig->discount > 0)
								{{ $oig->discount }}%
							@endif
						</td>

						{{-- Total --}}
						<td class="text-right">
							{{ round($amount['total_amount'], 2) }}{{ $currency->symbol }}
						</td>

						{{-- Fecha entrega prevista --}}
						@php
						$estimated_delivery_date = $oig->estimated_delivery_date? Carbon\Carbon::parse($oig->estimated_delivery_date)->format($languages[session()->get('locale')][4]):'';
						@endphp

						<td class="text-right">
							{!! $estimated_delivery_date !!}
						</td>

						{{-- Centro de coste --}}
						<td>
							{{ $oig->costCenter }}
						</td>

						{{-- Factura --}}
						<td class="text-right">
							@if($oig->invoiceId)
								<a target="_blank" href="{{ route('invoices.edit', $oig->invoiceId) }}">
									@if($oig->invoice_num) {{ $oig->invoice_num }} @else {{ $oig->id }} @endif
								</a>
							@endif	
						</td>

						{{-- Estado --}}
						<td>
							<span class="xoption-{{ $oig->id }}">
								@if(array_key_exists($oig->status, $orderStatus)) 
									{!! $orderStatus[$oig->status] !!}
								@endif
							</span>
						</td>

						{{-- Acciones --}}
						<td class="text-right">
							@can('orders.edit')
                   				{{-- Estado --}}
								<x-select-status :options="$orderStatus" id="{{ $oig->id }}" />

								{{-- Editar --}}
								<a target="_blank" href="{{ route('orders.edit', $oig->id) }}" class="btn btn-xs btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_editar')) !!}">
                            		<i class="fa fa-edit"></i>
                        		</a>

                        		{{-- Eliminar de la agrupación - condiciones 
                        			1. El pedido no debe estar facturado.
                        			2. Debe estar en estado válido para facturación.
									...
                        		--}}
                        		@if(!$oig->invoiced && !in_array($oig->order_status, config('constants.ORDER_STATUS_INVOICED_')))
									<form method="POST" action="{{ route('order-groups.destroy', $oig->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                        			<input name="_method" type="hidden" value="DELETE">
	                        			<input type="hidden" name="return" value="order-group">
	                        			{{ csrf_field() }}
	                        			<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_eliminar_de_grupo')) !!}" data-msg="{!! ucfirst(trans('textos.pedido_confirm_eliminar_grupo')) !!}"><i class="fa fa-trash"></i></button>
	                    			</form>
	                    		@endif
		                    @endcan
						</td>
					</tr>
				@endforeach
			</tbody>	

			<tfoot>
				<tr>
					<td colspan="7" class="text-right"><b>{{ ucfirst(trans('textos.agrupacion_total')) }}</b></td>
					<td class="text-right">{{ round($groupAmount, 2) }}{{ $currency->symbol }}</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>

				{{-- Info bolsa --}}
				@if($order->bag_amount)
					<tr>
						<td colspan="7" class="text-right"><b>{{ ucfirst(trans('textos.bolsa')) }}</b></td>
						<td class="text-right">{{ round($order->bag_amount, 2) }}{{ $currency->symbol }}</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>	
					</tr>

					<tr>
						<td colspan="7" class="text-right"><b>{{ ucfirst(trans('textos.pendiente')) }}</b></td>

						@php
						$pendent = $order->bag_amount - $groupAmount;
						@endphp
						<td class="text-right @if($pendent <= 0) text-danger @endif">{{ round($pendent, 2) }}{{ $currency->symbol }}</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>	
					</tr>
				@endif
			</tfoot>
		</table>
	</div>
</div>