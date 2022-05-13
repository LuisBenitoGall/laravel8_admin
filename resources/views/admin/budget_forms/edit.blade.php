@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="formId" value="{{ $form->id }}">

<div class="row mb-3">
	<div class="col-12">
		{{-- Acciones: --}}
		@can('budget-forms.index')
			<a href="{{ route('budget-forms.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_formularios') !!}
			</a>
		@endcan

		@can('budget-forms.create')
    		<a href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-new-form">
    			<i class="fa fa-plus"></i>
    			{!! ucfirst(trans('textos.formulario_nuevo')) !!}
    		</a>
        @endcan

        @can('budget-forms.edit')
        	{{-- Nuevo bloque: --}}
			<button href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-block">
				<i class="fa fa-plus"></i>
				{!! ucfirst(trans('textos.bloque_nuevo')) !!}
			</button>

			{{-- Nuevo item: --}}
			@if($blocks->count() > 0)
				<button href="#" class="btn btn-info" data-toggle="modal" data-target="#modal-add-item">
					<i class="fa fa-plus"></i>
					{!! ucfirst(trans('textos.item_nuevo')) !!}
				</button>
			@else
				<label class="text-warning ml-3">{!! trans('textos.crear_bloques_texto') !!}</label>
			@endif
		@endcan
    </div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card mb-3">
			<div class="card-body">	
				<h3 class="mb-3">{{ ucfirst(trans('textos.formulario_editar')) }} <u>{{ ucfirst($form->name) }}</u>
				</h3>

				<div class="mb-3">
					{{-- Fecha alta --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.fecha_alta')) !!}: 
						<b>
							{{ $form->created_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>

					{{-- Fecha última actualización --}}
					<label class="mr-5">
						{!! ucfirst(trans('textos.ultima_edicion')) !!}: 
						<b>
							{{ $form->updated_at->format($languages[session()->get('locale')][4].' H:i:s') }}
						</b>
					</label>
				</div>

				{{-- Tabs: --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item @if(!$tab) active @endif">
			    		<a class="nav-link @if(!$tab) active @endif" href="#tab-form-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.formulario')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'blocks') active @endif">
			    		<a class="nav-link @if($tab == 'blocks') active @endif" href="#tab-blocks" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.bloques')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'items') active @endif">
			    		<a class="nav-link @if($tab == 'items') active @endif" href="#tab-items" role="tab" data-toggle="tab">Items</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'journeys') active @endif">
			    		<a class="nav-link @if($tab == 'journeys') active @endif" href="#tab-journeys" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.jornadas')) !!}</a>
			  		</li>

			  		<li class="nav-item @if($tab == 'customers') active @endif">
			    		<a class="nav-link @if($tab == 'customers') active @endif" href="#tab-customers" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.clientes')) !!}</a>
			  		</li>
			  	</ul>

			  	<div class="tab-content pt-5">
					{{-- Info: --}}
					<div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-form-info">
						@include('admin.budget_forms.partials.form-info')	
					</div>

					{{-- Bloques: --}}
					<div class="tab-pane fade @if($tab == 'blocks') show active @endif" role="tabpanel" id="tab-blocks">
						@include('admin.budget_forms.partials.form-blocks')	
					</div>

					{{-- Items: --}}
					<div class="tab-pane fade @if($tab == 'items') show active @endif" role="tabpanel" id="tab-items">
						@include('admin.budget_forms.partials.form-items')	
					</div>

					{{-- Clientes --}}
					<div class="tab-pane fade @if($tab == 'customers') show active @endif" role="tabpanel" id="tab-customers">
						@include('admin.budget_forms.partials.form-customers')	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- Modals: --}}
{{-- New form: --}}
@include('admin.budget_forms.partials.modal-new-form')

{{-- New block: --}}
<div class="modal fade modal-add-block" id="modal-add-block" tabindex="-1" role="dialog" aria-labelledby="newBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newBlock">{!! ucfirst(trans('textos.bloque_nuevo')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budget-form-items.store') }}" accept-charset="utf-8" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                    <input type="hidden" name="recid" value="0">

					<div class="form-group row">
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" autocomplete="off" maxlength="255" required/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

{{-- Edit block: --}}
<div class="modal fade modal-edit-block" id="modal-edit-block" tabindex="-1" role="dialog" aria-labelledby="editBlock" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editBlock">{!! ucfirst(trans('textos.bloque_editar')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budget-form-items.update') }}" accept-charset="utf-8" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="form_id" value="{{ $form->id }}">
                    <input type="hidden" name="id" id="blockItemsId">

					<div class="form-group row">
                        <label for="name" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!}*</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" id="blockItemsName" autocomplete="off" maxlength="255" required/>
                        </div>
                    </div>
                    
                    <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

{{-- New item: --}}
<div class="modal fade modal-add-item" id="modal-add-item" tabindex="-1" role="dialog" aria-labelledby="newItem" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="newItem">{!! ucfirst(trans('textos.item_nuevo')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
            	@if($products->count())
	                <form method="post" autocomplete="off" action="{{ route('budget-form-items.store') }}" accept-charset="utf-8" role="form">
	                    {{ csrf_field() }}
	                    <input type="hidden" name="form_id" value="{{ $form->id }}">
	                    
	                    <div class="form-group row">
	                    	{{-- Item --}}
	                        <label for="product_id" class="col-lg-12">Item *</label>
	                        <div class="col-lg-12">
	                        	@php
	                        	$products_ = [];
	                        	@endphp

	                        	@foreach($products as $product)
	                        		@php
	                        		$products_[$product->id] = $product->name;
	                        		@endphp
	                        	@endforeach

	                        	<x-select name="product_id" id="product_id" class="select2" label="producto_selec" required :options="$products_" />
	                        </div>
	                    </div>

		                <div class="form-group row">
	                    	{{-- Bloque --}}
	                        <label for="recid" class="col-lg-12">{!! ucfirst(trans('textos.bloque')) !!} *</label>
	                        <div class="col-lg-12">
	                        	@php
	                        	$blocks_ = [];
	                        	@endphp

	                        	@foreach($blocks as $block)
	                        		@php
	                        		$blocks_[$block->id] = $block->name;
	                        		@endphp
	                        	@endforeach

	                        	<x-select name="recid" class="select2" label="bloque_selec" required :options="$blocks_" />
	                        </div>
	                    </div>

	                    <div class="form-group row">
	                    	{{-- Observaciones --}}
	                        <label for="observations" class="col-lg-12">{{ ucfirst(trans('textos.observaciones')) }}</label>
	                        <div class="col-lg-12">
	                        	<x-input-textarea name="observations"/>
	                        </div>
	                    </div>

						<div class="form-group row">
							{{-- Precio. Viene dado de la tabla products --}}
							<div class="col-lg-6">
								<div class="row">
									<label for="price" class="col-lg-12">{!! ucfirst(trans('textos.precio')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="price_" id="itemPrice" class="text-right set-decimals" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}" info="budget-form-item-price"/>
									</div>	
								</div>
							</div>

							{{-- Cálculo por porcentaje --}}
							<div class="col-lg-6">
								<div class="row">
			                        <label for="calculated_by_percent" class="col-lg-12">{!! ucfirst(trans('textos.calculo_por_porcentaje')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="calculated_by_percent" id="calculated_by_percent" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-calculate-by-percent"/>
									</div>
								</div>
							</div>
						</div> 

						<div class="form-group row">
							{{-- Descuento --}}
							<div class="col-lg-6">
								<div class="row">
			                        <label for="discount" class="col-lg-12">{!! ucfirst(trans('textos.descuento')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="discount" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-discount"/>
									</div>
								</div>
							</div>

							{{-- Cantidad mínima --}}
							<div class="col-lg-6">
								<div class="row">
									<label for="qty_min" class="col-lg-12">{!! ucfirst(trans('textos.cantidad_min')) !!}</label>
			                        <div class="col-lg-12">
			                        	<x-input type="text" name="qty_min" class="text-right set-decimals" autocomplete="off" maxlength="11" value="0" info="budget-form-item-qty-min"/>
			                        </div>
			                    </div>
			                </div>
			            </div>
						
						<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
	                </form>    

                @else
                	<p class="text-center text-warning mt-4">
						<b>{!! ucfirst(trans('textos.catalogo_vacio')) !!}</b> 
						<a href="{{ route('products.index') }}" class="btn btn-info ml-4">{!! ucfirst(trans('textos.catalogo')) !!}</a>
					</p>
	            @endif
            </div>
        </div>
    </div>
</div>

{{-- Edit item: --}}
<div class="modal fade modal-edit-item" id="modal-edit-item" tabindex="-1" role="dialog" aria-labelledby="editItem" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editItem">{!! ucfirst(trans('textos.item_editar')) !!} <span class="userTo"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="post" autocomplete="off" action="{{ route('budget-form-items.update') }}" accept-charset="utf-8" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="ItemsId">
                    <input type="hidden" name="product_id" id="ItemsProductId">
                    <input type="hidden" name="form_id" value="{{ $form->id }}">

                    <div class="form-group row">
                    	{{-- Item --}}
                        <label for="name_" class="col-lg-12">Item *</label>
                        <div class="col-lg-12">
                        	<x-input type="text" name="name" id="ItemsName" maxlength="255" readonly="true"/>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Observaciones --}}
                        <label for="observations" class="col-lg-12">{{ ucfirst(trans('textos.observaciones')) }}</label>
                        <div class="col-lg-12">
                        	<x-input-textarea name="observations" id="ItemsObservations"/>
                        </div>
                    </div>

                    <div class="form-group row">
                    	{{-- Precio. Viene dado de la tabla products --}}
                    	<div class="col-lg-6">
							<div class="row">
		                        <label for="price" class="col-lg-12">{!! ucfirst(trans('textos.precio')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="price_" id="ItemsPrice" class="text-right" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}" readonly="true" info="budget-form-item-price"/>

		                        	{{-- Guardamos el precio original para posible cambios --}}
		                        	<input type="hidden" id="ItemsOrigPrice">
		                        </div>
		                    </div>
		                </div>

		                {{-- Cálculo por porcentaje --}}
		                <div class="col-lg-6">
							<div class="row">
		                        <label for="calculated_by_percent" class="col-lg-12">{!! ucfirst(trans('textos.calculo_por_porcentaje')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="calculated_by_percent" id="ItemsCalculateByPercent" class="text-right set-decimals" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-calculate-by-percent"/>
								</div>
							</div>
						</div>
					</div> 

					<div class="form-group row">
						{{-- Descuento --}}
						<div class="col-lg-6">
							<div class="row">
		                        <label for="discount" class="col-lg-12">{!! ucfirst(trans('textos.descuento')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="discount" class="text-right set-decimals" id="ItemsDiscount" autocomplete="off" maxlength="5" group="right" groupvalue="%" info="budget-form-item-discount"/>
								</div>
							</div>
						</div>

						{{-- Cantidad mínima --}}
						<div class="col-lg-6">
							<div class="row">
								<label for="qty_min" class="col-lg-12">{!! ucfirst(trans('textos.cantidad_min')) !!}</label>
		                        <div class="col-lg-12">
		                        	<x-input type="text" name="qty_min" class="text-right set-decimals" id="ItemsQtyMin" autocomplete="off" maxlength="11" value="0" info="budget-form-item-qty-min"/>
		                        </div>
		                    </div>
		                </div>
		            </div>

		            <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
                </form>    
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function(){
	//Modal editar bloque:
	$(document).on('click', '.btnEditFormBlock', function(){
		var id = $(this).data('id');
		var name = $(this).data('name');

		$('#blockItemsId').val(id);
		$('#blockItemsName').val(name);
	});

	//Modal editar item de formulario de presupuestos:
    $(document).on('click', '.btnEditFormItem', function(){
    	$('#ItemsPrice, #ItemsOrigPrice').val('');

        var id = $(this).data('id');
        var product_id = $(this).data('productid');
        var name = $(this).data('name');
        var observations = $(this).data('observations');
        var price = $(this).data('price');
        var discount = $(this).data('discount');
        var qty_min = $(this).data('qtymin');
        var calculated_by_percent = $(this).data('calculatebypercent');

        $('#ItemsId').val(id);
        $('#ItemsName').val(name);
        $('#ItemsObservations').val(observations);
        $('#ItemsPrice, #ItemsOrigPrice').val(price);
        $('#ItemsDiscount').val(discount);
        $('#ItemsQtyMin').val(qty_min);
        $('#ItemsCalculateByPercent').val(calculated_by_percent);
    });

	//Mostrar precio de producto:
	var products = {!! json_encode($products) !!};
	$('#product_id').on('change', function(){
		var option = $(this).val();

		for(var i=0; i<products.length; i++){
			if(products[i].id == option && $('#calculated_by_percent').val() == 0){
				$('#itemPrice').val(products[i].price);
			}
		}
	});

	//Precio fijo y precio calculado por porcentaje se excluyen entre si:
	$('#itemPrice').on('change', function(){
		if($(this).val() > 0){
			$('#calculated_by_percent').val('');
		}
	});

	//Precio calculado por porcentaje. Precio fijo y precio calculado por porcentaje son excluyentes entre sí:
	$('#calculated_by_percent, #ItemsCalculateByPercent').on('change', function(){
		var by_percent = $(this).val();

		//Precio por porcentaje:
		if(by_percent > 0){
			$('#itemPrice, #ItemsPrice').val(0);	

		//Devolvemos el precio fijo:
		}else if(by_percent == 0 || by_percent == ''){
			var orig_price = $('#ItemsOrigPrice').val();
			$('#ItemsPrice').val(orig_price);
		}
	});

	//Asignar formulario de prespuesto por cliente.
    $(document).on('change', '.btnSetCustomerForm', function(){
        var status = '';
        var elem = $(this);
        if(elem.find('input').is(':checked')){
            status = 1;
        }else{
            status = 0;
        }  
        var id = elem.find('.chk-link').data('id');
        var form = elem.find('.chk-link').data('form');
        $.get("/admin/budget-forms/set-customer-forms/"+id+"/"+form+"/"+status+"", function(response, state){
            if(response){
                $('.check-ko').remove();
                elem.before('<i class="fa fa-check check-ok"></i>');
                $('.check-ok').delay(1000).fadeOut();
            }else{
                elem.before('<i class="fa fa-exclamation-triangle check-ko"></i>');
            }
        });     
    });
});
</script>
@endpush

@endsection