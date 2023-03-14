<div class="row">
	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblInvoicePayments">
			<thead>
				<tr>
					<th class="text-center wdt150">{!! ucfirst(trans('textos.fecha')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.metodo')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.entidad')) !!}</th>
					<th class="wdt100">{!! ucfirst(trans('textos.cuenta_bancaria')) !!}</th>
					<th class="wdt250">{!! ucfirst(trans('textos.observaciones')) !!}</th>
					<th class="text-center wdt100">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$total_charged = 0;
				$text_confirm = $is_sale? trans('textos.cobro_confirm_eliminar'):trans('textos.pago_confirm_eliminar');
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
							{{-- @if($pay->mode) {!! App\Helpers\helpers::paymentMethods($pay->mode) !!} @endif --}}
							{{ $pay->method }}
						</td>

						{{-- Banco --}}
						<td>
							{{ $pay->bank }}		
						</td>

						{{-- Cuenta bancaria --}}
						<td>
							{{ $pay->account }}	
						</td>

						{{-- Observaciones --}}
						<td>
							{{ $pay->observations }}
						</td>
						
						{{-- Acciones --}}
						<td class="text-right">
							{{-- Editar --}}
							<a href="#" class="btn btn-xs btn-info btnEditPayment" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}" data-toggle="modal" data-target="#modal-edit-payment" data-id="{{ $pay->id }}"><i class="fa fa-edit"></i></a>

							{{-- Eliminar --}}
							<form method="POST" action="{{ route('payments.destroy', $pay) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                            <input name="_method" type="hidden" value="DELETE">
	                            {{ csrf_field() }}
	                            <button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.eliminar')) }}" data-msg="{{ $text_confirm }}"><i class="fa fa-trash"></i></button>
	                        </form>
						</td>
					</tr>
				@endforeach		
			</tbody>

			@if($payments->count())
				<tfoot>
					<tr>
						<td class="text-right">
							<b>@if($is_sale) {{ trans('textos.cobrado_total') }} @else {{ trans('textos.pagado_total') }} @endif</b>
						</td>		
						<td class="text-right">
							<b>{{ round($total_charged, 2) }}{{ $currency->symbol }}</b>	
						</td>
						<td colspan="5"></td>
					</tr>
					<tr>
						<td class="text-right">
							<b>{{ ucfirst(trans('textos.pendiente')) }}</b>
						</td>		
						<td class="text-right">
							<b>{{ round($invoice->amount_with_iva -  $total_charged, 2) }}{{ $currency->symbol }}</b>	
						</td>
						<td colspan="5"></td>
					</tr>
				</tfoot>
			@endif
		</table>
	</div>
</div>