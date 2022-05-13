<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documentos')) !!}</label>
	<div class="col-lg-10">
		{{-- Dropzone --}}
		<x-dropzone url="/admin/product-docs/{{ $product->id }}/store"/>

		{{-- Galería --}}
		@if(count($product->docs))
			<x-lightbox-gallery-products :docs="$product->docs"/>	
		@endif
	</div>
</div>