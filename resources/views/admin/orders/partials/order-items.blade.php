<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="tblOrderItems">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{{ ucfirst(trans('textos.producto')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.precio_unidad')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.jornadas')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.descuento')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
					<th>{{ ucfirst(trans('textos.estado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			{{-- @php
			$i = 1;
			$ivas = [];
			$first_iva = true;
			$item_status = App\Helpers\Helpers::orderLineStatus();
			$iva_resume = false;
			@endphp

			<tbody>
				@foreach($items as $item)
					@php
					if(!in_array($item->iva, $ivas)){
						array_push($ivas, $item->iva);
						if(!$first_iva) $iva_resume = true;
					}

					$amount = App\Helpers\Helpers::itemAmount($item, true);
					@endphp

					@if($iva_resume)
						<tr>
							<td colspan="7" class="text-right"><b>{{ trans('textos.importe') }}</b></td>	
							<td></td>
							<td></td>
		                	<td></td>
						</tr>
					@endif

					<tr>
						<td class="text-right">{{ $i }}</td>
						<td>{!! $item->concept !!}</td>
						<td class="text-right">{!! $item->qty !!}</td>
						<td class="text-right">{!! $item->price !!}{{ $currency->symbol }}</td>
						<td class="text-right">@if($item->days > 0) {!! $item->days !!} @endif</td>
						<td class="text-right">@if($item->discount > 0) {!! $item->discount !!}% @endif</td>
						<td class="text-right">@if($item->iva) {!! $item->iva !!}% @endif</td>
						<td class="text-right">{!! $amount !!}{{ $currency->symbol }}</td>
						<td>@if($item->status) {!! $item_status[$item->status] !!} @endif</td>
						<td class="text-right">
							<a href="#" class="btn btn-xs btn-info btnEditItem" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-toggle="modal" data-target="#modal-edit-line" data-id="{{ $item->id }}"><i class="fa fa-edit"></i></a>

							<form method="POST" action="{{ route('items.destroy', $item) }}" class="frm-inline" role="form" accept-charset="UTF-8">
                            	<input name="_method" type="hidden" value="DELETE">
                            	{{ csrf_field() }}
                            	<input type="hidden" name="environment" value="orders">
                            	<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{!! ucfirst(trans("textos.pedido_item_confirm_eliminar")) !!}"><i class="fa fa-trash"></i></button>
                        	</form>
						</td>
					</tr>

					

					@php
					$i++;
					$first_iva = false;
					$iva_resume = false;
					@endphp
				@endforeach
			</tbody> --}}

			<tfoot>
				@if($order->discount)
					<tr>
		                <td colspan="6" class="text-right"><b>{{ ucfirst(trans('textos.descuento')) }}</b></td>
		                <td class="text-right">{{ $order->discount }}%</td>
		                <td></td>
		                <td></td>
		            </tr>
				@endif

	            <tr>
	                <td colspan="6" class="text-right"><b>{{ trans('textos.importe_total') }}</b></td>
	                <td class="text-right">{{ $order_amount }}{{ $currency->symbol }}</td>
	                <td></td>
	                <td></td>
	            </tr>

	            {{-- <tr>
	                <td colspan="6" class="text-right"><b>{{ trans('textos.importe') }} + IVA</b></td>
	                <td class="text-right" id="totalAmountIva"></td>
	                <td></td>
	                <td></td>
	            </tr> --}}
	        </tfoot>
		</table>
	</div>
</div>
