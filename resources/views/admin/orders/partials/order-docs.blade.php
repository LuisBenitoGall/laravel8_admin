<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documentos')) !!}</label>
	<div class="col-lg-10">
		{{-- Dropzone --}}
		<x-dropzone url="/admin/order-docs/{{ $order->id }}/store"/>

		{{-- Galería --}}
		@if(count($order->docs))
			<x-lightbox-gallery-docs :docs="$order->docs" schema="orders"/>	
		@endif
	</div>
</div>