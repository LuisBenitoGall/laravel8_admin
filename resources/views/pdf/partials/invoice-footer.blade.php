	{{-- <div style="page-break-after:always;"></div> --}}
	<footer style="margin-top: 490px;">
		<table class="tblFooter">
			<thead>
				{{-- Subtotal: importe neto sin IVA --}}
				<tr>
					<th style="background-color: #efefef; text-align: right; width: 80%;">
						{{ strtoupper(trans('textos.subtotal')) }}	
					</th>
					<th style="background-color: #efefef;"></th>
					<th style="background-color: #efefef; text-align: right;">
						<b>
							@if($invoice->refund)
								-{{ App\Helpers\helpers::setEUNum($invoice->amount) }}{{ $currency->symbol }}	
							@else
								{{ App\Helpers\helpers::setEUNum($invoice->amount) }}{{ $currency->symbol }}
							@endif
						</b>
					</th>
				</tr>

				{{-- Descuentos aplicados sobre el pedido: indicamos descuento sobre el total para facturas que NO sean de abono --}}
				@if($invoice_order->discount && !$invoice->refund && $invoice->discount_amount)
					<tr>
						<th style="background-color: #efefef; text-align: right; width: 80%;">
							<b>{{ strtoupper(trans('textos.descuento')) }}</b>
						</th>
						<th style="background-color: #efefef; text-align: right;">
							{{ $invoice_order->discount }}%
						</th>
						<th style="background-color: #efefef; text-align: right;">
							{{ App\Helpers\helpers::setEUNum($invoice_order->discount_amount) }}{{ $currency->symbol }}
						</th>
					</tr>
				@endif

				{{-- Retención IRPF  --}}
				@php
				$desc_irpf = 0;
				@endphp

				@if($invoice->irpf > 0)
					<tr>
						<th style="background-color: #efefef; text-align: right; width: 80%;">
							<b>{{ strtoupper(trans('textos.retencion')) }} IRPF ({{ $invoice->irpf }}%)</b>
						</th>
						<th style="background-color: #efefef; text-align: right;">
							
						</th>
						<th style="background-color: #efefef; text-align: right;">
							@php
							$desc_irpf = ($invoice->irpf * $invoice->amount) / 100;
							@endphp
							-{{ $desc_irpf }}{{ $currency->symbol }}
						</th>
					</tr>
				@endif

				{{-- Importes de IVA facturas simple --}}
				{{-- 27/11/2023: se reestablece el IVA desglosado según tipo aplicado --}}
				@if(isset($sumIva) && count($sumIva) > 0)
					{{-- Cantidad de IVA distintos aplicados --}}
					@php
					$amount_accumulated = $invoice->amount;		//Acumulamos los importes parciales para comprobar que cuadran con el importe final.
					$j = 1;
					$num_ivas = count($sumIva);
					@endphp

					@foreach($sumIva as $i => $si)
						{{-- Facturación parcial: --}}
						@if($amount_invoiced && $amount_invoiced->percent < config('constants.TOLERANCE_INVOICED_'))
							@php
							$base_ = App\Helpers\helpers::percent(array_sum($baseIva[$i]), $amount_invoiced->percent);
							$iva_ = App\Helpers\helpers::percent(array_sum($si), $amount_invoiced->percent);
							@endphp
						@endif
						
						{{-- Para facturas con un único tipo de IVA, se toman los valores de la factura. Evitamos posibles desajustes de decimales --}}
						@if($num_ivas == 1)
							@php
							$base_ = $invoice->amount;
							$iva_ = $invoice->amount_with_iva - $invoice->amount; 
							@endphp
						@endif

						@php
						$amount_accumulated = $amount_accumulated + $iva_;
						@endphp

						{{-- Ajuste del último importe de IVA para cuadrar importes --}}
						@if($num_ivas == $j && $amount_accumulated != $invoice->amount_with_iva)
							@php
							$iva_ = $iva_ + ($invoice->amount_with_iva - $amount_accumulated);
							@endphp
						@endif

						<tr>
							<th style="background-color: #efefef; text-align: right; width: 80%;">
								IVA ({{ $i }}%)
							</th>
							<th style="background-color: #efefef;">
								{{-- <b>{{ array_sum($baseIva[$i]) }}{{ $currency->symbol }}</b>	 --}}
								{{-- <b>{{ $base_ }}{{ $currency->symbol }}</b> --}}
							</th>
							<th style="background-color: #efefef; text-align: right;">
								{{-- <b>{{ array_sum($si) }}{{ $currency->symbol }}</b> --}}
								<b>
									@if($invoice->refund)
										-{{ App\Helpers\helpers::setEUNum($iva_) }}{{ $currency->symbol }}
									@else
										{{ App\Helpers\helpers::setEUNum($iva_) }}{{ $currency->symbol }}
									@endif
								</b>
							</th>
						</tr>

						@php
						$j++;
						@endphp
					@endforeach
				@endif

				{{-- Importes de IVA facturas agrupadas --}}
				@if(isset($group_iva_amount))
					<tr>
						<th style="background-color: #efefef; text-align: right;">
							IVA ({{ config('constants.IVA_DEFAULT_') }}%)	
						</th>
						<th style="background-color: #efefef;"></th>
						<th style="background-color: #efefef; text-align: right;">
							{{-- <b>{{ array_sum($si) }}{{ $currency->symbol }}</b> --}}
							<b>
								@if($invoice->refund)
									-{{ App\Helpers\helpers::setEUNum($group_iva_amount) }}{{ $currency->symbol }}
								@else
									{{ App\Helpers\helpers::setEUNum($group_iva_amount) }}{{ $currency->symbol }}
								@endif
							</b>
						</th>
					</tr>
				@endif

				{{-- <tr>
					<th style="background-color: #efefef; text-align: right; width: 80%;">
						IVA	
					</th>
					<th style="background-color: #efefef; text-align: right;">
						@php
						$iva_amount = $invoice->amount_with_iva - $invoice->amount;
						@endphp

						<b>{{ $iva_amount }}{{ $currency->symbol }}</b>
					</th>
				</tr> --}}
			</thead>

			<tbody>
				{{-- Total factura --}}
				<tr>
					<td colspan="2" style="background-color: {{ $bg_color }}; text-align: right;">
						{{ strtoupper(trans('textos.factura_total')) }}
					</td>
					<td style="background-color: {{ $bg_color }}; text-align: right;">
						<b>
							@if($invoice->refund)
								-{{ App\Helpers\helpers::setEUNum($invoice->amount_with_iva) }}{{ $currency->symbol }}
							@else
								@php
								$invoiceTotalAmount = $invoice->amount_with_iva - $desc_irpf;
								@endphp
								{{ App\Helpers\helpers::setEUNum($invoiceTotalAmount) }}{{ $currency->symbol }}
							@endif
						</b>
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

		@if($bank_account)
			<table class="footer-textos">
				<tbody>	
					<tr>
						<td style="font-weight: bold; text-transform: uppercase;">{{ trans('textos.bancarios_datos') }}:</td>
					</tr>
					<tr>
						<td>{{ ucfirst(trans('textos.entidad')) }}: {{ $bank_account->tradename }}</td>	
					</tr>
					<tr>
						@php
						//Formato IBAN:
						$ba_ = App\Helpers\helpers::customString($bank_account->account, 4, ' ');
						@endphp
						<td>IBAN: {{ $ba_ }}</td>	
					</tr>
					<tr>
						<td>BIC/SWIFT: {{ $bank_account->swift }}</td>	
					</tr>
				</tbody>
			</table>
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

							<b style="color: #FFF">
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
							<b style="color: #FFF">{{ $invoice_order->order_num }}</b>
						</td>	
					</tr>
				@endif

				@if($invoice_order->purchase_num || $invoice_order->purchase_old_num)
					<tr>
						<td>
							{{ trans('textos.orden_compra_num') }}:
							<b style="color: #FFF">@if($invoice_order->purchase_num) {{ $invoice_order->purchase_num }} @else {{ $invoice_order->purchase_old_num }} @endif</b>
						</td>	
					</tr>
				@endif

				@if($invoice->expiration_date)
					@php
					$expiration_date = \Carbon\Carbon::parse($invoice->expiration_date)->format($languages[session()->get('locale')][4]);
					@endphp
					<tr>
						<td>
							{{ trans('textos.fecha_vencimiento') }}:
							<b style="color: #FFF">{!! $expiration_date !!}</b>	
						</td>
					</tr>
				@endif

				{{-- Cuenta bancaria proveedor --}}
				@if($provider_bank_account)
					<tr>
						<td>
							{{ ucfirst(trans('textos.cuenta_bancaria')) }}:	
							<b style="color: #FFF">{{ $provider_bank_account->tradename }} - {{ $provider_bank_account->swift }} - {{ $provider_bank_account->account }}</b>
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