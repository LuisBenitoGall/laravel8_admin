<input type="hidden" id="dtController" value="orders">

<div class="row">
	<div class="col-12">
		<div class="table-responsive">
			<table class="table table-hover dTable" id="tblOrderGroupOrders">
				<thead>
					<tr>
						<th class="text-center wdt50">#</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.num_manual')) !!}</th>
						<th class="wdt250">{!! ucfirst(trans('textos.pedido_titulo')) !!}</th>
						<th class="text-center wdt100">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
						<th class="text-center wdt100">{!! trans('textos.importe') !!}</th>
						<th class="text-center wdt100">{!! trans('textos.factura') !!}</th>
						<th class="wdt100">{!! trans('textos.estado') !!}</th>
						<th class="text-center wdt150">{!! ucfirst(trans('textos.acciones')) !!}</th>
					</tr>
				</thead>

				@php
				$i = 1;
				$totalAmount = 0;
				$totalDiscountAmount = 0;
				$order_status = App\Helpers\helpers::orderStatus();	
				@endphp

				<tbody>
					{{-- Pedidos del grupo: --}}
					@foreach($orders as $row)
						@php
						$totalAmountByOrderInvoices = 0;		//Cálculo a partir del importe de las facturas de cada pedido.
						@endphp

						<tr>
							{{-- Número pedido --}}
							<td class="text-right @if($row->status == 0) text-soft @endif">
								@php
								$num = $row->order_num? $row->order_num:$row->id;
								@endphp
								<a target="_blank" href="{{ route('orders.edit', $row->id) }}">{{ $num }}</a>
							</td>

							{{-- Número manual --}}
							<td class="text-right @if($row->status == 0) text-soft @endif">
								{{ $row->order_old_num }}
							</td>

							{{-- Título --}}
							<td @if($row->status == 0) class="text-soft" @endif>{{ $row->name }}</td>

							{{-- Fecha entrega --}}
							<td class="text-right @if($row->status == 0) text-soft @endif">
								@php
        						$fechaEntrega = $row->delivery_date? Carbon\Carbon::parse($row->delivery_date)->format($languages[session()->get('locale')][4]):'';
        						@endphp

        						{{ $fechaEntrega }}	
							</td>

							{{-- Importe --}}
							@php
							$amount = $row->amount['amount_with_discount'];
							$totalAmount = $totalAmount + $amount;
							@endphp
							<td class="text-right">{{ round($amount, 2) }}{{ $currency->symbol }}</td>

							{{-- Nº factura --}}
							<td class="text-right">
								@php
								$invoiced = false;
								@endphp

								@if($row->invoiced)
									@php
									//Facturas del pedido:
									$invoices_ = $row->invoicesOrderGroup($group->id);

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
												$row_invoices = unserialize($inv->invoices);
												$arr_order_invoices = [];
												$row_invoices_amount = 0;
												@endphp

												@foreach($row_invoices as $oi)
													@php
													array_push($arr_order_invoices, $oi['invoice']);
													$row_invoices_amount = $row_invoices_amount + $oi['amount'];
													@endphp
												@endforeach

												@if(in_array($inv->id, $arr_order_invoices))
													<a href="{{ route('invoices.edit', $inv->id) }}" class="ml-3">
														@if($inv->invoice_num) {{ $inv->invoice_num }} @else {{ $inv->id }} @endif
													</a>
												@endif

												@php
												$totalAmountByOrderInvoices = $totalAmountByOrderInvoices + $row_invoices_amount;
												@endphp 
											@endif --}}


											<a href="{{ route('invoices.edit', $inv->id) }}" class="ml-3">
												@if($inv->invoice_num) {{ $inv->invoice_num }} @else {{ $inv->id }} @endif
											</a>
										@endforeach
									@endif
								@endif		
							</td>

							{{-- Estado del pedido: --}}
							<td @if($row->status == 0) class="text-soft" @endif>
								<span class="xoption-{{ $row->id }}">
									@if($row->status > 0) {!! ucfirst($order_status[$row->status]) !!} @endif
								</span>	

								{{-- <select name="" class="form-control select2 comboEstado reload">
									@foreach($statuses as $idx => $st)
										@php
										$selected = $idx == $row->status? 'selected':'';
										@endphp
										<option value="{{ $idx }}" {{ $selected }} data-orderid="{{ $row->id }}">{{ ucfirst($st) }}</option>
									@endforeach
								</select> --}}
							</td>

							{{-- Acciones --}}
							<td class="text-right">
								{{-- Incluir en factura - condiciones:
									1. Permisos para generar facturas.
									2. Pedido no facturado.
									3. Pedido en estado apto para facturación.
								--}}
								@can('invoices.create')
									@if(!$row->invoiced && in_array($row->status, config('constants.ORDER_STATUS_INVOICED_')))
										<label class="switch mr-1 addOrder" rel="tooltip" title="{{ trans('textos.factura_incluir_pedido') }}">
										 	<input type="checkbox" value="{{ $row->id }}">
										  	<span class="slider round"></span>
										</label>
									@endif
								@endcan

								{{-- Incluir en la factura, si no ha sido ya facturado o queda pendiente algún importe --}}
								@if(!$invoiced)
									{{-- || $totalAmountByOrderInvoices < $amount[2]  26/03/21 revisar esta condición, pues los pedidos ya facturados no pueden ya quitarse --}}

									@if(in_array($row->status, config('constants.ORDER_STATUS_INVOICED_'))) 
										@if($row->invoiceable)
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
		                       					<input type="checkbox" name="status" {{ $checked }}><span class="chk1 btnAddOrderToInvoice" data-order="{{ $row->id }}"></span>
		                   					</label>
		                   				@elseif($order_invoices_amount > 0)
		                   					<span class="btn btn-xs btn-light no-spinner" rel="tooltip" title="{{ trans('textos.pedido_facturado') }}">
		                   						<i class="fa fa-file-invoice-dollar text-success"></i>
		                   					</span>
		                   				@endif
	                   				@endif
                   				@endif

                   				@can('orders.edit')
	                   				{{-- Estado --}}
									<x-select-status :options="$order_status" id="{{ $row->id }}" />

									{{-- Editar --}}
									<a target="_blank" href="{{ route('orders.edit', $row->id) }}" class="btn btn-xs btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_editar')) !!}">
	                            		<i class="fa fa-edit"></i>
	                        		</a>
	                        	@endcan

	                        	@can('orders.destroy')
	                        		{{-- Eliminar, si no ha sido facturada --}}
	                        		@if($group->status == config('constants.ORDER_GROUPS_OPENED_') && !$invoiced)
										<form method="POST" action="{{ route('order-group-orders.destroy', $row->ogo_id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
		                        			<input name="_method" type="hidden" value="DELETE">
		                        			{{ csrf_field() }}
		                        			<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{!! ucfirst(trans('textos.pedido_eliminar_de_grupo')) !!}" data-msg="{!! ucfirst(trans('textos.pedido_confirm_eliminar_grupo')) !!}"><i class="fa fa-trash"></i></button>
		                    			</form>
		                    		@endif
		                    	@endcan
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