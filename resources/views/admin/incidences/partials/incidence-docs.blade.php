<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documentos')) !!}</label>
	<div class="col-lg-10">
		{{-- @include('admin.partials.dropzone', ['id' => 'incidenceDocs2', 'icon' => 'file', 'msg' => 'arrastra_docs', 'url' => '/admin/incidence-docs/'.$incidence->id.'/store', 'second_dropzone' => true]) --}}

		<div class="dropzone" id="incidenceDocs2"></div>

		{{-- Galería --}}
		@if(count($incidence->docs))
			<x-lightbox-gallery-incidences :docs="$incidence->docs" schema="incidences" />
		@endif
	</div>
</div>

