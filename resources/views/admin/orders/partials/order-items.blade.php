@php
$colspan = $env == 'invoices'? 7:6;
$see_iva = $env == 'invoices'? true:false;
$tbl = $env == 'invoices'? 'tblInvoiceItems':'tblOrderItems';
@endphp

<input type="hidden" id="seeIva" value="{{ $see_iva }}">

<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dtabla" id="{{ $tbl }}">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{{ ucfirst(trans('textos.producto')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.cantidad')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.precio_unidad')) }}</th>
					@if($env == 'invoices')
						<th class="text-center">IVA</th>
					@endif
					<th class="text-center">{{ ucfirst(trans('textos.jornadas')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.descuentos')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.importe')) }}</th>
					<th>{{ ucfirst(trans('textos.centro_coste')) }}</th>
					<th>{{ ucfirst(trans('textos.estado')) }}</th>
					<th class="text-center">{{ ucfirst(trans('textos.acciones')) }}</th>
				</tr>
			</thead>

			<tfoot>
				{{-- @if($order->discount)
					<tr>
		                <td colspan="{{ $colspan }}" class="text-right"><b>{{ ucfirst(trans('textos.descuento')) }}</b></td>
		                <td class="text-right">{{ $order->discount }}%</td>
		                <td></td>
		                <td></td>
		            </tr>
				@endif --}}

	            <tr>
	                <td colspan="{{ $colspan }}" class="text-right"><b>{{ trans('textos.importe_total') }} *</b></td>
	                <td class="text-right">{{ $order_amount }}{{ $currency->symbol }}</td>
	                <td></td>
	                <td></td>
	                <td></td>
	            </tr>

	            @if(isset($invoice) && $invoice->with_iva > 0)
		            <tr>
		                <td colspan="{{ $colspan }}" class="text-right"><b>{{ trans('textos.importe') }} + IVA</b></td>
		                {{-- ({{ $invoice->with_iva }}%) --}}
		                <td class="text-right">{{ round($invoice->amount_with_iva, 2) }}{{ $currency->symbol }}</td>
		                <td></td>
		                <td></td>
		                <td></td>
		            </tr>
		        @endif
	        </tfoot>
		</table>

		{{-- Aviso sobre cálculo importe de líneas --}}
		<div class="col-12">
			<small class="text-warning">
			* {{ trans('textos.importe_total_texto') }}
			</small>
		</div>
	</div>
</div>
