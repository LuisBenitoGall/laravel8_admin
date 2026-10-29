<form method="post" autocomplete="off" action="{{ route('briefings.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nombre --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}*</label>
		<div class="col-lg-10">
			<x-input type="text" name="name" placeholder="{!! ucfirst(trans('textos.titulo')) !!}" autocomplete="off" maxlength="150" required value="{{ $briefing->name }}"/>
		</div>	
	</div>

	<div class="form-group row">
		{{-- Observaciones --}}
		<label for="observations" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.observaciones')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="observations" class="editor" value="{!! $briefing->observations !!}"/>
		</div>
	</div>

	<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
</form>