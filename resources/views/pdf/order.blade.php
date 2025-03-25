@php
$orderNum = $order->order_num? $order->order_num:$order->id;
$bg_color = $company_settings->base_color_orders? $company_settings->base_color_orders:'#FFF';
@endphp

@include('pdf.partials.head', ['header_title' => ucfirst(trans('textos.pedido')).' #'.$orderNum])

@include('pdf.partials.header', [
	'standard' => true, 
	'company_settings' => $company_settings, 
	'bg_color' => $bg_color, 
	'left_h1' => ucfirst(trans('textos.pedido')).' Nº '.$orderNum,
	'left_title' => $order->name? $order->name:false,
	'left_manager' => $provider_manager? $provider_manager->name.' '.$provider_manager->surname:false,
	'left_commercial' => false,
	'right_h1' => ucfirst(trans('textos.cliente')),
	'right_manager' => $customer_manager? $customer_manager->name.' '.$customer_manager->surname:'',
])

@php
$i = 1;
$total = 0;
$html = '';

$depositAmount = 0;
$depositDiscountAmount = 0;

//Calculando importe sin items de cálculo porcentual:
$amountWithoutPercentItems = 0;

$my_deposit_lines = [];
@endphp

@foreach($lines as $l)
	{{-- Si el importe del producto NO se calcula mediante porcentaje --}}
	@if(!$l->calculated_by_percent)
		@php
		$subt = $l->qty * $l->price;

		//Contabilizando jornadas:
		if($l->days > 1){
			$subt = $subt * $l->days;

			//Aplicando descuento de días continuos:
			if($l->days_continue == 1 && $l->days_continue_discount){
				$jc_percentage = App\Helpers\helpers::percent($subt, $l->days_continue_discount);
				$subt = $subt - $jc_percentage;
			}
		}

		$percentage = App\Helpers\helpers::percent($subt, $l->discount);	
		$subt = $subt - $percentage;
		$amountWithoutPercentItems = $amountWithoutPercentItems + $subt;
		@endphp
	@endif
@endforeach

@if($lines->count() > 0)
	@foreach($lines as $row)
		@php
		$html .= '<tr>';
			$html .= '<td class="text-right" style="text-align: right; width: 15px;">'.$i.'</td>';
			$html .= '<td style="width: 250px;">'.$row->concept.'</td>';
			//Qty:
			$html .= '<td class="text-right" style="text-align: right; width: 60px;">'.$row->qty;
				if($row->days > 1){
					$html .= '<br><small style="font-size: 9px;">(x '.$row->days.' '.trans('textos.jornadas');
						if($row->days_continue_discount && $row->days_continue_discount > 0){
							$html .= '<br>- '.$row->days_continue_discount.'%';
						}
					$html .= ')</small>';
				}
			$html .= '</td>';
			$html .= '<td class="text-right" style="text-align: right; width: 90px;">'.$row->price.$currency->symbol;

			//Importe de la línea, si es por porcentaje:
			if($row->calculated_by_percent && $row->calculated_by_percent > 0){
				$amount = $amountWithoutPercentItems * $row->price;
				if($row->discount && $row->discount > 0){
					$percentage_ = App\Helpers\helpers::percent($amount, $row->discount);	
					$amount = $amount - $percentage_;
				}

			//NO por porcentaje:
			}else{
				$amount = $row->getItemAmount($row);
			}

			if($row->discount){
				$html .= ' <span style="font-size: 9px;">('.$row->discount.'%)</span>';	
			}
			$html .= '</td>';

			$html .= '<td class="text-right" style="text-align: right; width: 50px;">'.number_format($amount, 2).$currency->symbol.'</td>';
			$html .= '<td class="text-left">'.$row->observations.'</td>';
		$html .= '</tr>';
		$i++;
		$total = $total + $amount;
		@endphp
	@endforeach

	{{-- Líneas de depósito: --}}
	@if(count($my_deposit_lines))
		@foreach($my_deposit_lines as $mdl)
			@php
			//Importe de la línea:
			$amount = $row->getItemAmount($mdl);

			$depositAmount = $depositAmount + $amount;	

			$depositDiscountAmount = $depositDiscountAmount + $amount;

			//Número de pedido.
			$nOrder = $mdl->order_num? $mdl->order_num:$mdl->order_id;
			if($mdl->old_order){
				$nOrder .= ' ('.$mdl->old_order.')';	
			}

			$html .= '<tr>';
				$html .= '<td class="td-success" style="text-align: center; width: 15px;">'.$i.'</td>';	
				$html .= '<td class="td-success text-left" style="font-size: 10px; width: 250px;">'.trans('textos.descuento_pedido').' '.$nOrder.'</td>';
				$html .= '<td class="td-success" style="text-align: center; width: 50px;">'.$mdl->qty.'</td>';
				$html .= '<td class="td-success" style="text-align: center; width: 100px;">-'.$mdl->price.$currency->symbol.'</td>';
				$html .= '<td class="td-success" style="text-align: center; width: 50px;">-'.number_format($amount, 2).$currency->symbol.'</td>';
				$html .= '<td class="td-success text-left">'.$mdl->observations.'</td>';
			$html .= '</tr>';

			$i++;
			@endphp
		@endforeach
	@endif

	{{-- Descuento sobre el pedido --}}
	@if($order->discount)
		@php
		$t_percentage = App\Helpers\Helpers::percent($total, $order->discount);
		$total = $total - $t_percentage;
		@endphp
	@endif

	{{-- Descuento líneas de depósito --}}
	@if($depositAmount > 0)
		@php
		$total = $total - $depositDiscountAmount;
		@endphp
	@endif
@endif

<main>
	<table class="tblBody">
		<thead>
			<tr>
				<th style="text-align: left;">
					{{-- 06-10-20: comentada la confirmación de pedido hasta nuevo aviso.

					@if(!$order->confirm_code_date)
						<a target="_blank" href="{{ route('orders.confirm', [$order->confirm_code]) }}" style="color: #3db9dc;">{{ trans('textos.pedido_confirmar') }}</a>
					@endif --}} &nbsp;
				</th>	
				<th style="background-color: {{ $bg_color }}; width: 20%;">{{ strtoupper(trans('textos.total')) }}</th>
				<th style="background-color: {{ $bg_color }}; text-align: right; width: 20%;">{{ number_format($total, 2) }}{{ $currency->symbol }}</th>
			</tr>
		</thead>	
	</table>

	<table class="tblBody">
		<thead>
			<tr>
				<th style="background-color: {{ $bg_color }}; text-align: center;"></th>
				<th style="background-color: {{ $bg_color }};">
					{{ ucfirst(trans('textos.producto')) }}
				</th>
				<th style="background-color: {{ $bg_color }}; text-align: center;">
					{{ ucfirst(trans('textos.cantidad')) }}	
				</th>
				<th style="background-color: {{ $bg_color }}; text-align: center;">
					{{ ucfirst(trans('textos.precio_unidad')) }}
				</th>
				<th style="background-color: {{ $bg_color }}; text-align: center;">
					{{ ucfirst(trans('textos.importe')) }}
				</th>
				<th style="background-color: {{ $bg_color }}; text-align: left;">
					{{ ucfirst(trans('textos.observaciones')) }}
				</th>
			</tr>
		</thead>

		<tbody>
			{!! $html !!}	
		</tbody>	

		<tfoot>
			<tr>
				<td colspan="4" class="text-right">
					{{ ucfirst(trans('textos.importe_total')) }}

					@if($order->discount)
						<span style="font-size; 9px">({{ $order->discount }}%)</span>
					@endif
				</td>
				<td class="text-center">
					{{ round($total, 2) }}{{ $currency->symbol }}
				</td>
				<td></td>
			</tr>	
		</tfoot>
	</table>

	@include('pdf.partials.conditions-template')

	@include('pdf.partials.footer')
</main>