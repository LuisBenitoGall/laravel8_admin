@php
$budgetNum = $order->budget_num? $order->budget_num:$order->id;
$bg_color = $company_settings->base_color_budgets? $company_settings->base_color_budgets:'#FFF';
@endphp

@include('pdf.partials.head', ['header_title' => ucfirst(trans('textos.presupuesto')).' #'.$budgetNum])

@include('pdf.partials.header', [
	'standard' => true, 
	'company_settings' => $company_settings, 
	'bg_color' => $bg_color, 
	'left_h1' => ucfirst(trans('textos.presupuesto')).' Nº '.$budgetNum,
	'left_title' => $order->name? $order->name:false,
	'left_manager' => $provider_manager? $provider_manager->name.' '.$provider_manager->surname:false,
	'right_h1' => ucfirst(trans('textos.cliente')),
	'right_manager' => $customer_manager? $customer_manager->name.' '.$customer_manager->surname:'',
])

@php
$resumen = '';
$bloques = '';
$totalAmount = 0;
$totalAmountWithoutDiscounts = 0;
$discount_in_lines = false;
@endphp

<main>
	@if($blocks)
		@foreach($blocks as $block)
			{{-- Items: --}}
			@php
			$translations = $block->translations? unserialize($block->translations):false;
			$block_name = $translations? $translations[$locale]:$block->name;

			$items = $block->getItems($block->id, $order->customer_id);

			$htmlItems = '';
			$blockAmount = 0;
			$blockAmountWithoutDiscount = 0;
			$itemsWithQty = false;

			foreach($itemsSelected as $its){
				//Sólo items con $print == 1
				if($its->print == 1){
					//Buscando el bloque al que pertenece el item:
					if($its->budget_form_item_id == 1 || !$its->budget_form_item_id){
						//$p = explode('|', $its->free_field);
						//$block_id = $p[0];	
						$block_id = $its->budget_form_block_id;
					}else{
						$block_id = $its->recid;
					}

					//Si el item pertenece al bloque:
					if($block->id == $block_id){
						$htmlItems .= '<tr>';	
							//Item:
							$concept = $its->concept? $its->concept:$its->name;
							$htmlItems .= '<td>'.$concept.'</td>';

							//Qty:
							$htmlItems .= '<td style="text-align: right;">'.$its->qty;
								if($its->days > 1){
									$htmlItems .= '<br><small style="font-size: 9px;">(x '.$its->days.' '.trans('textos.jornadas');
										if($its->days_continue_discount && $its->days_continue_discount > 0){
											$htmlItems .= '<br>- '.$its->days_continue_discount.'%';
										}
									$htmlItems .= ')</small>';
								}	
							$htmlItems .= '</td>';

							//Cálculo de importe y descuento:
							$amount = $its->getItemAmount($its);

							//Cálculo importe sin ningún descuento:
							$amountWithoutDiscount = $its->getItemAmount($its, false);

							if($its->discount){
								$discount = $its->discount? ' <span style="font-size: 9px;">('.$its->discount.'%)</span>':'';
								$discount_in_lines = true;
							}else{
								$discount = '';	
							}

							//Subtotal para importe porcentual:
							if($its->calculated_by_percent && $its->calculated_by_percent > 0){
								$amount = $amountWithoutPercentItems * $its->price;	

								if($its->discount && $its->discount > 0){
									$percentage_ = App\Helpers\Helpers::percent($amount, $its->discount);	
									$amount = $amount - $percentage_;
								}
								$amount = round($amount, 2);
								$amountWithoutDiscount = $amountWithoutDiscount + $amount;
							}

							//Texto alternativo para líneas a coste 0:
							if($its->line_no_price){
								$precio = $noPriceLinesTexts[$its->line_no_price];
								$importe = $noPriceLinesTexts[$its->line_no_price];
								$amount = 0;
							}else{
								$precio = $its->price.$currency->symbol.$discount;
								$importe = $amount.$currency->symbol;
							}

							//Precio:
							$htmlItems .= '<td style="text-align: right;">'.$precio.'</td>';	

							//Importe:
							$htmlItems .= '<td style="text-align: right;">'.$importe.'</td>';

							//Cálculo del importe del bloque:
							$blockAmount = $blockAmount + $amount;

							$blockAmountWithoutDiscount = $blockAmountWithoutDiscount + $amountWithoutDiscount;

							//Observaciones:
							//Manipulamos el font-size de las observations (hasta que encontrar una solución más estable en Trumbowyg):
					        $observations = str_replace("14.4", "11", $its->observations);
					        $observations = str_replace("14", "11", $observations);
							$htmlItems .= '<td colspan="2">'.$observations.'</td>';
						$htmlItems .= '</tr>';

						$itemsWithQty = true;
					}
				}	
			}

			//Si el bloque tiene items con cantidad, se muestra:
			if($itemsWithQty){
				// Cada bloque genera una tabla: 
				$bloques .= '<table class="tblBody" style="margin-top: 0;">';
					$bloques .= '<thead>';
						$bloques .= '<tr>';
							$bloques .= '<th style="background-color: '.$bg_color.'; text-transform: uppercase; width: 240px;">'.$block_name.'</th>';
							$bloques .= '<th style="background-color: '.$bg_color.'; text-align: center; width: 60px;">'.trans('textos.ct').'</th>';
							$bloques .= '<th style="background-color: '.$bg_color.'; text-align: center; width: 60px;">'.ucfirst(trans('textos.coste')).'</th>';
							$bloques .= '<th style="background-color: '.$bg_color.'; text-align: center; width: 70px;">'.ucfirst(trans('textos.coste_total')).'</th>';
							$bloques .= '<th style="background-color: '.$bg_color.';">'.ucfirst(trans('textos.observaciones')).'</th>';
							$bloques .= '<th style="background-color: '.$bg_color.'; text-align: right;">'.$blockAmount.$currency->symbol.'</th>';
						$bloques .= '</tr>';
					$bloques .= '</thead>';

					$bloques .= '<body>';
						$bloques .= $htmlItems;	
					$bloques .= '</body>';
				$bloques .= '</table>';
				
				// Resumen de bloque:
				$resumen .= '<tr>';
					$resumen .= '<td style="background-color: #dbe5f1; color: '.$bg_color.' !important; font-weight: bold;">'.$block_name.'</td>';
					$resumen .= '<td colspan="3" style="border-bottom: 1px dotted #222; text-align: right;">'.number_format($blockAmount, 2).$currency->symbol.'</td>';
				$resumen .= '</tr>';
			}

			$totalAmount = $totalAmount + $blockAmount;
			$totalAmountWithoutDiscounts = $totalAmountWithoutDiscounts + $blockAmountWithoutDiscount;
			@endphp
		@endforeach

		<table class="tblBody" style="margin-bottom: 30px;">
			<thead>
				<tr>
					<th style="background-color: {{ $bg_color }}; text-align: left; width: 250px;">{{ trans('textos.presupuesto_resumen') }}</th>
					<th colspan="2" style="width: 400px;">&nbsp;</th>
					<th>&nbsp;</th>
				</tr>	
			</thead>

			<tbody>
				{!! $resumen !!}
			</tbody>

			<tfoot>
				<tr>
					<td colspan="2" style="background-color: #FFF;"></td>
					<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: left;">{{ strtoupper(trans('textos.total')) }}</td>
					<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: right;">
						{{ number_format($totalAmount, 2) }}{{ $currency->symbol }}
					</td>
				</tr>

				@if($order->discount && $order->discount > 0)	{{-- //&& !$order->final_customer_id --}}
					@php
					$percentage_ = App\Helpers\helpers::percent($totalAmount, $order->discount);	
					$totalAmountWithDiscount = $totalAmount - $percentage_;
					@endphp
					<tr>
						<td colspan="2" style="background-color: #FFF;"></td>
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: left;">{{ strtoupper(trans('textos.importe_descuento')) }} ({{ $order->discount }}%)</td>	
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: right;">
							{{ $totalAmountWithDiscount }}{{ $currency->symbol }}
						</td>
					</tr>
				@endif

				{{-- Importe sin descuento --}}
				@if($order->final_customer_id || $discount_in_lines)
					<tr>
						<td colspan="2" style="background-color: #FFF;"></td>
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: left;">{{ strtoupper(trans('textos.importe_sin_descuentos')) }}</td>	
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: right;">
							{{ $totalAmountWithoutDiscounts }}{{ $currency->symbol }}
						</td>	
					</tr>
				@endif
			</tfoot>
		</table>

		{!! $bloques !!}

		{{-- Pie de tabla: --}}
		<table class="tblBody" style="margin-top: 0;">
			<tfoot>
				<tr>
					<td style="text-align: right; width: 390px;">{{ trans('textos.total') }}</td>
					<td style="text-align: right; width: 67px">{{ $totalAmount }}{{ $currency->symbol }}</td>
					<td style="">&nbsp;
				</tr>
			</tfoot>
		</table>
	@endif

	@include('pdf.partials.conditions-template')

	@include('pdf.partials.footer')
</main>