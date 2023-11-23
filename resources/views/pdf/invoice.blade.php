@php
$invoiceNum = $invoice->invoice_num? $invoice->invoice_num:$invoice->id;
$bg_color = $bn? '#000':($company_settings->base_color_invoices? $company_settings->base_color_invoices:'#10172C');
$text_doc = $invoice->refund > 0? 'abono':'factura';
@endphp

@include('pdf.partials.head', ['header_title' => ucfirst(trans('textos.'.$text_doc)).' #'.$invoiceNum])

@include('pdf.partials.header', [
	'standard' => true,
	'company_settings' => $company_settings, 
	'bg_color' => $bg_color, 
	'left_h1' =>  ucfirst(trans('textos.'.$text_doc)).' Nº '.$invoiceNum,
	'left_title' => false,
	'left_manager' => false,
	'right_h1' => ucfirst(trans('textos.cliente')),
	'right_manager' => $customer_manager? $customer_manager->name.' '.$customer_manager->surname:''
])

<main>
	{{-- Líneas de la factura --}}
	@if($items && $items->count())
		@php
		$arrIva = [];

		//Comprobando si alguna línea viene con valor modificado manualmente:
		$modified_amount = false;
		$colspan = 3;
		foreach($items as $row){
			$line_amount = $row->qty * $row->price;
			if($row->discount){
				$percentage = App\Helpers\Helpers::percent($line_amount, $row->discount);
				$line_amount = $line_amount - $percentage;
			}	

			//Agregamos el cálculo al objeto:
			$row->line_amount = $line_amount;

			// if($line_amount != $row->amount){ 
			// 	$modified_amount = true; 
			// 	$colspan = 5;
			// }

			//Guardamos los tipos de IVA:
			array_push($arrIva, $row->iva);
		}

		//Eliminamos tipos de IVA duplicados:
		$arrIva = array_unique($arrIva);
		//Generamos los arrays de la suma de los distintos tipos de IVA:
		$sumIva = [];
		foreach($arrIva as $ai){
			$sumIva[$ai] = [];
		}
		@endphp

		<table class="tblBody">
			<thead>
				<tr>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.cantidad')) }}	
					</th>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.descripcion')) }}	
					</th>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.precio_unidad')) }}
					</th>
					{{-- @if($modified_amount)
						<th style="background-color: {{ $bg_color }}; text-align: center;">
							{{ ucfirst(trans('textos.importe_modificado')) }}	
						</th>	
					@endif --}}

					{{-- Porcentaje facturado para facturas parciales --}}
					@if($amount_invoiced && $amount_invoiced->percent < config('constants.TOLERANCE_INVOICED_'))
						<th style="background-color: {{ $bg_color }}; text-align: center;">
							{{ ucfirst(trans('textos.facturado')) }}
						</th>	

						@php
						$colspan = $colspan+1;
						@endphp
					@endif

					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.descuento')) }}
					</th>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						IVA
					</th>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.total')) }}({{ $currency->symbol }})
					</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td style="background-color: #efefef; text-align: right;"></td>
					<td style="background-color: #efefef;"></td>
					<td colspan="{{ $colspan }}" style="background-color: #efefef;">
						<b>{{ $invoice_order->name }}</b>	
					</td>
					<td style="background-color: #efefef; text-align: right;">
						<b>{{ $invoice->amount }}{{ $currency->symbol }}</b>
					</td>
				</tr>

				{{-- Líneas de la factura --}}
				@php
				$total = 0;
				@endphp

				@foreach($items as $row)
					<tr>
						<td class="text-right" style="text-align: right;">{{ $row->qty }}</td>
						<td>{{ $row->concept }}</td>
						{{-- Precio / unidad --}}
						<td class="text-right" style="text-align: right;">{{ $row->price.$currency->symbol }}
							@if($row->days > 1)
								<br><small style="font-size: 9px;">(x {{ $row->days }} {{ trans('textos.jornadas') }}
								
								@if($row->days_continue_discount && $row->days_continue_discount > 0)
									<br>- {{ $row->days_continue_discount }}%
								@endif)
								</small>
							@endif
						</td>

						{{-- Facturado --}}
						@if($amount_invoiced && $amount_invoiced->percent < config('constants.TOLERANCE_INVOICED_'))
							<td class="text-right" style="text-align: right;">
								{{ $amount_invoiced->percent }}%
							</td>
						@endif

						{{-- Precio modificado --}}
						{{-- @if($modified_amount)
							<td style="text-align: right;">
								@if($row->line_amount != $row->amount)
									{{ $row->amount }}{{ $currency->symbol }}
								@endif
							</td>
						@endif --}}

						<td style="text-align: right;">
							@if($row->discount)
								{{ $row->discount }}%
							@endif
						</td>

						<td style="text-align: right;">@if($invoice->with_iva > 0) {{ $row->iva }}% @endif</td>

						<td style="text-align: right;">
							@php
							$amount = $row->amount? $row->amount:$row->qty * $row->price;
							
							//Descuento. Sólo si el importe de factura por línea no viniera declarado:
							if($row->discount && !$row->amount){
								$discount = App\Helpers\Helpers::percent($amount, $row->discount);	
								$amount = $amount - $discount;	
							}

							//Facturación parcial:
							if($amount_invoiced && $amount_invoiced->percent < config('constants.TOLERANCE_INVOICED_')){
								$amount = App\Helpers\Helpers::percent($amount, $amount_invoiced->percent);
							}

							//IVA:
							$iva_amount = App\Helpers\Helpers::percent($amount, $row->iva);
							$amount = $amount + $iva_amount;

							//Sumando importes de IVA:
							array_push($sumIva[$row->iva], $iva_amount);
							@endphp
							{{ number_format($amount, 2) }}{{$currency->symbol }}
						</td>
					</tr>
				@endforeach

				{{-- Indicamos descuento sobre el total para facturas que NO sean de abono --}}
				@if($invoice_order->discount && !$invoice->refund && $invoice->discount_amount)
					<tr>
						<td></td>
						<td colspan="2">
							<b>{{ trans('textos.descuento_pedido') }}</b>
						</td>
						<td style="text-align: right;">
							{{ $invoice_order->discount }}%
						</td>
						<td colspan="2"></td>
					</tr>
				@endif

				{{-- Avances de facturas --}}
				{{-- @if($advance_invoices->count() > 0)
					<tr>
						<td style="background-color: #efefef;"></td>
						<td colspan="6" style="background-color: #efefef;">
							<b>{{ trans('textos.facturas_avance') }}</b>
						</td>	
					</tr>

					@foreach($advance_invoices as $ais)
						<tr>
							<td></td>
							<td>
								<b>{{ ucfirst(trans('textos.factura')) }} @if($ais->invoice_num) {{ $ais->invoice_num }} @else {{ $ais->id }} @endif</b>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align: right;">
								<b>-{{ number_format($ais->amount_with_iva, 2) }}{{ $currency->symbol }}</b>
							</td>
						</tr>
					@endforeach
				@endif --}}
			</tbody>
		</table>
	@endif

	{{-- Pedidos de agrupaciones --}}
	@if($suborders && $suborders->count())
		<table class="tblBody">
			<thead>
				<tr>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						#
					</th>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.pedido_num')) }}	
					</th>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.orden_compra_num')) }}	
					</th>
					<th style="background-color: {{ $bg_color }};">
						{{ ucfirst(trans('textos.pedido')) }}	
					</th>
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.importe')) }}	
					</th>
				</tr>
			</thead>

			@php
			$j = 1;
			@endphp

			<tbody>
				@foreach($suborders as $row)
					<tr>	
						<td style="text-align: right">
							{{ $j }}
						</td>
						<td style="text-align: right">
							<b>{{ $row->order_num }}</b>
						</td>
						<td style="text-align: right">
							@php
							$purchaseNum = $row->purchase_num? $row->purchase_num:($row->purchase_old_num? $row->purchase_old_num:'');
							@endphp
							{{ $purchaseNum }}
						</td>
						<td>
							{{ $row->name }}
						</td>
						<td style="text-align: right">
							@php
							$amount = $row->getAmount();
							@endphp
							{{ $amount['total_amount'] }}{{ $currency->symbol }}
						</td>
					</tr>

					@php
					$j++;
					@endphp
				@endforeach
			</tbody>
		</table>

		@if($j > 17)
			<div style="page-break-after:always;"></div>
		@endif
	@endif
</main>

@include('pdf.partials.invoice-footer')

