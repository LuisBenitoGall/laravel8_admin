@extends('admin.partials.layout')

@section('content')

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
                            <span><strong>{{ trans('textos.fecha_factura') }}:</strong> Marh 18, 2014</span><br/>
                            <span><strong>Due Date:</strong> March 24, 2014</span>
                        </p>
                    </div>
                </div>

                <div class="table-responsive m-t">
                    <table class="table invoice-table">
                        <thead>
	                        <tr>
	                            <th>Item List</th>
	                            <th>Quantity</th>
	                            <th>Unit Price</th>
	                            <th>Tax</th>
	                            <th>Total Price</th>
	                        </tr>
                        </thead>

                        <tbody>


                        </tbody>
                    </table>
                </div>
			</div>
		</div>
	</div>
</div>

@endsection