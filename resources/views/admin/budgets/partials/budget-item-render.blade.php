@php
function renderBudgetItem($is, $block_id, $currency, $amountWithoutPercentItems, $order){
	$amountWithoutDiscounts = 0;

	$html = '<div class="block-item" id="item-'.$is->id.'">';
		$html .= '<h5>';
			//Imprimir:
			$print = $is->print == 1? 'text-success':'text-danger';
			$title = $is->print == 1? ucfirst(trans('textos.se_imprime')):ucfirst(trans('textos.imprimir_no'));
			$html .= '<i class="fa fa-print mr-2 itemPrint '.$print.'" rel="tooltip" title="'.$title.'" data-print="'.$is->print.'"></i>';

			//Concepto:
			$html .= $is->concept;
		$html .= '</h5>';
		$html .= '<div class="row">';
			$html .= '<div class="col-1 sinpadding-right">';
				//Opciones para pedidos facturados < 100%
				if(!$order || $order->percentage_invoiced < 100){
					//Editar:
					$html .= '<a href="#" class="text-warning mr-2 btnEditBudgetItem" data-block="'.$block_id.'" rel="tooltip" title="'.ucfirst(trans('textos.editar')).'" wire:click="editItem('.$is.')"><i class="fa fa-edit"></i></a>';

					//Eliminar:
					$html .= '<form method="POST" action="/admin/items/'.$is->id.'/budgets" class="frm-inline mr-2" role="form" accept-charset="UTF-8">';
						$html .= '<input name="_method" type="hidden" value="DELETE">';
						$html .= csrf_field();
						$html .= '<button type="submit" class="btn-empty text-danger frm-confirm" rel="tooltip" title="'.ucfirst(trans('textos.linea_eliminar')).'" data-msg="'.ucfirst(trans('textos.presupuesto_linea_confirm_eliminar')).'"><i class="fa fa-trash"></i></button>';
					$html .= '</form>';

					//Duplicar:
					$html .= '<a href="/admin/items/'.$is->id.'/copy" class="text-warning mr-2 btn-confirm" data-msg="'.trans('textos.presupuesto_linea_duplicar_confirm').'" rel="tooltip" title="'.ucfirst(trans('textos.duplicar')).'"><i class="fa fa-copy"></i></a>';
				}

				//Configuraciones de venta:
				if($is->pssId){
					//$html .= 'a configurarrrrrrrrrrr';
				}

			$html .= '</div>';
			//Cantidad:
			$html .= '<div class="col-2">';	
				$html .= '<b>'.trans('textos.cantidad').':</b> <span class="mr-5 itemQty">'.$is->qty.'</span>';
			$html .= '</div>';
			//Precio:
			$html .= '<div class="col-2 sinpadding-right">';
				if($is->line_no_price > 0){
					$price = App\Helpers\Helpers::statusBudgetLinesWithoutPrice($is->line_no_price);	
					$data_price = '';
				}else{
					$price = number_format($is->price, 2).$currency->symbol;
					$data_price = $is->price; //intval(str_replace(",", "", $is->price)); 
					$data_price = $is->price > 0? number_format($is->price, 2, '.', ''):''; 
				}

				//Precio porcentual o fijo:
				if($is->calculated_by_percent && $is->calculated_by_percent > 0){
					$html .= '<b>'.trans('textos.precio_porcentual').':</b> <span class="mr-5 itemPrice byPercent" data-price="'.$is->price.'" data-bp="'.$is->price.'">'.$price.'</span>';
				}else{
					//$data_price = is_float($data_price)? number_format($data_price, 2):$data_price;
					$html .= '<b>'.trans('textos.precio').':</b> <span class="mr-5 itemPrice" data-price="'.$data_price.'">'.$price.'</span>';
				}									
			$html .= '</div>';
			//Descuento:
			$html .= '<div class="col-2 sinpadding-right">';
				$discount = $is->discount? $is->discount:'0';
				$html .= '<b>'.ucfirst(trans('textos.descuento')).':</b> <span class="mr-5 itemDiscount" data-discount="'.$discount.'">'.$discount.'%</span>';
			$html .= '</div>';
			//Subtotal:
			$html .= '<div class="col-2 sinpadding-right">';
				//$subtotal = $is->getItemAmount($is);
				$subtotal = $is->qty * $is->price;

				//Subtotal para importe porcentual:
				if($is->calculated_by_percent && $is->calculated_by_percent > 0){
					$amountByPercent = 'amountByPercent';
					$subtotal = $amountWithoutPercentItems * $is->price;

					if($is->discount && $is->discount > 0){
						$percentage_ = App\Helpers\Helpers::percent($subtotal, $is->discount);	
						$subtotal = $subtotal - $percentage_;
					}
					$subtotal = round($subtotal, 2);

				}else{
					$amountByPercent = '';
				}

				//Contabilizando jornadas:
				if($is->days > 1){
					$subtotal = $subtotal * $is->days;

					//Aplicando descuento de días continuos:
					if($is->days_continue == 1 && $is->days_continue_discount){
						$jc_percentage = App\Helpers\Helpers::percent($subtotal, $is->days_continue_discount);
						$subtotal = $subtotal - $jc_percentage;
					}
				}

				//Importe sin considerar los descuentos sobre items:
				$amountWithoutDiscounts = $subtotal;

				$percentage = App\Helpers\Helpers::percent($subtotal, $is->discount);	
				$subtotal = $subtotal - $percentage;

				$subtotal_ = is_float($subtotal)? number_format($subtotal, 2):$subtotal;
				
				$html .= '<b>'.trans('textos.subtotal').':</b> <span class="mr-5 itemSubtotal '.$amountByPercent.'" data-subtotal="'.$subtotal.'">'.$subtotal_.$currency->symbol.'</span>';
			$html .= '</div>';
			//Jornadas:
			$html .= '<div class="col-2">';
				$html .= '<b>'.ucfirst(trans('textos.jornadas')).':</b> <span class="mr-5 itemDays">'.$is->days.'</span>';
			$html .= '</div>';
			//J/C:
			$html .= '<div class="col-1">';
				$jc = $is->days_continue == 1? '<i class="fa fa-check text-success"></i>':'<i class="fa fa-ban text-danger"></i>';
				$html .= '<b>J/C:</b> <span class="mr-5 itemJC" data-jc="'.$is->days_continue.'">'.$jc.'</span>';
			$html .= '</div>';
			//Descuento por días continuos:
			$html .= '<input type="hidden" class="itemJCDiscount" value="'.$is->days_continue_discount.'">';
		$html .= '</div>';
		$html .= '<div class="itemObservations" style="font-style: italic;">';
			$html .= $is->observations;
		$html .= '</div>';
	$html .= '<hr></div>';

	$data['html'] = $html;
	$data['amountWithoutDiscounts'] = $amountWithoutDiscounts;
	$data['amount'] = $subtotal; 

	return $data;
}
@endphp