@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones --}}
	<div class="col-12">
		{{-- Volver a facturas --}}
		<a class="btn btn-info" href="{{ route('invoices.index') }}"><i class="fa fa-angle-left"></i> {!! ucfirst(trans('textos.volver_facturas')) !!}</a>
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				<form method="post" autocomplete="off" action="{{ route('invoices.store-from-provider') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}

					<div class="form-group row">
						{{-- Proveedor --}}
						<label for="provider_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!}*</label>
						<div class="col-lg-3">
							<x-select name="provider_id" id="expenseProvider" class="select2" label="proveedor_selec" :options="$providers" required info="invoice-provider"/>
						</div>

						{{-- Añadir nuevo proveedor --}}
						<div class="col-md-1 text-center pt-1">
							<a href="#" class="btn btn-warning btn-circle" data-toggle="modal" data-target="#modal-add-provider" rel="tooltip" title="{!! trans('textos.proveedor_nuevo_add') !!}">
								<i class="fa fa-plus"></i>
							</a>
						</div>

						{{-- Pedido --}}
						<label for="order_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.pedido_compra')) !!}</label>
						<div class="col-lg-4">
							<x-select name="order_id" id="orderId" class="select2" label="pedido_selec" :options="[]" disabled/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Nº factura --}}
						<label for="invoice_old_num" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.factura_num')) !!}*</label>
						<div class="col-lg-2">
							<x-input type="text" name="invoice_old_num" autocomplete="off" maxlength="150" required/>
						</div>

						{{-- Fecha fiscal --}}
						<label for="fiscal_date" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fecha_fiscal')) !!}</label>
						<div class="col-lg-2">
							<x-date-picker name="fiscal_date" :format="$languages[config('app.locale')][4]"/>
						</div>

						<label for="expiration_date" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.fecha_vencimiento')) !!}</label>
						<div class="col-md-4 col-lg-2">
							<x-date-picker name="expiration_date" :format="$languages[config('app.locale')][4]"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Importe neto --}}
						<label for="amount" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe_neto')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="amount" id="invoiceAmount" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}"/>
						</div>

						{{-- IVA --}}
						<label for="iva" class="col-lg-2 col-form-label">IVA</label>
						<div class="col-lg-2">
							<x-input type="text" name="iva" id="invoiceIva" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" groupvalue="%" value="{{ config('constants.IVA_DEFAULT_') }}"/>
						</div>

						{{-- Importe total --}}
						<label for="amount_with_iva" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.importe_total')) !!}</label>
						<div class="col-lg-2">
							<x-input type="text" name="amount_with_iva" id="invoiceTotalAmount" class="text-right set-decimals" autocomplete="off" maxlength="15" group="right" groupvalue="{{ $currency->symbol }}"/>
						</div>
					</div>

					<div class="form-group row">
						{{-- Estado --}}
						<label for="status" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
						<div class="col-md-4 col-lg-2">
							@php
							$estados = App\Helpers\helpers::invoiceStatus();
							@endphp

							<x-select name="status" class="select2" label="indica_estado" :options="$estados"/>	
						</div>

						{{-- Documento --}}
						<label for="doc" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.documento')) !!}</label>
						<div class="col-md-4">
							<x-input-file name="doc"/>

							@if($errors->has('doc'))
			                    <span class="invalid-feedback" role="alert">
			                      <strong>{{ $errors->first('doc') }}</strong>
			                    </span>
			                @endif
						</div>
					</div>

					<div class="form-group row">
						{{-- Observaciones --}}
						<label for="customer_observations" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
						<div class="col-md-10">
							<x-input-textarea name="customer_observations" class="editor"/>
						</div>
					</div>					

					<x-btn-submit name="submit_from_provider" label="{{ trans('textos.guardar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

{{-- Modals --}}
{{-- Add provider --}}
@include('admin.expenses.partials.modal-add-provider')

@push('scripts')
<script>
$(function(){
	//Selección de pedidos en función del proveedor:
	$('#expenseProvider').on('change', function (event){
        $('#town_id').html('');
        $('#expenseProvider option:selected').each(function (){
            var id = event.target.value;	
            $("#orderId").empty();

            if (id == null || id == 0) {
                $('#orderId').attr('disabled', 'disabled');
            }else{
                $('#orderId').removeAttr('disabled');
	            $.get("/admin/orders/" + id + "/orders-about-providers", function (response, state){
	            	if(response.length > 0){
		                $("#orderId").append('<option value="">' + txtPedidoSelec + '</option>');
		                for (i = 0; i < response.length; i++){
		                	var option = response[i].purchase_num? response[i].purchase_num:(response[i].purchase_old_num? response[i].purchase_old_num:'');
		                	option = option+' - '+response[i].name;
	                    	$("#orderId").append('<option value="' + response[i].id + '">' + option + '</option>');
		                }
		            }else{
		            	$('#orderId').attr('disabled', 'disabled');		
		            }
	            });
	        }
        });
    });

    //Cálculo total factura:
    $('#invoiceAmount, #invoiceIva').on('change', function(){
    	var amount = $('#invoiceAmount').val();
    	var iva = $('#invoiceIva').val();

    	percent = (amount * iva) / 100;
    	var total = parseFloat(amount) + parseFloat(percent);
    	$('#invoiceTotalAmount').val(total.toFixed(2));
    });
});
</script>
@endpush
	

@endsection