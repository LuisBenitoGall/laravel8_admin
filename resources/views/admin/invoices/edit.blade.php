@extends('admin.partials.layout')

@section('content')

@php
//Campos que se bloquean ante factura bloqueada o para compras, campos que son gestionados por el proveedor.
$disabled = $invoice->blocked || ($order->customer_id == session('currentCompany') && $provider_account)? true:false;
$readonly = $invoice->blocked && !$is_sale || ($order->customer_id == session('currentCompany') && $provider_account)? true:false;
@endphp

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

		{{-- Presupuesto - condiciones: 
			1. Permisos de edición.
			2. Pedido existe en formato presupuesto.
			3. Se trata de una venta.
			...
		--}}
		@can('budgets.edit')
			@if($order->is_budget && $is_sale)
				<a target="_blank" href="{{ route('budgets.edit', $order->id) }}" class="btn btn-info">
					<i class="fa fa-file-alt"></i>
					{!! ucfirst(trans('textos.presupuesto')) !!}
				</a>	
			@endif
		@endcan

		{{-- Pedido --}}
		@can('orders.edit')
			<a target="_blank" href="{{ route('orders.edit', $order->id) }}" class="btn btn-info">
				@if($order->is_group)
					<i class="fa fa-object-group"></i>
					{!! ucfirst(trans('textos.agrupacion')) !!}
				@else
					<i class="fa fa-shopping-cart"></i>
					{!! ucfirst(trans('textos.pedido')) !!}
				@endif
			</a>	
		@endcan

		{{-- Nueva línea --}}
		@can('orders.edit')
			{{-- @if(!$order->invoiced) --}}
			@if($order->from_customer == 1)
				<a href="#" class="btn btn-info btnAddItem" data-toggle="modal" data-target="#modal-new-line">
					<i class="fa fa-plus"></i> {!! ucfirst(trans('textos.linea_nueva')) !!}
				</a>
		    @endif
		@endcan

		{{-- Facturas del pedido --}}
		@if($invoices->count() > 1)
			<a href="{{ route('invoices.by-order', $order->id) }}" class="btn btn-info" rel="tooltip">
				<i class="fa fa-file-invoice-dollar"></i>
				{{ ucfirst(trans('textos.facturas_pedido')) }}
			</a>	
		@endif

		{{-- Generar abono - condiciones: 
			1. Permisos de edición de facturas.
			2. Factura no bloqueada.
			3. Facturas que NO sean de abono.
			4. Exclusivo proveedor.
			...
		--}}
		@can('invoices.edit')
			@if(!$invoice->blocked && !$invoice->refund && $order->provider_id == session('currentCompany'))
				<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-generate-refund">
					<i class="fa fa-hand-holding-usd"></i>
					{{ ucfirst(trans('textos.abono_generar')) }}
				</a>
			@endif
		@endcan

		{{-- Agregar cobro - condiciones: 
			1. No debe tratarse de una factura de abono.
			2. Ser una venta.
			...
		--}}
		@if(!$invoice->refund && $is_sale)
			<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#modal-add-payment">
				<i class="fa fa-cash-register"></i>
				{{ ucfirst(trans('textos.cobro_add')) }}
			</a>
		@endif

		{{-- Imprimir - condiciones 
			1. Para facturas simples.
		--}}
		@if(!$order->is_group)
			<a href="{{ route('invoices.print', $invoice) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
				<i class="fa fa-print"></i>
			</a>

			<a href="{{ route('invoices.print', [$invoice, true]) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir_bn')) !!}">
				<i class="fa fa-print"></i> B/N
			</a>
		@endif

		{{-- Cancelar factura - condiciones:
			1. Permisos de cancelación de factura.
			2. Acción permitida.
			3. Factura no bloqueada.
			4. Exclusivo proveedor.
			...
		--}}
        @can('invoices.destroy')
        	@if($permitted && !$invoice->blocked && $order->provider_id == session('currentCompany'))
	        	<form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                <input name="_method" type="hidden" value="DELETE">
	                {{ csrf_field() }}
	                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.factura_cancelar')) }}" data-msg="{{ ucfirst(trans('textos.factura_cancelar_confirm')) }}"><i class="fa fa-trash"></i></button>
	            </form>
	        @endif
        @endcan

		{{-- Bloquear factura / factura bloqueada - condiciones: 
			1. Permisos de edición de facturas.
			2. Ser una venta.
		--}}
		@can('invoices.edit')
			@if($is_sale)
				@if(!$invoice->blocked)
					<a href="{{ route('invoices.block', $invoice) }}" class="btn btn-warning close-animation btn-confirm ml-3" data-msg="{{ trans('textos.factura_bloquear_confirm') }}" rel="tooltip" title="{!! ucfirst(trans('textos.factura_bloquear')) !!}">
						<i class="fa fa-unlock"></i>
					</a>
				@else
					<a href="{{ route('invoices.unlock', $invoice) }}" class="btn btn-danger close-animation btn-confirm ml-3" data-msg="{{ trans('textos.factura_desbloquear_confirm') }}" rel="tooltip" title="{!! ucfirst(trans('textos.factura_bloqueada')) !!}">
						<i class="fa fa-lock"></i>
					</a>		
				@endif
			@endif
		@endcan
	</div>
</div>

<input type="hidden" id="dtController" value="items">
<input type="hidden" id="myEnvironment" value="invoices">
<input type="hidden" id="myOrder" value="{{ $order->id }}">

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe pedido flotante: --}}
				@livewire('budget-amount', ['budget_amount' => $invoice->amount_with_iva, 'currency' => $currency->symbol, 'budget_amount_without_discount' => false, 'environment' => 'invoices'])

				<h3 class="mb-3">
					{{-- Factura abono --}}
					@if($invoice->refund)
						{!! ucfirst(trans('textos.factura_abono')) !!}

					{{-- Factura de cobro --}}
					@else
						@if($order->is_group)
							{!! ucfirst(trans('textos.factura_agrupacion')) !!}
						@else
							{!! ucfirst(trans('textos.factura')) !!} 
						@endif
					@endif

					<u>
						@if($invoice->invoice_num)
							{{ $invoice->invoice_num }}
						@elseif($invoice->invoice_old_num)
							{{ $invoice->invoice_old_num }}
						@else
							{{ $invoice->id }}
						@endif
					</u>

					{{-- Factura devuelta --}}
					@if($refund)
						<small class="ml-3">
							<a target="_blank" class="text-danger" href="{{ route('invoices.edit', $refund) }}"> 
								{{ trans('textos.factura_reintegrada') }} 
								@if($refund->invoice_num)
									{{ $refund->invoice_num }}
								@else
									{{ $invoice->id}}
								@endif
							</a>
						</small>
					@endif
				</h3>

				{{-- Info --}}
				<div class="mb-3" style="width: 90%;">	
					{{-- Cliente --}}
					@include('admin.orders.partials.data-customer')

					{{-- Proveedor --}}
					@include('admin.orders.partials.data-provider')

					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_creacion')) !!}: 
						<b>
							{{ $invoice->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.actualizado')) !!}: 
						<b>
							{{ $invoice->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Importe facturado --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.importe_facturado')) !!}: 

						@if($invoice->irpf > 0)
							<small>(-{{ $invoice->irpf }}% IRPF)</small>
						@endif

    					<b>{!! $invoice->amount !!}{{ $currency->symbol }}</b>
					</label>

					{{-- Total factura --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.factura_total')) !!}: 
    					<b>{!! $invoice->amount_with_iva !!}{{ $currency->symbol }}</b>
					</label>

					{{-- Incluir IVA: - condiciones: 
						1. Exclusivo proveedores. 
					--}}
					@if($order->provider_id == session('currentCompany'))
						<label class="mr-5">
							{!! ucfirst(trans('textos.incluye_iva')) !!}:

							@if(!$invoice->blocked)
								<label class="custom-chk-rdb" rel="tooltip">
					                <input type="checkbox" name="with_iva" @if($invoice->with_iva) checked @endif>
					                <span class="chk chksmall btnWithIva mt-1 ml-2" data-invoice="{{ $invoice->id }}"></span>
					            </label>

							@else
					        	@if($invoice->with_iva)
					        		{{-- <b>{{ $invoice->with_iva }}%</b> --}} <i class="fa fa-check text-success ml-2"></i> 
					        	@else
					        		<i class="fa fa-times text-danger ml-2"></i>
					        	@endif
					        @endif

					        @if(!$invoice->with_iva) <small class="text-warning">{!! ucfirst(trans('textos.facturado_sin_iva')) !!}</small> @endif
						</label>
					@endif
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.factura')) !!}</a>
			  		</li>

			  		@if($order->is_group)
			  			<li class="nav-item @if($tab == 'orders') active @endif">
				  			<a class="nav-link @if($tab == 'orders') active @endif" href="#tab-orders" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pedidos')) !!}</a>	
				  		</li>
			  		@else
				  		<li class="nav-item @if($tab == 'items') active @endif">
				  			<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.lineas_pedido')) !!}</a>	
				  		</li>
				  	@endif

				  	@if($is_sale)
				  		<li class="nav-item @if($tab == 'template') active @endif">
				  			<a class="nav-link @if($tab == 'template') active @endif" href="#tab-template" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.plantilla')) !!}</a>	
				  		</li>
				  	@endif

			  		<li class="nav-item @if($tab == 'payments') active @endif">
			  			@php
			  			$text = $is_sale? 'cobros':'pagos';
			  			@endphp
			  			<a class="nav-link @if($tab == 'payments') active @endif" href="#tab-payments" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.'.$text)) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.invoices.partials.invoice-info')	
					</div>

					@if($order->is_group)
						{{-- Pedidos --}}
						<div class="tab-pane fade @if($tab == 'orders') show active @endif" role="tabpanel" id="tab-orders">
							@include('admin.invoices.partials.invoice-orders')
						</div>
					@else
						{{-- Items: --}}
						<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
							@include('admin.orders.partials.order-items', ['env' => 'invoices'])
						</div>
					@endif

					{{-- Plantilla --}}
					@if($is_sale)
						<div class="tab-pane fade @if($tab == 'template') show active @endif" role="tabpanel" id="tab-template">
							@include('admin.invoices.partials.invoice-template')	
						</div>
					@endif

					{{-- Cobros --}}
					<div class="tab-pane fade @if($tab == 'payments') show active @endif" role="tabpanel" id="tab-payments">
						@include('admin.invoices.partials.invoice-payments')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Add payment --}}
@include('admin.invoice_payments.partials.modal-add-payment')

{{-- Edit payment --}}
@include('admin.invoice_payments.partials.modal-edit-payment')

{{-- Add item --}}
@include('admin.orders.partials.modal-add-item', ['environment_' => 'invoices', 'modality' => $order->modality])

{{-- Edit item --}}
@include('admin.orders.partials.modal-edit-item', ['environment_' => 'invoices'])

{{-- See catalogue --}}
@include('admin.orders.partials.modal-see-catalogue')

{{-- Generate refund --}}
@include('admin.invoices.partials.modal-generate-refund')

@push('scripts')
<script>
$(function(){
	var payments = {!! json_encode($payments) !!}

	//Activar - desactivar IVA en factura.
    $('.btnWithIva').on('click', function(){
        var status = '';
        var elem = $(this);
        var invoice = elem.data('invoice');
        //Atención!!: detecta el estado previo a la acción, de modo que :checked es si queda deseleccionado:
        if(elem.siblings('input').is(':checked')){
            status = 0;
        }else{
            status = 1;
        }  
        $.get("/admin/invoices/"+invoice+"/with_iva/"+status, function(response, state){
             window.location.href = '/admin/invoices/'+invoice+'/edit';
        });
    });

    //Tipo de factura de abono.
    $('.rdbRefund').on('change', function(){
        var value = $(this).val();
        if(value == 'p'){
            $('.refund-fields').slideDown();

        }else{
            $('#refund_amount, #refund_concept').val('');
            $('.refund-fields').slideUp();
        }
    });

    //Modal edición cobro:
    $(document).on('click', '.btnEditPayment', function(){
    	var id = $(this).data('id');
    	for(let i in payments){
    		if(payments[i].id == id){
    			$('#paymentId').val(payments[i].id);
    			$('#paymentAmount').val(payments[i].amount);
    			$('#paymentObservations').val(payments[i].observations);

    			var arrMode = [payments[i].mode];
                $('#paymentMode').val(arrMode);
                $('#paymentMode').trigger('change');

                var arrBankAccount = [payments[i].bank_account_id];
                $('#paymentBankAccount').val(arrBankAccount);
                $('#paymentBankAccount').trigger('change');
    		}
    	}
    });

    //Selector conceptos contables:
});
</script>
@endpush

@endsection