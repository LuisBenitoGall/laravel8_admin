<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.documentos')) !!}</label>
	<div class="col-lg-10">
		{{-- Dropzone --}}
		<x-dropzone url="/admin/project-docs/{{ $project->id }}/store"/>

		{{-- Galería --}}
		@if(count($project->docs))
			<x-lightbox-gallery-projects :docs="$project->docs"/>
		@endif
	</div>
</div>