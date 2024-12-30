<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documentos')) !!}</label>
	<div class="col-lg-10">
		{{-- @include('admin.partials.dropzone', ['id' => 'incidenceDocs2', 'icon' => 'file', 'msg' => 'arrastra_docs', 'url' => '/admin/incidence-docs/'.$incidence->id.'/store', 'second_dropzone' => true]) --}}

		{{-- Galería --}}
		@if(count($incidence->docs))
			<x-lightbox-gallery-incidences :docs="$message->docs" schema="incidences" />
		@endif
	</div>
</div>

