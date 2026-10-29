<form method="post" autocomplete="off" action="{{ route('briefing-templates.update', $template) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Nombre de la plantilla --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plantilla_nombre')) !!}*</label>
		<div class="col-lg-7">
			<x-input type="text" name="name" id="name" value="{{ $template->name }}" autocomplete="off" maxlength="255" required/>
		</div>	

		{{-- Estado --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.plantilla_activa')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip">
                <input type="checkbox" name="status" @if($template->status == 1) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
        </div>
	</div>

	<div class="form-group row">
		{{-- Condiciones --}}
		<label for="conditions" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.condiciones')) !!}</label>
		<div class="col-lg-10">
			<x-input-textarea name="conditions" class="editor" value="{!! $template->conditions !!}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>