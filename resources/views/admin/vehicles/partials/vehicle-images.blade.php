<div class="form-group row">
	<label for="title" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.imagenes')) !!}</label>
	<div class="col-lg-10">
		{{-- Dropzone --}}
		<x-dropzone url="/admin/vehicle-images/{{ $vehicle->id }}/store"/>

		{{-- Galería --}}
		@if(count($vehicle->images))
			<x-lightbox-gallery-vehicles :images="$vehicle->images"/>	
		@endif
	</div>
</div>