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
	                <td colspan="6" class="text-right"><b>{{ trans('textos.importe_total') }} *</b></td>
	                <td class="text-right">{{ $order_amount }}{{ $currency->symbol }}</td>
	                <td></td>
	                <td></td>
	            </tr>

	            @if(isset($invoice) && $invoice->with_iva > 0)
		            <tr>
		                <td colspan="6" class="text-right"><b>{{ trans('textos.importe') }} + IVA ({{ $invoice->with_iva }}%)</b></td>
		                <td class="text-right">{{ round($invoice->amount_with_iva, 2) }}{{ $currency->symbol }}</td>
		                <td></td>
		                <td></td>
		            </tr>
		        @endif
	        </tfoot>
		</table>

		{{-- Aviso sobre cálculo importe de líneas --}}
		<div class="col-12">
			<small class="text-warning">
			* {{ trans('textos.importe_total_texto') }}
			</small>
		</div>
	</div>
</div>
