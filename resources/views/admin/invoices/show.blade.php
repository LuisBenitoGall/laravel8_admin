@extends('admin.partials.layout')

@section('content')

{{-- Avisos --}}
@include('admin.invoices.partials.invoice-warning-template', ['accounting_options' => $accounting_options])

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Listado facturas --}}
		@can('invoices.index')
			<a href="{{ route('invoices.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_facturas') !!}
			</a>
		@endcan

		{{-- Editar factura - condiciones: 
			1. Permisos de edición.
			2.
		--}}
		@can('invoices.edit')
			<a target="_blank" href="{{ route('invoices.edit', $invoice) }}" class="btn btn-info" rel="tooltip" title="{{ ucfirst(trans('textos.editar')) }}"><i class="fa fa-edit"></i></a>
		@endcan

		{{-- Imprimir - condiciones: 
			1. Para facturas simples.
		--}}
		@if(!$order->is_group)
			<a href="{{ route('invoices.print', $invoice) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
				<i class="fa fa-print"></i>
			</a>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body p-xl">
				<div class="row">
                    <div class="col-sm-6">
                        <h5>{{ trans('textos.de') }}:</h5>
                        <address>
                            <strong>{{ $provider->name }}</strong><br>
                            {{ $provider_workplace->address }}<br>
                            {{ $provider_workplace->cp }} - {{ $provider_workplace->Town }}<br>
                            @if($provider->phone_number)
                            	<abbr title="Phone">T:</abbr> {{ $provider->phone_number }}
                            @endif
                        </address>
                    </div>

                    <div class="col-sm-6 text-right">
                        <h4>{{ trans('textos.factura') }} No.</h4>

                        @php
                        $invoice_num = $invoice->invoice_num? $invoice->invoice_num:($invoice->invoice_old_num? $invoice->invoice_old_num:$invoice->id);
                        @endphp
                        <h4 class="text-navy">{{ $invoice_num }}</h4>
                        <span>{{ ucfirst(trans('textos.a')) }}:</span>
                        <address>
                            <strong>{{ $customer->name }}</strong><br>
                            {{ $customer_workplace->address }}<br>
                            {{ $customer_workplace->cp }} - {{ $customer_workplace->Town }}<br>
                            @if($customer->phone_number)
                            	<abbr title="Phone">T:</abbr> {{ $customer->phone_number }}
                            @endif
                        </address>
                        <p>
                            <span><strong>{{ trans('textos.factura_fecha') }}:</strong> @if($invoice->fiscal_date) {!! \Carbon\Carbon::parse($invoice->fiscal_date)->format($languages[session()->get('locale')][4]) !!} @endif</span><br/>
                            <span><strong>{{ trans('textos.fecha_vencimiento') }}:</strong> @if($invoice->expiration_date) {!! \Carbon\Carbon::parse($invoice->expiration_date)->format($languages[session()->get('locale')][4]) !!} @endif</span>
                        </p>
                    </div>
                </div>

                <div class="table-responsive m-t">
                    <table class="table invoice-table">
                        <thead>
	                        <tr>
	                            <th>{{ ucfirst(trans('textos.concepto')) }}</th>
	                            <th class="text-center">{{ trans('textos.cantidad') }}</th>
	                            <th class="text-center">{{ trans('textos.precio') }}</th>
	                            <th class="text-center">IVA</th>
	                            <th class="text-center">{{ trans('textos.importe') }}</th>
	                        </tr>
                        </thead>

                        <tbody>
                        	@php
                        	$subtotal = 0;
                        	$iva = 0;
                        	$total = 0;
                        	@endphp

                        	@foreach($items as $item)
                        		@php
	                        	$subtotal = $subtotal + $item->amount;
	                        	$percent = App\Helpers\helpers::percent($item->amount, $item->iva);
	                        	$iva = $iva + $percent;
	                        	$total = $total + $item->amount + $percent;
	                        	@endphp

                        		<tr>
                        			<td>{{ $item->concept }}</td>
                        			<td class="text-right">{{ $item->qty }}</td>
                        			<td class="text-right">{{ $item->price }}{{ $currency->symbol }}</td>
                        			<td class="text-right">{{ $item->iva }}%</td>
                        			<td class="text-right">{{ $item->amount }}{{ $currency->symbol }}</td>
                        		</tr>
                        	@endforeach
                        </tbody>
                    </table>
                </div>

                <table class="table invoice-total">
                    <tbody>
	                    <tr>
	                        <td><strong>{{ ucfirst(trans('textos.subtotal')) }}:</strong></td>
	                        <td>{{ $subtotal }}{{ $currency->symbol }}</td>
	                    </tr>
	                    <tr>
	                        <td><strong>IVA :</strong></td>
	                        <td>{{ $iva }}{{ $currency->symbol }}</td>
	                    </tr>
	                    <tr>
	                        <td><strong>{{ ucfirst(trans('textos.total')) }}:</strong></td>
	                        <td>{{ $total }}{{ $currency->symbol }}</td>
	                    </tr>
                    </tbody>
                </table>

                
                @if($invoice->observations)
	                <div class="well m-t">
	                	<strong>{{ ucfirst(trans('textos.observaciones')) }}</strong>
	                    {!! $invoice->observations !!}
	                </div>
	            @endif
			</div>
		</div>
	</div>
</div>

@endsection