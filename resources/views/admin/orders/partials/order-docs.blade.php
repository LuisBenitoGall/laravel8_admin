<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documentos')) !!}</label>
	<div class="col-lg-10">
		{{-- Dropzone --}}
		{{-- 27/09/22: se omite el componente pues da error en producción -no en local- y se sustituye de momento por un archivo include --}}
		{{-- <x-dropzone url="/admin/order-docs/{{ $order->id }}/store"/> --}}

		@include('admin.partials.dropzone', ['id' => false, 'icon' => 'camera-retro', 'msg' => 'arrastra_fotos', 'url' => '/admin/order-docs/'.$order->id.'/store'])

		{{-- Galería --}}
		@if(count($order->docs))
			<x-lightbox-gallery-docs :docs="$order->docs" schema="orders"/>	
		@endif
	</div>
</div>