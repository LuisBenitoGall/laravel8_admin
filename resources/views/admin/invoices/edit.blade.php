@extends('admin.partials.layout')

@section('content')

@php
$disabled = $invoice->blocked? true:false;
$readonly = $invoice->blocked? true:false;
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

		{{-- Presupuesto --}}
		@can('budgets.edit')
			@if($shared->budget_id)
				<a target="_blank" href="{{ route('budgets.edit', $shared->budget_id) }}" class="btn btn-info">
					<i class="fa fa-file-alt"></i>
					{!! ucfirst(trans('textos.presupuesto')) !!}
				</a>	
			@endif
		@endcan

		{{-- Pedido --}}
		@can('orders.edit')
			@if($shared->order_id)
				<a target="_blank" href="{{ route('orders.edit', $shared->order_id) }}" class="btn btn-info">
					<i class="fa fa-shopping-cart"></i>
					{!! ucfirst(trans('textos.pedido')) !!}
				</a>	
			@endif
		@endcan

		{{-- Facturas del pedido. Pasamos el shared_id --}}
		@if($invoices->count() > 1)
			<a href="{{ route('invoices.by-order', $shared->id) }}" class="btn btn-info" rel="tooltip">
				<i class="fa fa-file-invoice-dollar"></i>
				{{ ucfirst(trans('textos.facturas_pedido')) }}
			</a>	
		@endif

		{{-- Imprimir --}}
		<a href="{{ route('invoices.print', $invoice) }}" class="btn btn-info close-animation" rel="tooltip" title="{!! ucfirst(trans('textos.imprimir')) !!}">
			<i class="fa fa-print"></i>
		</a>

		{{-- Cancelar factura - condiciones:
			1. Permisos de cancelación de factura.
			2. Acción permitida.
			3. Factura no bloqueada.
		--}}
        @can('invoices.destroy')
        	@if($permitted && !$invoice->blocked)
	        	<form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="frm-inline" role="form" accept-charset="UTF-8">
	                <input name="_method" type="hidden" value="DELETE">
	                {{ csrf_field() }}
	                <button type="submit" class="btn btn-danger frm-confirm" rel="tooltip" title="{{ ucfirst(trans('textos.factura_cancelar')) }}" data-msg="{{ ucfirst(trans('textos.factura_cancelar_confirm')) }}"><i class="fa fa-trash"></i></button>
	            </form>
	        @endif
        @endcan

		{{-- Bloquear factura / factura bloqueada --}}
		@if(!$invoice->blocked)
			<a href="{{ route('invoices.block', $invoice) }}" class="btn btn-warning close-animation btn-confirm ml-3" data-msg="{{ trans('textos.factura_bloquear_confirm') }}" rel="tooltip" title="{!! ucfirst(trans('textos.factura_bloquear')) !!}">
				<i class="fa fa-unlock"></i>
			</a>
		@else
			<a href="#" class="btn btn-danger close-animation ml-3" rel="tooltip" title="{!! ucfirst(trans('textos.factura_bloqueada')) !!}">
				<i class="fa fa-lock"></i>
			</a>		
		@endif
	</div>
</div>

<input type="hidden" id="myShared" value="{{ $shared->id }}">

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Componente importe pedido flotante: --}}
				@livewire('budget-amount', ['budget_amount' => number_format($invoice->amount_with_iva, 2), 'currency' => $currency->symbol, 'budget_amount_without_discount' => false, 'environment' => 'invoices'])

				<h3 class="mb-3">
					{!! ucfirst(trans('textos.factura')) !!} 
					<u>
						@if($invoice->invoice_num)
							{{ $invoice->invoice_num }}
						@else
							{{ $invoice->id }}
						@endif
					</u>
				</h3>

				{{-- Info --}}
				<div class="mb-3">	
					{{-- Cliente --}}
					@include('admin.budget_order_shared_fields.partials.data-customer')

					{{-- Proveedor --}}
					@include('admin.budget_order_shared_fields.partials.data-provider')

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
    					<b>{!! $invoice->amount !!}{{ $currency->symbol }}</b>
					</label>

					{{-- Total factura --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.factura_total')) !!}: 
    					<b>{!! $invoice->amount_with_iva !!}{{ $currency->symbol }}</b>
					</label>

					{{-- Incluir IVA: --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.incluye_iva')) !!}:

						@if(!$invoice->blocked)
							<label class="custom-chk-rdb" rel="tooltip">
				                <input type="checkbox" name="with_iva" @if($invoice->with_iva) checked @endif>
				                <span class="chk chksmall btnWithIva mt-1 ml-2" data-invoice="{{ $invoice->id }}"></span>
				            </label>

						@else
				        	@if($invoice->with_iva)
				        		<i class="fa fa-check text-success ml-2"></i>
				        	@else
				        		<i class="fa fa-times text-danger ml-2"></i>
				        	@endif
				        @endif

				        @if(!$invoice->with_iva) <small class="text-warning">{!! ucfirst(trans('textos.facturado_sin_iva')) !!}</small> @endif
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.factura')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			  			<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.lineas_pedido')) !!}</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'template') active @endif">
			  			<a class="nav-link @if($tab == 'template') active @endif" href="#tab-template" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.plantilla')) !!}</a>	
			  		</li>

			  		<li class="nav-item @if($tab == 'payments') active @endif">
			  			<a class="nav-link @if($tab == 'payments') active @endif" href="#tab-payments" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.pagos')) !!}</a>	
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-4">
					{{-- Cabecera: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-info">
						@include('admin.invoices.partials.invoice-info')	
					</div>

					{{-- Items: --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
						{{-- @include('admin.invoices.partials.invoice-items')	 --}}
						@include('admin.orders.partials.order-items')
					</div>

					{{-- Plantilla --}}
					<div class="tab-pane fade @if($tab == 'template') show active @endif" role="tabpanel" id="tab-template">
						@include('admin.invoices.partials.invoice-template')	
					</div>

					{{-- Pagos --}}
					<div class="tab-pane fade @if($tab == 'payments') show active @endif" role="tabpanel" id="tab-payments">
						@include('admin.invoices.partials.invoice-payments')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- Edit item --}}
{{-- @include('admin.budget_order_shared_fields.partials.modal-edit-item', ['environment_' => 'invoices']) --}}

@push('scripts')
<script>
$(function(){
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
});
</script>
@endpush

@endsection