<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblProjectPurchases">
			<thead>
				<tr>
					<th>{!! ucfirst(trans('textos.proveedor')) !!}</th>
					<th>{!! ucfirst(trans('textos.cliente_primero')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.pedido')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.importe_pedido')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.factura')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.importe_factura')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_fiscal')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$ordersAmount = 0;
				$invoicesAmount = 0;
				@endphp

				@foreach($project_purchases as $pp)
					@php
					//Método que permita ver pedidos y facturas ajenos a la empresa en session:
					$method_order = 'edit';
					$method_invoice = 'edit-purchase';
					@endphp

					<tr>
						{{-- Proveedor --}}
						<td>
							{{ $pp->provider }}
						</td>
						{{-- Cliente primero --}}
						<td>
							@if($pp->customer_id != session('currentCompany'))
								{{ $pp->customer }}	
								@php
								$method_order = 'show';
								$method_invoice = 'show';
								@endphp
							@endif
						</td>
						{{-- Número compra --}}
						<td class="text-right">
							@php
							$num = $pp->purchase_num? $pp->purchase_num:($pp->purchase_old_num? $pp->purchase_old_num:$pp->id);
							@endphp

							@can('orders.edit')
								<a target="_blank" href="{{ route('orders.'.$method_order, $pp->id) }}"> 
							@endcan
								{{ $num }}
							@can('orders.edit')
								</a>
							@endcan
						</td>
						
						{{-- Importe pedido --}}
						<td class="text-right">
							@php
							$order_ = App\Models\Order::select('id')->find($pp->id);
							$purchase_amount = $order_->getAmount();
							//$totalAmount = $totalAmount + $purchase_amount['total_amount'];
							$ordersAmount = $ordersAmount + $purchase_amount['total_amount'];
							@endphp
							{{-- {{ $purchase_amount['total_amount'] }} --}}
							{{ $purchase_amount['total_amount'] }}
							{{ $currency->symbol }}
						</td>

						{{-- Factura --}}
						<td class="text-right">
							@php
							//$invoices = $pp->invoices;
							//$invoices_docs = [];
							@endphp

							{{-- @foreach($invoices as $inv)
								@if($inv->doc)
									@php
									array_push($invoices_docs, $inv->doc);
									@endphp
								@endif

								<a target="_blank" href="{{ route('invoices.edit-purchase', $inv->id) }}">
									@if($inv->invoice_num)
										{{ $inv->invoice_num }}
									@elseif($inv->invoice_old_num)
										{{ $inv->invoice_old_num }}
									@else
										{{ $inv->id }}
									@endif
								</a>
							@endforeach --}}

							<a target="_blank" href="{{ route('invoices.'.$method_invoice, $pp->invoiceId) }}">
								@if($pp->invoice_num)
									{{ $pp->invoice_num }}
								@elseif($pp->invoice_old_num)
									{{ $pp->invoice_old_num }}
								@else
									{{ $pp->invoiceId }}
								@endif
							</a>
						</td>

						{{-- Importe factura --}}
						<td class="text-right">
							@php
							//$purchase_amount = $pp->getAmount();
							//$totalAmount = $totalAmount + $purchase_amount['total_amount'];
							$invoicesAmount = $invoicesAmount + $pp->amount;
							@endphp
							{{-- {{ $purchase_amount['total_amount'] }} --}}
							{{ $pp->amount }}
							{{ $currency->symbol }}
						</td>

						{{-- Fecha fiscal --}}
						<td class="text-right">
							@if($pp->fiscal_date)
								{!! Carbon\Carbon::parse($pp->fiscal_date)->format($languages[session()->get('locale')][4]) !!}
							@endif
						</td>
						
						{{-- Estado --}}
						<td>
							@if($pp->status > 0)
								{!! ucfirst(App\Helpers\helpers::orderStatus($pp->order_status)) !!}
							@endif
						</td>

						{{-- Acciones --}}
						<td class="text-right">
							@can('orders.edit')
								{{-- Documento --}}
								@if(!empty($invoices_docs))
									@foreach($invoices_docs as $id_)
										<a target="_blank" href="{{ asset('docs/invoices/'.$id_) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.documento_ver')) }}"><i class="fa fa-file"></i></a>	
									@endforeach
								@endif

								{{-- Editar --}}
								@if($method_order == 'edit')
									<a target="_blank" href="{{ route('orders.edit', $pp->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
								@endif

								{{-- Desvincular --}}
								<form method="POST" action="{{ route('purchase-from-project.destroy', $pp->ppId) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                            	<input name="_method" type="hidden" value="DELETE">
	                            	{{ csrf_field() }}
	                            	<input type="hidden" name="return" value="project">
	                            	<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.desvincular')) }}" data-msg="{!! ucfirst(trans("textos.compra_proyecto_desvincular_confirm")) !!}"><i class="fa fa-trash"></i></button>
	                        	</form>
							@endcan
						</td>
					</tr>
				@endforeach
			</tbody>

			<tfoot>
				<tr>
					<td colspan="3" class="text-right">
						<b>{{ ucfirst(trans('textos.importe_pedidos')) }}</b>
					</td>
					<td class="text-right">
						{{ $ordersAmount }}{{ $currency->symbol }}
					</td>
					<td class="text-right">
						<b>{{ ucfirst(trans('textos.importe_facturas')) }}</b>
					</td>
					<td class="text-right">
						{{ $invoicesAmount }}{{ $currency->symbol }}
					</td>
					<td colspan="3" class="text-right">
				</tr>	
			</tfoot>
		</table>
	</div>
</div>