<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblProjectPurchases">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{!! ucfirst(trans('textos.proveedor')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_entrega')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$totalAmount = 0;
				@endphp

				@foreach($project_purchases as $pp)
					<tr>
						{{-- Número compra --}}
						<td>
							@php
							$num = $pp->purchase_num? $pp->purchase_num:($pp->purchase_old_num? $pp->purchase_old_num:$pp->id);
							@endphp

							@can('orders.edit')
								<a target="_blank" href="{{ route('orders.edit', $pp->id) }}"> 
							@endcan
								{{ $num }}
							@can('orders.edit')
								</a>
							@endcan
						</td>
						{{-- Proveedor --}}
						<td>
							{{ $pp->provider }}
						</td>
						{{-- Importe --}}
						<td class="text-right">
							@php
							$purchase_amount = $pp->getAmount();
							$totalAmount = $totalAmount + $purchase_amount['amount_without_discount'];
							@endphp
							{{ $purchase_amount['amount_without_discount'] }}
							{{ $currency->symbol }}
						</td>
						{{-- Fecha entrega --}}
						<td class="text-right">
							@if($pp->delivery_date)
								{!! Carbon\Carbon::parse($pp->delivery_date)->format($languages[session()->get('locale')][4]) !!}
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
								{{-- Editar --}}
								<a target="_blank" href="{{ route('orders.edit', $pp->id) }}" class="btn btn-xs btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>

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
					<td colspan="2" class="text-right">
						<b>{{ ucfirst(trans('textos.importe_total')) }}</b>
					</td>
					<td class="text-right">
						{{ $totalAmount }}{{ $currency->symbol }}
					</td>
					<td colspan="3" class="text-right">
				</tr>	
			</tfoot>
		</table>
	</div>
</div>