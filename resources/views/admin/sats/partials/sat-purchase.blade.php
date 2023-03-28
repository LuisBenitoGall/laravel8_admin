@if($order_purchase)
	<div class="row">
		<div class="col-md-2">
	        <div class="widget bg-warning no-padding">
	            <div class="p-m">
	            	<h3 class="font-bold mb-3 text-center">
	                    {{ trans('textos.importe_compra') }}
	                </h3>
	                <h1 class="m-xs text-center">{{ $purchase_amount['total_amount'] }}{{ $currency->symbol }}</h1>
	            </div>
	        </div>
	    </div>

	    <div class="col-md-8 pt-2">
	    	{{-- Editar pedido --}}
	    	<a href="{{ route('orders.edit', $sat->purchase_id) }}" class="btn btn-info">
				<i class="fa fa-edit"></i>
				{!! trans('textos.compra_editar') !!}
			</a>
	    </div>
	</div>

@else
	<form method="post" autocomplete="off" action="{{ route('orders.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
        {{ csrf_field() }}
        <input type="hidden" name="sat" value="{{ $sat->id }}">
        <input type="hidden" name="type" value="purchase">

        <div class="row">
        	<div class="col-12 mb-3">
				<h5 class="text-warning">{{ trans('textos.pedido_compra_generar') }}</h5>	
			</div>	
        </div>

        <div class="form-group row">
            {{-- Proveedor --}}
            <label for="provider" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.proveedor')) !!}</label>
            <div class="col-md-6 position-relative">
                <x-input type="text" name="provider" id="search_provider" placeholder="{!! ucfirst(trans('textos.proveedor')) !!}" maxlength="150"/>
            </div>

            <input type="hidden" name="company_id" id="providerId">
        </div>

        <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
    </form>	
@endif
