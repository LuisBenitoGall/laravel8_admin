<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblDeliveryOrders">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{{ ucfirst(trans('textos.pedido')) }}</th>
					<th>{{ ucfirst(trans('textos.estado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach($delivery_orders as $row)
					@if(in_array($row->id, $arr_delivery_orders))
						<tr>
							{{-- Nº pedido --}}
							<td class="text-right">
								@php
								$num = $row->order_num? $row->order_num:($row->order_old_num? $row->order_old_num:$row->orderId);
								@endphp

								@can('orders.edit')
									<a target="_blank" href="{{ route('orders.edit', $row->orderId) }}">
								@endcan
								{{ $num }}
								@can('orders.edit')
									</a>
								@endcan
							</td>
							{{-- Pedido --}}
							<td>
								{{ $row->name }}	
							</td>
							{{-- Estado --}}
							<td>
								
							</td>
							{{-- Acciones --}}
							<td class="text-right">
								{{-- Editar --}}
								@can('orders.edit')
									<a target="_blank" href="{{ route('orders.edit', $row->orderId) }}" rel="tooltip" title="{{ trans('textos.pedido_editar') }}" class="btn btn-xs btn-info">
										<i class="fa fa-edit"></i>
									</a>
								@endcan

								{{-- Desvincular --}}
								@can('deliveries.edit')
									<form method="POST" action="{{ route('delivery-orders.destroy', $row->id) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                            			<input name="_method" type="hidden" value="DELETE">
                            			{{ csrf_field() }}
                            			<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ trans('textos.pedido_desvincular') }}" data-msg="{{ trans('textos.pedido_entrega_confirm_desvincular') }}"><i class="fa fa-trash"></i></button>
                        			</form>	
								@endcan
							</td>
						</tr>
					@endif
				@endforeach
			</tbody>
		</table>
	</div>
</div>
