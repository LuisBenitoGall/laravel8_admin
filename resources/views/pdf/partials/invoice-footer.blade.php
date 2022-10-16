	<footer>
		<table class="tblFooter">
			<thead>
				<tr>
					<th style="background-color: #efefef; text-align: right; width: 80%;">
						{{ strtoupper(trans('textos.subtotal')) }}	
					</th>
					<th style="background-color: #efefef; text-align: right;">
						<b>{{ $invoice->amount }}{{ $currency->symbol }}</b>
					</th>
				</tr>

				{{-- Importes de IVA --}}
				@if(isset($sumIva) && count($sumIva) > 0)
					@foreach($sumIva as $i => $si)
						<tr>
							<th style="background-color: #efefef; text-align: right; width: 80%;">
								IVA ({{ $i }}%)	
							</th>
							<th style="background-color: #efefef; text-align: right;">
								<b>{{ array_sum($si) }}{{ $currency->symbol }}</b>
							</th>
						</tr>
					@endforeach
				@endif
			</thead>

			<tbody>
				<tr>
					<td style="background-color: {{ $bg_color }}; text-align: right;">
						{{ strtoupper(trans('textos.total')) }}
					</td>
					<td style="background-color: {{ $bg_color }}; text-align: right;">
						<b>{{ $invoice->amount_with_iva }}{{ $currency->symbol }}</b>
					</td>
				</tr>
			</tbody>
		</table>	

		@if($template)
			@if($template['payment_method'])
				<table class="footer-textos">
					<tbody>
						<tr>
							<td style="color: {{ $bg_color }}; text-transform: uppercase;"><b>{{ trans('textos.detalles_transferencia') }}</b></td>
						</tr>
						<tr>
							<td>{!! $template['payment_method'] !!}</td>
						</tr>
					</tbody>
				</table>
			@endif
		@endif

		<table class="tblFoot" style="background-color: {{ $bg_color }}">
			<tbody>
				<tr>
					<td style="font-weight: bold; text-transform: uppercase;">{{ trans('textos.observaciones') }}:</td>
				</tr>

				@if($invoice_order)
					<tr>
						<td>
							{{ trans('textos.presupuesto_num') }}:

							<b style="color: #111">
								@if($invoice_order->budget_num)
									{{ $invoice_order->budget_num }}
								@elseif($invoice_order->budget_old_num)
									{{ $invoice_order->budget_old_num }}
								@else
									{{ $invoice_order->id }}
								@endif
							</b>
						</td>
					</tr>
				@endif

				@if($invoice_order->order_num)
					<tr>
						<td>
							{{ trans('textos.pedido') }}:
							<b style="color: #111">{{ $invoice_order->order_num }}</b>
						</td>	
					</tr>
				@endif

				@if($invoice_order->purchase_num || $invoice_order->old_purchase)
					<tr>
						<td>
							{{ trans('textos.orden_compra_num') }}:
							<b style="color: #111">@if($invoice_order->purchase_num) {{ $invoice_order->purchase_num }} @else {{ $invoice_order->old_purchase }} @endif</b>
						</td>	
					</tr>
				@endif

				@if($template)
					<tr>
						<td style="padding: 30px 0;">{!! $template['conditions'] !!}</td>
					</tr>
					
					<tr>
						<td style="padding: 30px 0;">{!! $template['observations'] !!}</td>
					</tr>
				@endif
			</tbody>
		</table>
	</footer>
</body>
</html>