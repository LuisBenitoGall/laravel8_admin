<th class="tbl-header-left" style="width: 60%;">
	{{-- <h1>{{ ucfirst(trans('textos.pedido')) }} Nº @if($order->order_num) {{ $order->order_num }} @else {{ $order->id }} @endif
		@if($order->old_order)
			({{ $order->old_order }})
		@endif
	</h1>
	--}}

	<h1 style="color: {{ $bg_color }}">
		@if($invoice->refund > 0) {{ ucfirst(trans('textos.abono')) }} @else {{ ucfirst(trans('textos.factura')) }} @endif Nº {{ $invoiceNum }}
	</h1>	

	<p style="display: block; font-size: 11px; font-weight: normal; line-height: 120%; margin: 0 0 5px 0; width: 100%;">
		<span class="item-left" style="color: {{ $bg_color }}; display: inline-block; vertical-align: top; width: 35%;"><b>{{ strtoupper(trans('textos.fecha')) }}:</b></span> 
		<span class="item-left" style="display: inline-block; vertical-align: top; width: 64%;">{{ $fechaLiteral }}</span>
	</p>

	@if($invoice_order || $group)
		<p style="display: block; font-size: 11px; font-weight: normal; line-height: 120%; margin: 0 0 5px 0; width: 100%;">
			<span class="item-left" style="color: {{ $bg_color }}; display: inline-block; vertical-align: top; width: 35%;"><b>{{ strtoupper(trans('textos.proyecto')) }}:</b></span> 
			<span class="item-left" style="display: inline-block; vertical-align: top; width: 64%;">@if($invoice_order) {{ $invoice_order->name }} @elseif($group) {{ $group->name }} @endif</span>
		</p>	

		<p style="display: block; font-size: 11px; font-weight: normal; line-height: 120%; margin: 0 0 5px 0; width: 100%;">
			<span class="item-left" style="color: {{ $bg_color }}; display: inline-block; vertical-align: top; width: 35%;"><b>{{ strtoupper(trans('textos.proyecto_info_')) }}:</b></span>	
			<span class="item-left" style="display: inline-block; vertical-align: top; width: 64%;">@if($invoice_order->project) {{ $invoice_order->project }} @else {{ $invoice_order->name }} @endif</span>
		</p>

		<p style="display: block; font-size: 11px; font-weight: normal; line-height: 120%; margin: 0 0 5px 0; width: 100%;">
			<span class="item-left" style="color: {{ $bg_color }}; display: inline-block; vertical-align: top; width: 35%;"><b>{{ strtoupper(trans('textos.responsable')) }}:</b></span> 
			<span class="item-left" style="display: inline-block; vertical-align: top; width: 64%;">@if($invoice_order) {{ $invoice_order->nombre }} {{ ucwords($invoice_order->surname) }} @endif</span>
		</p>
	@endif	
</th>

<th class="tbl-header-right">
	<h1 style="color: {{ $bg_color }}">{{ ucfirst(trans('textos.cliente')) }} </h1>	

	<p style="color: {{ $bg_color }}; display: block; font-size: 11px; font-weight: normal; line-height: 120%; margin: 0 0 5px 0;">
		<strong>{{ $customer->name }}</strong>
	</p>

	<p style="display: block; font-size: 11px; font-weight: normal; line-height: 120%; margin: 0 0 5px 0;">
		@if($customer_workplace)
			{{ $customer_workplace->address }}<br>
			{{ $customer_workplace->cp }} {{ $customer_workplace->Town }}<br>
		@endif

		{{ $customer->nif }}<br>
		@if($customer_manager)
			{{ $customer_manager->name }} {{ ucwords($customer_manager->surname) }}
		@endif
	</p>
</th>