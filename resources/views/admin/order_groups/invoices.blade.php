@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		@can('orders.edit')
			<a class="btn btn-info" href="{{ route('orders.edit', $order) }}"><i class="fa fa-angle-left"></i> {!! ucfirst(trans('textos.agrupacion_volver')) !!}</a>
		@endcan
	</div>
</div>

<div class="row">
	<div class="col-12">						
		<div class="card mb-3">
			<div class="card-body">
				{{-- Acumulado factura flotante: --}}
				<div class="float-fixed-div" id="accumulated">
				    <div class="row">
				        <div class="col-12">
				        	{!! ucfirst(trans('textos.factura_acumulado')) !!}
				        	<span class="totalAmount">
				        		
                			</span>
				        </div>
				    </div>
				</div>

				<h3 class="mb-3">
					{!! ucfirst(trans('textos.agrupacion_facturas')) !!}
					<u>
						@if($order->order_num)
							{{ $order->order_num }}
						@else
							{{ $order->id }}
						@endif
					</u>
				</h3>
				<p class="text-warning">{{ trans('textos.pedidos_facturacion_texto') }}</p>

				<form method="post" autocomplete="off" action="{{ route('invoices.create-from-group') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
					{{ csrf_field() }}
					<input type="hidden" name="order_id" value="{{ $order->id }}">

					@php
					$no_invoices = true;
					@endphp

					<div class="form-group row">
						@foreach($ordersInGroup as $oig)
							<div class="col-md-4">
								<div class="widget gray-bg">
									<div class="row vertical-align">
										<div class="col-md-4">
											{{ trans('textos.pedido') }}
											<b>
												<a target="_blank" href="{{ route('orders.edit', $oig) }}"> 
													@if($oig->order_num)
														{{ $oig->order_num }}
													@else
														{{ $oig->id }}
													@endif
												</a>
											</b>
										</div>

										<div class="col-md-3 sinpadding">
											{{ trans('textos.importe') }}
											@php
											$amount = $oig->getAmount();
											@endphp
											<b>
												{{ $amount['total_amount'] }}{{ $currency->symbol }}
											</b>
										</div>

										<div class="col-md-5 text-right">
											{{-- Pedido facturado --}}
											@if($oig->invoiced && $oig->invoiceId)
												{{ trans('textos.factura') }}
												<b>
													<a target="_blank" href="{{ route('invoices.edit', $oig->invoiceId) }}">
														@if($oig->invoice_num)
															{{ $oig->invoice_num }}
														@else
															{{ $oig->invoiceId}}
														@endif
													</a>
												</b>

											{{-- Pedido NO facturado --}}
											@else
												@php
												$no_invoices = false;
												@endphp

												<label class="custom-chk-rdb mr-2" rel="tooltip" >
                            						<input type="checkbox" class="chk-order" name="orders[]" value="{{ $oig->id }}" data-amount="{{ $amount['total_amount'] }}"><span class="chk" ></span>
                        						</label>
											@endif
										</div>
									</div>
								</div>
							</div>
						@endforeach

						{{-- Sin pedidos para facturar --}}
						@if($no_invoices)
							<div class="col-12 mt-3">
								<p class="text-warning">{{ trans('textos.no_pedidos_facturables') }}</p>
							</div>
						@endif
					</div>

					@if($errors->has('orders'))
                        <span class="invalid-feedback" role="alert">
                          	<strong>{{ $errors->first('orders') }}</strong>
                        </span>
                    @endif

					<x-btn-submit name="submit_generate_by_group" label="{{ trans('textos.facturar') }}"/>
				</form>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script>
$(function(){
	//Cálculo acumulado factura:
	var total_amount = 0;
	var currency = '{{ $currency->symbol }}';
	$('.chk-order').on('change', function(){
		var amount = $(this).data('amount');
		if($(this).is(':checked')){
			total_amount = parseFloat(total_amount + amount);
		}else{
			total_amount = parseFloat(total_amount - amount);
		}

		if(total_amount > 0){
			$('.totalAmount').text(total_amount.toFixed(2)+currency);
			$('#accumulated').fadeIn();
		}else{
			$('.totalAmount').text('');
			$('#accumulated').fadeOut();
		}
	});
	
});
</script>
@endpush

@endsection