<form method="post" autocomplete="off" action="{{ route('protocols.update', $protocol) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nombre --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}</label>
		<div class="col-lg-6">
			<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.nombre')) !!}" maxlength="150" value="{{ $protocol->name }}"/>
		</div>

		{{-- Estado --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-lg-2">
			<x-select name="status" class="select2" label="estado_selec" :options="$estados" value="{{ $protocol->status }}" info=""/>
		</div>
	</div>

	<div class="form-group row">		
		{{-- Descripción --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="description" class="editor" value="{!! $protocol->description !!}"/>
		</div>	
	</div>

	<x-btn-submit name="submit_update" classdiv="col-12 text-right" label="{{ trans('textos.guardar') }}"/> 
</form>