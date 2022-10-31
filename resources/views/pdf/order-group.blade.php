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

				@php
				$amount = $order->bag_amount? $order->bag_amount:$orderGroupAmount;
				@endphp

				<th style="background-color: {{ $bg_color }}; text-align: right; width: 20%;">{{ number_format($amount, 2) }}{{ $currency->symbol }}</th>
			</tr>
		</thead>	
	</table>

	@include('pdf.partials.conditions-template')

	@include('pdf.partials.footer')
</main>