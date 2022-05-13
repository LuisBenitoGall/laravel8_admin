<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<table class="table table-hover dTable" id="tblOrderGroupsOrders">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.numero_antiguo')) !!}</th>
						<th class="wdt250">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
						{{-- <th class="text-center">{!! ucfirst(trans('textos.fecha')) !!}</th> --}}
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
						{{-- <th>{!! trans('textos.area_negocio') !!}</th> --}}
						<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
						{{-- <th>{!! trans('textos.centro_trabajo') !!}</th> --}}
						<th class="wdt100">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt100">{!! trans('textos.factura') !!}</th>
						<th class="text-center wdt150">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				@php
				$i = 1;
				$totalAmount = 0;
				$totalDiscountAmount = 0;
				@endphp

				<tbody>
					{{-- Pedidos del grupo: --}}
					@foreach($orders as $order)
						@php
						$amount = $order->amount();
						$totalAmount = $totalAmount + $amount[0];
						$totalAmountByOrderInvoices = 0;		//Cálculo a partir del importe de las facturas de cada pedido.
						@endphp

						<tr>
							<td class="text-right @if($order->status == 0) text-soft @endif">
								@php
								$num = $order->order_num? $order->order_num:$order->id;
								@endphp
								<a target="_blank" href="{{ route('orders.edit', $order->id) }}">{{ $num }}</a>
							</td>
							<td class="text-right @if($order->status == 0) text-soft @endif">
								{{ $order->old_order }}
							</td>
							<td @if($order->status == 0) class="text-soft" @endif>{{ $order->name }}</td>
							<td class="text-right @if($order->status == 0) text-soft @endif">
								@php
        						$fechaEntrega = $order->delivery_date && $order->delivery_date != config('constants.DATE_NULL_')? Carbon\Carbon::parse($order->delivery_date)->format(config('locale.languages.'.config('app.locale'))[4]):'';
        						@endphp

        						{{ $fechaEntrega }}	
							</td>

							<td class="text-right">{{ number_format($amount[2], 2) }}{{ $currency }}</td>

							{{-- Estado del pedido: --}}
							<td @if($order->status == 0) class="text-soft" @endif>
								@php
								//$status = $order->status? App\Helpers\Helpers::orderStatus($order->status):'';
								$statuses = App\Helpers\Helpers::orderStatus();	
								@endphp

								<select name="" class="form-control select2 comboEstado reload">
									@foreach($statuses as $idx => $st)
										@php
										$selected = $idx == $order->status? 'selected':'';
										@endphp
										<option value="{{ $idx }}" {{ $selected }} data-orderid="{{ $order->id }}">{{ ucfirst($st) }}</option>
									@endforeach
								</select>
							</td>

							{{-- Nº factura --}}
							<td class="text-right">
								@php
								$invoiced = false;
								@endphp

								@if($order->invoiced)
									@php
									//Facturas del pedido:
									$invoices_ = $order->invoicesOrderGroup($group->id);

									//dd($invoices_);
									@endphp

									@if($invoices_->count() > 0)
										@php
										$invoiced = true;
										@endphp

										@foreach($invoices_ as $inv)
											{{-- Comprobamos que la factura esté entre las facturas del pedido --}}
											{{-- @if($inv->invoices)
												@php
												$order_invoices = unserialize($inv->invoices);
												$arr_order_invoices = [];
												$order_invoices_amount = 0;
												@endphp

												@foreach($order_invoices as $oi)
													@php
													array_push($arr_order_invoices, $oi['invoice']);
													$order_invoices_amount = $order_invoices_amount + $oi['amount'];
													@endphp
												@endforeach

												@if(in_array($inv->id, $arr_order_invoices))
													<a href="{{ route('invoices.edit', $inv->id) }}" class="ml-3">
														@if($inv->invoice_num) {{ $inv->invoice_num }} @else {{ $inv->id }} @endif
													</a>
												@endif

												@php
												$totalAmountByOrderInvoices = $totalAmountByOrderInvoices + $order_invoices_amount;
												@endphp 
											@endif --}}


											<a href="{{ route('invoices.edit', $inv->id) }}" class="ml-3">
												@if($inv->invoice_num) {{ $inv->invoice_num }} @else {{ $inv->id }} @endif
											</a>
										@endforeach
									@endif
								@endif		
							</td>

							<td class="text-right">
								@if($order->provider_id == session('currentCompany'))
									{{-- Incluir en la factura, si no ha sido ya facturado o queda pendiente algún importe --}}
									@if(!$invoiced)
										{{-- || $totalAmountByOrderInvoices < $amount[2]  26/03/21 revisar esta condición, pues los pedidos ya facturados no pueden ya quitarse --}}

										@if(in_array($order->status, config('constants.ORDER_STATUS_INVOICED_'))) 
											@if($order->invoiceable)
												@php
												$title = trans('textos.factura_pedido_incluido');
												$checked = 'checked';
												@endphp
											@else
												@php
												$title = trans('textos.factura_incluir_pedido');
												$checked = '';
												@endphp
											@endif

											{{-- Si ya hay facturas generadas no se puede desvincular el pedido del grupo, por lo que sólo se indica que el pedido es facturable con un check, pero se omite el checkbox --}}
											@if($totalAmountByOrderInvoices == 0)
												<label class="custom-chk-rdb" rel="tooltip" title="{{ $title }}">
			                       					<input type="checkbox" name="status" {{ $checked }}><span class="chk1 btnAddOrderToInvoice" data-order="{{ $order->id }}"></span>
			                   					</label>
			                   				@elseif($order_invoices_amount > 0)
			                   					<span class="btn btn-sm btn-light no-spinner" rel="tooltip" title="{{ trans('textos.pedido_facturado') }}">
			                   						<i class="fa fa-file-invoice-dollar text-success"></i>
			                   					</span>
			                   				@endif
		                   				@endif
	                   				@endif

									{{-- Editar --}}
									<a target="_blank" href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_editar')) !!}">
	                            		<i class="fa fa-edit"></i>
	                        		</a>

	                        		{{-- Eliminar, si no ha sido facturada --}}
	                        		@if($group->status == config('constants.ORDER_GROUPS_OPENED_') && !$invoiced)
										<form method="POST" action="{{ route('order-groups-orders.destroy', $order->ogo_id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		                        			<input name="_method" type="hidden" value="DELETE">
		                        			{{ csrf_field() }}
		                        			<button type="submit" class="btn btn-sm btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_eliminar_de_grupo')) !!}" data-msg="{!! ucfirst(trans('textos.pedido_confirm_eliminar_grupo')) !!}"><i class="fa fa-trash"></i></button>
		                    			</form>
		                    		@endif
	                    		@else
	                    			<i class="fa fa-ban text-danger"></i>
	                    		@endif
							</td>
						</tr>

						@php
						$i++;
						@endphp
					@endforeach
				</tbody>

				<tfoot>
					<tr>
						<td colspan="4" class="text-right">
							<b>{!! ucfirst(trans('textos.total')) !!}</b>
						</td>
						<td class="text-right">
							{{ number_format($totalAmount, 2) }}{{ $currency->symbol }}	
						</td>	
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

@php
//Importe total de las facturas:
$invoicesAmount = 0;
if(count($invoices)){
	foreach($invoices as $inv){
		if(!$inv->refund){
			$invoicesAmount = $invoicesAmount + $inv->total_amount;
		}else{
			$invoicesAmount = $invoicesAmount - $inv->total_amount;
		}
	}
}
@endphp

@push('scripts')
<script>
var ordersAmount = '{{ number_format($totalAmount, 2, '.', '') }}';
var invoicesAmount =  '{{ number_format($invoicesAmount, 2, '.', '') }}';

$(document).ready(function(){
	//Bloqueo de nuevas facturas si el importe de las facturas iguala o supera el importe de los pedidos:
	if($('.newInvoice').length > 0 && parseFloat(invoicesAmount) >= parseFloat(ordersAmount)){
		$('.newInvoice').hide();	
	}
});
</script>
@endpush