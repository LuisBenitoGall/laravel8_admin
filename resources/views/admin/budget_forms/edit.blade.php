@extends('admin.partials.layout')

@section('content')

<input type="hidden" id="formId" value="{{ $form->id }}">

<div class="row mb-3">
	{{-- Acciones: --}}
	<div class="col-12">
		{{-- Formularios --}}
		@can('budget-forms.index')
			<a href="{{ route('budget-forms.index') }}" class="btn btn-info">
				<i class="fa fa-angle-left"></i>
				{!! trans('textos.volver_formularios') !!}
			</a>
		@endcan

		{{-- Nuevo formulario --}}
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
@include('admin.budget_forms.partials.modal-new-block')

{{-- Edit block: --}}
@include('admin.budget_forms.partials.modal-edit-block')

{{-- New item: --}}
@include('admin.budget_forms.partials.modal-new-item')

{{-- Edit item: --}}
@include('admin.budget_forms.partials.modal-edit-item')

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
				$('#calculated_by_percent').val(products[i].calculated_by_percent);
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