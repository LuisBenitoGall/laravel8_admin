<form method="post" autocomplete="off" action="{{ route('groups.update', $group->id) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}
	<input type="hidden" name="group_id" id="groupId" value="{{ $group->id }}">

	<div class="form-group row">
		{{-- Nombre grupo --}}
		<label for="name" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.grupo')) !!} *</label>
		<div class="col-md-6">
			<x-input type="text" name="name" id="name" autocomplete="off" maxlength="100" required value="{{ $group->name }}"/>
		</div>	

		{{-- Estado --}}
		<label for="status" class="col-md-2 col-form-label text-md-right">{!! ucfirst(trans('textos.grupo_activo')) !!}</label>
        <div class="col-md-2">
			<label class="custom-chk-rdb">
        		<input type="checkbox" name="status" @if($group->status == 1) checked @endif>
        		<span class="chk mt-1"></span>
    		</label>
        </div>
	</div>

	<div class="form-group row">
		{{-- Descripción --}}
		<label for="description" class="col-md-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-md-10">
			<x-input-textarea name="description" class="editor" value="{{ $group->description }}"/>
		</div>
	</div>
	
	<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
</form>