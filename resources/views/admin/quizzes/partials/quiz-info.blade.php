<form method="post" autocomplete="off" action="{{ route('quizzes.update', $quiz) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Concurso --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nombre')) !!}*</label>
		<div class="col-lg-6">
			<x-input type="text" name="name" id="name" value="{!! $quiz->name !!}" autocomplete="off" maxlength="255" required/>
		</div>
		
		{{-- Status --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.activo')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip">
                <input type="checkbox" name="status" @if($quiz->status == 1) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
		</div>
	</div>

	<div class="form-group row">
		{{-- Descripción --}}
		<label for="description" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.descripcion')) !!}</label>
		<div class="col-lg-10">	
			<x-input-textarea name="description" class="editor" value="{!! $quiz->description !!}"/>
		</div>
	</div>
	
	<x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
</form>