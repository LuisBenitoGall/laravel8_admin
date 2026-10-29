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
			//Aplicando descuento:
			if($row->discount){
				$percentage = App\Helpers\helpers::percent($line_amount, $row->discount);
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
		$baseIva = [];
		foreach($arrIva as $ai){
			$sumIva[$ai] = [];
			$baseIva[$ai] = [];
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
					{{-- 23/11/23: se desactiva la columna de IVA para cada registro, y se incluirán en la parte inferior, en los sumatorios --}}
					{{-- <th style="background-color: {{ $bg_color }}; text-align: center;">
						IVA
					</th> --}}
					<th style="background-color: {{ $bg_color }}; text-align: center;">
						{{ ucfirst(trans('textos.total')) }}({{ $currency->symbol }})
					</th>
				</tr>
			</thead>

			<tbody>
				<tr>
					<td style="background-color: #efefef; text-align: right;"></td>
					<td style="background-color: #efefef;"></td>
					<td colspan="{{ $colspan - 1 }}" style="background-color: #efefef;">
						<b>
							@if($invoice->refund)
								{{-- Obtenemos el item generado --}}
								@php
								$refund_item = App\Models\Item::select('concept')
								->where('invoice_id', $invoice->id)
								->first();
								@endphp

								@if($refund_item) 
									{{ $refund_item->concept }}
								@else
									{{ $invoice_order->name }}	
								@endif
							@else
								{{ $invoice_order->name }}
							@endif
						</b>	
					</td>
					<td style="background-color: #efefef; text-align: right;">
						<b>
							@if($invoice->refund)
								{{ $invoice->amount * -1 }}{{ $currency->symbol }}
							@else
								{{ $invoice->amount }}{{ $currency->symbol }}
							@endif
						</b>
					</td>
				</tr>

				{{-- Líneas de la factura --}}
				@if($invoice->refund)
					{{-- Buscamos facturada abonada --}}
					@php
					$refunded = App\Models\Invoice::select('id', 'invoice_num', 'invoice_old_num')->find($invoice->refund);
					$refunded_num = $refunded->invoice_num ? $refunded->invoice_num:($refunded->invoice_old_num? $refunded->invoice_old_num:$refunded->id);
					@endphp

					<tr>
						<td colspan="6">
							{{ trans('textos.abono_factura_num') }} <b>{{ $refunded_num }}</b>
						</td>
					</tr>

				@else
					@php
					$totalInvoice = 0;
					$j = 1;
					//Importe total por suma de items:
					$sum_items = 0;
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
									{{ number_format($amount_invoiced->percent, 2) }}%
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

							{{-- Descuento --}}
							<td style="text-align: right;">
								@if($row->discount)
									{{ $row->discount }}%
								@endif
							</td>

							{{-- <td style="text-align: right;">@if($invoice->with_iva > 0) {{ $row->iva }}% @endif</td> --}}

							{{-- Total línea --}}
							<td style="text-align: right;">
								@php
								$arr_order_amount = App\Helpers\helpers::itemAmount($row, false);
								$amount = $arr_order_amount['amount'];
								//$amount = $row->amount? $row->amount:round($row->qty * $row->price, 2);


								
								//Descuento. Sólo si el importe de factura por línea no viniera declarado:
								// if($row->discount && !$row->amount){
								// 	$discount = App\Helpers\helpers::percent($amount, $row->discount);	
								// 	$amount = round($amount - $discount, 2);	
								// }

								//Facturación parcial:
								$partial_amount = $amount;
								if($amount_invoiced && $amount_invoiced->percent < config('constants.TOLERANCE_INVOICED_')){
									$partial_amount = ($amount * $amount_invoiced->percent) / 100;
									//$amount = App\Helpers\helpers::percent(round($amount, 2), $amount_invoiced->percent);
								}

								//Total de la factura:
								$totalInvoice = $totalInvoice + round($amount, 2);

								//IVA:
								//23/11/23: se desactiva la columna de IVA para cada registro, y se incluirán en la parte inferior, en los sumatorios
								$iva_amount = App\Helpers\helpers::percent($amount, $row->iva);

								//Suma por items:
								$sum_items = $sum_items + round($partial_amount, 2);

								//Al alcanzar la última línea, la suma de los items debe ser igual al importe de la factura. En caso negativo, ajustamos la última línea para cuadrar decimales.
								if($j == $items->count()){
									$diff = $invoice->amount - $sum_items;
									if($diff != 0){
										$partial_amount = $partial_amount + $diff;
									}
								}

								//Sumando importes de IVA:
								array_push($sumIva[$row->iva], round($iva_amount,2));
								//Sumando bases por cada tipo de IVA:
								array_push($baseIva[$row->iva], round($partial_amount,2));
								@endphp

								@if($invoice->refund)
									{{ number_format($partial_amount, 2) * -1 }}{{$currency->symbol }}
								@else
									{{ number_format($partial_amount, 2) }}{{$currency->symbol }}
								@endif
							</td>
						</tr>

						@php
						$j++;
						@endphp
					@endforeach

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
				@endif
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
			$group_iva_amount = 0;
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

							//IVA acumulado:
							$group_iva_amount = $group_iva_amount + ($amount['invoice_amount'] - $amount['total_amount']);

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

