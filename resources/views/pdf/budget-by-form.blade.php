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
	'left_manager' => $provider_manager? ucfirst($provider_manager->name).' '.ucfirst($provider_manager->surname):false,
	'left_commercial' => $commercial && $commercial->id != $provider_manager->id? ucfirst($commercial->name).' '.ucfirst($commercial->surname):false,
	'right_h1' => ucfirst(trans('textos.cliente')),
	'right_manager' => $customer_manager? ucfirst($customer_manager->name).' '.ucfirst($customer_manager->surname):'',
])

@php
$resumen = '';
$bloques = '';
$totalAmount = 0;
$totalAmountWithoutDiscounts = 0;
$discount_in_lines = false;
$arr = [];
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
				//19/10/22 código obsoleto. Se sustituye por el de abajo.
				// if($its->budget_form_item_id == 1 || !$its->budget_form_item_id){
				// 	//$p = explode('|', $its->free_field);
				// 	//$block_id = $p[0];	
				// 	$block_id = $its->budget_form_block_id;
				// }else{
				// 	$block_id = $its->recid;
				// }

				//Buscando el bloque al que pertenece el item:
				// if($its->budget_form_item_id == 1){
				// 	$block_id = $its->recid;
				// }else{
				// 	$block_id = $its->budget_form_block_id;	
				// }
				// 
				// 20/10/22 REVISAR A FONDO ESTA LÓGICA:
				
				if($its->budget_form_block_id){
					$block_id = $its->budget_form_block_id;	
				}else{
					$block_id =	$its->recid;
				}

				//Si el item pertenece al bloque:
				if($block->id == $block_id){
					//Cálculo de importe y descuento:
					$amount = $its->getItemAmount($its);

					//Cálculo importe sin ningún descuento:
					$amountWithoutDiscount = $its->getItemAmount($its, false);

					//Subtotal para importe porcentual:
					if($its->calculated_by_percent && $its->calculated_by_percent > 0){
						$amount = $amountWithoutPercentItems * $its->price;	

						if($its->discount && $its->discount > 0){
							$percentage_ = App\Helpers\helpers::percent($amount, $its->discount);	
							$amount = $amount - $percentage_;
						}
						$amount = round($amount, 2);
						$amountWithoutDiscount = $amountWithoutDiscount + $amount;
					}

					if($its->discount){
						$discount = $its->discount? ' <span style="font-size: 9px;">('.$its->discount.'%)</span>':'';
						$discount_in_lines = true;
					}else{
						$discount = '';	
					}

					//Texto alternativo para líneas a coste 0:
					if($its->item_no_price){
						$precio = $noPriceLinesTexts[$its->item_no_price];
						$importe = $noPriceLinesTexts[$its->item_no_price];
						$amount = 0;
					}else{
						$precio = $its->calculated_by_percent > 0? $its->price.'%':$its->price.$currency->symbol.$discount;
						$importe = $amount.$currency->symbol;
					}

					//Cálculo del importe del bloque:
					$blockAmount = $blockAmount + $amount;
					$blockAmountWithoutDiscount = $blockAmountWithoutDiscount + $amountWithoutDiscount;

					//Sólo items con $print == 1
					if($its->print == 1){
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

							//Precio:
							$htmlItems .= '<td style="text-align: right;">'.$precio.'</td>';	

							//Importe:
							$htmlItems .= '<td style="text-align: right;">'.$importe.'</td>';

							//Observaciones:
							//Manipulamos el font-size de las observations (hasta que encontrar una solución más estable en Trumbowyg):
							$observations = $its->observations? $its->observations:'&nbsp;';
					        //$observations = str_replace("14.4", "11", $its->observations);
					        //$observations = str_replace("14", "11", $observations);
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
							$bloques .= '<th style="background-color: '.$bg_color.'; width: 180px;">'.ucfirst(trans('textos.observaciones')).'</th>';
							$bloques .= '<th style="background-color: '.$bg_color.'; text-align: right; width: 40px;">'.$blockAmount.$currency->symbol.'</th>';
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

			array_push($arr, $blockAmount);
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

			@php
			//Obteniendo total del pedido
			$arr_amounts = $order->getAmount();    
			@endphp

			<tfoot>
				<tr>
					<td colspan="2" style="background-color: #FFF;"></td>
					<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: left;">{{ strtoupper(trans('textos.total')) }}</td>
					<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: right;">
						{{ number_format($arr_amounts['total_amount'], 2) }}{{ $currency->symbol }}
					</td>
				</tr>

				@if($order->discount && $order->discount > 0)	{{-- //&& !$order->final_customer_id --}}
					@php
					// $percentage_ = App\Helpers\helpers::percent($totalAmount, $order->discount);	
					// $totalAmountWithDiscount = $totalAmount - $percentage_;
					@endphp
					<tr>
						<td colspan="2" style="background-color: #FFF;"></td>
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: left;">{{ strtoupper(trans('textos.importe_descuento')) }} ({{ $order->discount }}%)</td>	
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: right;">
							{{ $arr_amounts['total_amount'] }}{{ $currency->symbol }}
						</td>
					</tr>
				@endif

				{{-- Importe sin descuento --}}
				{{-- 05/04/2023: se oculta el importe sin descuentos en el documento a petición de Victor Sánchez --}}
				{{-- @if($order->final_customer_id || $discount_in_lines)
					<tr>
						<td colspan="2" style="background-color: #FFF;"></td>
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: left;">{{ strtoupper(trans('textos.importe_sin_descuentos')) }}</td>	
						<td style="background-color: {{ $bg_color }}; color: #FFF; text-align: right;">
							{{ $arr_amounts['total_amount_without_discounts'] }}{{ $currency->symbol }}
						</td>	
					</tr>
				@endif --}}
			</tfoot>
		</table>

		{!! $bloques !!}

		{{-- Pie de tabla: --}}
		<table class="tblBody" style="margin-top: 0;">
			<tfoot>
				<tr>
					<td style="text-align: right; width: 390px;">{{ trans('textos.total') }}</td>
					<td style="text-align: right; width: 67px">{{ round($totalAmount, 2) }}{{ $currency->symbol }}</td>
					<td style="">&nbsp;
				</tr>
			</tfoot>
		</table>
	@endif

	@include('pdf.partials.conditions-template')

	@include('pdf.partials.footer')
</main>