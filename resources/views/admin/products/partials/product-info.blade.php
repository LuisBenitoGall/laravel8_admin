<form method="post" autocomplete="off" action="{{ route('products.update', $product->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Producto --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!}*</label>
		<div class="col-lg-6">
			<x-input type="text" name="name" id="name" value="{{ $product->name }}" autocomplete="off" maxlength="255" required/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Estado --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto_activo')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.producto_on_off') !!}">
                <input type="checkbox" name="status" @if($product->status == 1) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
        </div>

        @if($product->type == 'p')
	        {{-- Gestión de stock --}}
	        <label for="stock_management" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.stock_gestion')) !!}</label>
			<div class="col-lg-1">
				<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.stock_gestion_on_off') !!}">
	                <input type="checkbox" name="stock_management" @if($product->stock_management == 1) checked @endif>
	                <span class="chk" style="top: 5px;"></span>
	            </label>
	        </div>

	        {{-- Gestión de lotes --}}
	        <label for="batch" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.lotes_gestion')) !!}</label>
			<div class="col-lg-1">
				<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.lote_on_off') !!}">
	                <input type="checkbox" name="batch" @if($product->batch == 1) checked @endif>
	                <span class="chk" style="top: 5px;"></span>
	            </label>
	        </div>
	    @endif

        {{-- Producto para venta --}}
        <label for="on_sale" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.en_venta')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip" title="{!! trans('textos.producto_venta') !!}">
                <input type="checkbox" name="on_sale" @if($product->on_sale == 1) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
        </div>
	</div>

	<div class="form-group row">
		{{-- Referencia de producto --}}
		<label for="product_pattern_id" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.ref_automatica')) !!}</label>
		<div class="col-lg-2">
			@if(!$product->ref)
				@if($patterns)
					@if($patterns->count())
						@php
						$patterns_ = [];
						foreach($patterns as $ptn){
							$patterns_[$ptn->id] = $ptn->name;
						}
						@endphp

						<x-select name="product_pattern_id" class="select2" label="patron_selecciona" :options="$patterns_" info="product-referencia-automatica"/>
					@else
						<label class="text-warning mt-2">{{ trans('textos.patrones_no_hay') }}</label>
					@endif
				@endif
			@else
				<label class="text-warning mt-1" style="font-size: 16px;"><b><u>{{ $product->ref }}</u></b></label>
			@endif
		</div>

		{{-- Referencia manual --}}
		<label for="manual_ref" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.ref_manual')) !!}</label>
		<div class="col-lg-2">
			<x-input type="text" name="manual_ref" value="{{ $product->manual_ref }}" autocomplete="off" maxlength="255" info="product-referencia-manual"/>
		</div>

		{{-- Precio --}}
		<label for="price" class="col-lg-1 col-form-label">{!! ucfirst(trans('textos.precio')) !!}</label>
		<div class="col-lg-1 sinpadding-right">
			<x-input type="text" name="price" class="text-right set-decimals" value="{{ $product->price }}" autocomplete="off" maxlength="8" group="right" groupvalue="{{ $currency->symbol }}"/>
		</div>

		{{-- IVA --}}
		<label for="iva" class="col-lg-1 col-form-label">IVA</label>
		<div class="col-lg-1">
			@php
			$iva_ = $product->iva? $product->iva:($iva? $iva:config('constants.IVA_DEFAULT_'));
			@endphp
			<x-input type="text" name="iva" class="text-right set-decimals" value="{{ $iva_ }}" autocomplete="off" maxlength="5" group="right" groupvalue="%"/>
		</div>
    </div>
    <br>

    {{-- Tabs idiomas: --}}
    <x-tabs-languages :languages="$languages" />

    <div class="tab-content pt-5">
		@php
		$i = 0;

		$arrTranslations = $product->translations? unserialize($product->translations):'';
		$arrTags = $product->tags? unserialize($product->tags):'';
		$arrDescription = $product->description? unserialize($product->description):'';
		$arrLongDescription = $product->long_description? unserialize($product->long_description):'';
		@endphp

		@foreach($languages as $lang)	
			<div class="tab-pane fade show @if($i == 0) active @endif" role="tabpanel" id="tab-{{ $lang[0] }}">
				<div class="form-group row">
					{{-- Título producto --}}
					<label for="translations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.producto')) !!} {{ $lang[3] }}</label>
					<div class="col-lg-10">
						@php
						$productName = isset($arrTranslations[$lang[0]])? $arrTranslations[$lang[0]]:'';
						@endphp
						<x-input type="text" name="translations_{{ $lang[0] }}" value="{{ $productName }}" autocomplete="off" maxlength="150"/>
					</div>
				</div>

				@php
				//dd($product->tags);
				$tags = ($product->tags)? implode(';',$arrTags[$lang[0]]):'';
				@endphp

				<div class="form-group row">
					{{-- Tags --}}
					<label for="tags" class="col-lg-2 col-form-label">Tags {{ $lang[3] }}</label>
					<x-selectize name="tags_{{ $lang[0] }}" class="col-lg-10" maxlength="500" value="{{ $tags }}" autocomplete="off" style="position: relative; z-index: 0;"/>
				</div>

				<div class="form-group row">
					{{-- Descripción --}}
					<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!} {{ $lang[3] }}</label>
					<div class="col-lg-10">
						@php
						$description = $arrDescription? $arrDescription[$lang[0]]:'';
						@endphp
						<x-input-textarea name="description_{{ $lang[0] }}" value="{!! $description !!}"/>
					</div>
				</div>

				<div class="form-group row">
					{{-- Descripción larga --}}
					<label for="long_description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion_larga')) !!} {{ $lang[3] }}</label>
					<div class="col-lg-10">
						@php
						$long_description = $arrLongDescription? $arrLongDescription[$lang[0]]:'';
						@endphp
						<x-input-textarea name="long_description_{{ $lang[0] }}" class="editor" value="{!! $long_description !!}"/>
					</div>
				</div>
			</div>

			@php
			$i++;
			@endphp
		@endforeach	
	</div> 
			
	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>