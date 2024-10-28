<div class="row">
	{{-- Para producto referencia --}}
	@if(!$product->variant_of)
		<div class="col-12 mb-4">
	    	<form method="post" autocomplete="off" action="{{ route('product-attributes.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
				{{ csrf_field() }}  
				<input type="hidden" name="product_id" value="{{ $product->id }}">

				<div class="form-group row">
					{{-- Atributos --}}
					<label for="attributes" class="col-lg-2 col-form-label">
	                	{!! ucfirst(trans('textos.atributos_agregar')) !!}*
	                </label>
	                <div class="col-lg-7">
	                	<x-select name="attributes[]" class="select2" :options="$attributes" multiple="true" required/>

	                	<small class="text-warning">{{ trans('textos.atributos_vincula_producto') }}</small>
	                </div>

					<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}" inline="true"/>
				</div>
			</form>  
	    </div>

	    <div class="col-12">
	    	<div class="row">
				<div class="table-responsive">
					<table class="table table-hover dtabla" id="tblProductAttributes">
						<thead>
							<tr>
								<th>{!! ucfirst(trans('textos.atributo')) !!}</th>
								<th>{!! ucfirst(trans('textos.tipo_dato')) !!}</th>
								<th>{!! ucfirst(trans('textos.valor')) !!}</th>
								<th class="text-center">{!! ucfirst(trans('textos.acciones')) !!}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>    
	    </div>

	{{-- Para variantes de producto --}}
	@else
		<p class="text-warning mb-3 col-12 mt-0">Define la variable de producto a través de los atributos del producto referencia. Los atributos seleccionados formarán parte de la descripción del producto.</p>

		<div class="col-11 mb-4">
			<form method="post" autocomplete="off" action="{{ route('product-attributes-variant.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
				{{ csrf_field() }}  
				<input type="hidden" name="product_id" value="{{ $product->id }}">

				<div class="form-group row">
					@foreach($ref_attributes as $ra)
						@php
						$val_ = '';
						$vid_ = '';
						foreach($variant_attributes as $va){
							if($va->attribute_id == $ra->id){
								$val_ = $va->value;
								$vid_ = $va->value_in_description;
								break;
							}
						}
						@endphp

						<label for="" class="col-lg-2 col-form-label mb-3">{{ $ra->name }}</label>	
						<div class="col-md-4 col-lg-2 mb-3 variant-attribute">
							{{-- Input según el tipo de dato --}}
							{{-- Checkbox --}}
							@if($ra->datatype == 10)
								<label class="custom-chk-rdb" rel="tooltip">
				                	<input type="checkbox" name="attribute-{{ $ra->id }}" @if($val_ == 1) checked @endif>
				                	<span class="chk" style="top: 5px;"></span>
				            	</label>

				            {{-- Texto --}}
							@elseif($ra->datatype == 20 || $ra->datatype == 30)
								<x-input type="text" name="attribute-{{ $ra->id }}" maxlength="150" autocomplete="off" value="{{ $val_ }}"/>

							{{-- Fecha --}}
							@elseif($ra->datatype == 40)
								<x-date-picker name="attribute-{{ $ra->id }}" value="{{ $val_ }}" :format="$languages[config('app.locale')][4]"/>

							{{-- Opciones --}}
							@elseif($ra->datatype == 50)
								@php
								$arr_options_ = $ra->values? explode(',', $ra->values):[];
								$options_ = [];
								foreach($arr_options_ as $ao){
									$options_[$ao] = $ao;	
								}
								@endphp

								<x-select name="attribute-{{ $ra->id }}" class="select2" :options="$options_" value="{{ $val_ }}" label="tipo_dato_selec"/>

							{{-- Color --}}
							@elseif($ra->datatype == 60)
								@php
								$colors_ = $ra->values? explode(' ', $ra->values):[];
								@endphp

								<x-color-select name="attribute-{{ $ra->id }}" :colors="$colors_" value="{{ $val_ }}" />
							@endif

							{{-- Ampliar descripción con el atributo --}}
							<span class="va-confirm">
								<label class="custom-chk-rdb" rel="tooltip" rel="tooltip" title="{{ trans('textos.descripcion_ampliar') }}">
				                	<input type="checkbox" name="vid-{{ $ra->id }}" @if($vid_ == 1) checked @endif>
				                	<span class="chk chksmall" style="top: 5px;"></span>
				            	</label>
							</span>
						</div>
					@endforeach
				</div>

				<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
			</form>
		</div>
	@endif
</div>

{{-- Modals: --}}
@include('admin.attributes.partials.modal-set-color')

@push('scripts')
<script>
$(function(){
	//Tabla Product Attributes:
	    if($('#tblProductAttributes').length > 0){
	        var tokenDT = $('meta[name="csrf-token"]').attr('content');
	        var product_id = $('#productId').val();

	        var tableProductAttributes = $('#tblProductAttributes').DataTable({
	            serverSide: true,
	            processing: true,
	            ajax: {
	                "type": "GET",
	                "url": "/api/admin/product-attributes/"+product_id,
	                "error": function(reason){
	                    console.log(reason);
	                }
	            },
	            columns: [
	                {data: 'name'},
	                {data: 'datatype'},
	                {data: 'value'},
	                {data: 'acciones', className: 'text-right'}
	            ]
	        });
	    }
});
</script>
@endpush