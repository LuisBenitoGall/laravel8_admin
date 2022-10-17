@extends('admin.partials.layout')

@section('content')

@php
$order_num = $order->order_num? $order->order_num:$order->id;
@endphp

<input type="hidden" id="dtController" value="invoices">

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Volver al pedido --}}
		@can('orders.edit')
			<a class="btn btn-info" href="{{ route('orders.edit', $order) }}"><i class="fa fa-angle-left"></i> {!! ucfirst(trans('textos.volver_pedido')) !!} {{ $order_num }}</a>
		@endcan

		{{-- Volver a facturas --}}
		@can('invoices.index')
			<a class="btn btn-info" href="{{ route('invoices.index') }}"><i class="fa fa-angle-left"></i> {!! ucfirst(trans('textos.volver_facturas')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">						
		<h3>
			{!! trans('textos.facturas_pedido') !!} <u class="mr-5">{{ $order_num }}</u>

			@if($provider->id == session('currentCompany'))
				{!! ucfirst(trans('textos.cliente')) !!}: <u>{{ $customer->name }}</u>
			@else
				{!! ucfirst(trans('textos.proveedor')) !!}: <u>{{ $provider->name }}</u>
			@endif
		</h3>	
	</div>

	<div class="table-responsive">
		<table class="table table-hover dTable" id="tblOrderInvoices">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_fiscal')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.importe')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.estado')) !!}</th>
					<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
				</tr>
			</thead>

			<tbody>
				@php
				$invoice_amount = 0;
				@endphp

				@foreach($invoices as $row)
					@php
					$amount = $row->refund? $row->amount*-1:$row->amount;
					$invoice_amount = $invoice_amount + $amount;
					@endphp

					<tr>
						{{-- Número factura --}}
						@php
						$invoice_num = $row->invoice_num? $row->invoice_num:$row->id;
						@endphp

						<td class="text-right">
							@can('invoices.edit')
								@php
								$route = $provider->id == session('currentCompany')? 'edit':'edit-purchase';
								@endphp
								<a target="_blank" href="{{ route('invoices.'.$route, $row) }}">{{ $invoice_num }}</a>
							@else
								{{ $invoice_num }}
							@endcan
						</td>

						{{-- Fecha fiscal --}}
						<td class="text-right">
							{{ Carbon\Carbon::parse($row->fiscal_date)->format($languages[session()->get('locale')][4]) }}
						</td>

						{{-- Fecha vencimiento --}}
						<td class="text-right">
							@if($row->expiration_date) {{ Carbon\Carbon::parse($row->expiration_date)->format($languages[session()->get('locale')][4]) }} @endif
						</td>

						{{-- Importe factura --}}
						<td class="text-right">
							{{ round($amount, 2) }}{{ $currency->symbol }}
						</td>

						{{-- Estado --}}
						@php
						$status = $row->status? $invoice_status[$row->status]:'';
						@endphp
						<td>
							<span class="xoption-{{ $row->id }}">{{ ucfirst($status) }}</span>
						</td>

						{{-- Acciones --}}
						<td class="text-right">
							{{-- Estado --}}
							@if(!$row->blocked)
								<x-select-status :options="$invoice_status" id="{{ $row->id }}" />
							@endif

							{{-- Imprimir --}}
							<a target="_blank" class="btn btn-info btn-xs" href="{{ route('invoices.print', $row) }}" rel="tooltip" title="{!! ucfirst(trans('textos.factura_imprimir')) !!}">
								<i class="fa fa-print"></i>
							</a>

							{{-- Editar --}}
							@can('invoices.edit')
								<a target="_blank" class="btn btn-info btn-xs" href="{{ route('invoices.'.$route, $row) }}" rel="tooltip" title="{!! ucfirst(trans('textos.factura_editar')) !!}">
									<i class="fa fa-edit"></i>
								</a>
							@endcan

							{{-- Eliminar --}}
							@can('invoices.destroy')
								@if(!$row->blocked)
									<form method="POST" action="{{ route('invoices.destroy', $row) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                            		<input name="_method" type="hidden" value="DELETE">
	                            		{{ csrf_field() }}
	                            		<input type="hidden" name="return" value="by-order">
	                            		<button type="submit" class="btn btn-xs btn-danger frm-confirm" rel="tooltip" title="{{ trans('textos.factura_cancelar') }}" data-msg="{{ trans('textos.factura_confirm_cancelar') }}"><i class="fa fa-trash"></i></button>
	                        		</form>
	                        	@endif
							@endcan

							{{-- Bloquear factura - condiciones: 
								1. Sólo proveedor.
								2. Factura no bloqueada.
								3. Permisos de edición de factura.
							--}}
							@if($provider->id == session('currentCompany'))
								@if(!$row->blocked)
									@can('invoices.edit')
										<a href="{{ route('invoices.block', [$row, 'by-order']) }}" class="btn btn-xs btn-warning close-animation btn-confirm" data-msg="{{ trans('textos.factura_bloquear_confirm') }}" rel="tooltip" title="{!! ucfirst(trans('textos.factura_bloquear')) !!}">
											<i class="fa fa-unlock"></i>
										</a>
									@endcan
								{{-- Factura bloquedada --}}
								@else
									<span class="btn btn-xs btn-danger" rel="tooltip" title="{!! ucfirst(trans('textos.factura_bloqueada')) !!}">
										<i class="fa fa-lock"></i>
									</span>		
								@endif
							@endif
						</td>
					</tr>
				@endforeach
			</tbody>

			<tfoot>
				{{-- Facturado --}}
				<tr>
					<td class="text-right" colspan="3">
						<b>{!! ucfirst(trans('textos.total_facturado')) !!}</b>
					</td>
					<td class="text-right">
						{{ round($invoice_amount, 2) }}{{ $currency->symbol }}	
					</td>
					<td colspan="2">

					</td>
				</tr>

				{{-- Pedido --}}
				<tr>
					<td class="text-right" colspan="3">
						<b>{!! ucfirst(trans('textos.importe_pedido')) !!}</b>
					</td>
					<td class="text-right">
						{{ round($order_amount, 2) }}{{ $currency->symbol }}	
					</td>
					<td colspan="2">

					</td>
				</tr>

				{{-- Pendiente de facturar - condiciones: 
					1. Sólo proveedor:
				--}}
				@if($provider->id == session('currentCompany'))
					@php
					$pendent_amount = $order_amount - $invoice_amount;
					$class = $pendent_amount > 0? 'text-danger':'text-success';
					@endphp
					<tr>
						<td class="text-right" colspan="3">
							<b>{!! ucfirst(trans('textos.pendiente_facturar')) !!}</b>
						</td>
						<td class="text-right">
							<span class="{{ $class }}">{{ round($pendent_amount, 2) }}{{ $currency->symbol }}</span>
						</td>
						<td colspan="2">

						</td>
					</tr>
				@endif
			</tfoot>
		</table>
	</div>
</div>

@endsection