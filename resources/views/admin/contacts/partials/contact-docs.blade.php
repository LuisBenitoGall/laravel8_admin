<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documentos')) !!}</label>
	<div class="col-lg-10">
		{{-- Dropzone --}}
		{{-- 27/09/22: se omite el componente pues da error en producción -no en local- y se sustituye de momento por un archivo include --}}
		{{-- <x-dropzone url="/admin/product-docs/{{ $product->id }}/store"/> --}}

		@include('admin.partials.dropzone', ['id' => false, 'icon' => 'file', 'msg' => 'arrastra_docs', 'url' => '/admin/contact-docs/'.$contact->id.'/store'])

		{{-- Galería --}}
		@if(count($contact->docs))
			<x-lightbox-gallery-contacts :docs="$contact->docs"/>	
		@endif
	</div>
</div>