<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblInvoicePayments">
			<thead>
				<tr>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.modo')) !!}</th>
					<th>{!! ucfirst(trans('textos.observaciones')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$total_charged = 0;
				@endphp

				@foreach($payments as $pay)
					@php
					$total_charged = $total_charged + $pay->amount;
					@endphp
					<tr>
						{{-- Fecha --}}
						<td class="text-right">
							{{ $pay->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}	
						</td>

						{{-- Importe --}}
						<td class="text-right">
							{{ $pay->amount }}{{ $currency->symbol }}
						</td>

						{{-- Modo --}}
						<td>
							{{ $pay->modo }}
						</td>

						{{-- Observaciones --}}
						<td>
							{{ $pay->observations }}
						</td>
						
						{{-- Acciones --}}
						<td class="text-right">
							
						</td>
					</tr>
				@endforeach		
			</tbody>

			@if($payments->count())
				<tfoot>
					<tr>
						<td class="text-right">
							<b>{{ trans('textos.cobrado_total') }}</b>
						</td>		
						<td class="text-right">
							<b>{{ round($total_charged, 2) }}{{ $currency->symbol }}</b>	
						</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td class="text-right">
							<b>{{ ucfirst(trans('textos.pendiente')) }}</b>
						</td>		
						<td class="text-right">
							<b>{{ round($invoice->amount_with_iva -  $total_charged, 2) }}{{ $currency->symbol }}</b>	
						</td>
						<td colspan="3"></td>
					</tr>
				</tfoot>
			@endif
		</table>
	</div>
</div>