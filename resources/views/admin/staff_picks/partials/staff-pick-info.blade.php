<form method="post" autocomplete="off" action="{{ route('staff-pick.update', $process) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Título --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.titulo')) !!}*</label>
		<div class="col-lg-10">
			<x-input type="text" name="name" id="name" placeholder="{!! ucfirst(trans('textos.titulo')) !!}" value="{{ $process->name }}" autocomplete="off" maxlength="150" required/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Fecha publicación --}}
		<label for="published_at" class="col-lg-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.fecha_publicacion')) !!}</label>
		<div class="col-lg-3 col-xl-2">
			@php
			$published_at = $process->published_at != ''? Carbon\Carbon::parse($process->published_at)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="published_at" id="published_at" value="{{ $published_at }}" :format="$languages[config('app.locale')][4]"/>
		</div>

		{{-- Fecha final de publicación --}}
		<label for="published_end" class="col-lg-2 col-xl-2 col-form-label">{!! ucfirst(trans('textos.fin_publicacion')) !!}</label>
		<div class="col-lg-3 col-xl-2">
			@php
			$published_end = $process->published_end != ''? Carbon\Carbon::parse($process->published_end)->format($languages[session()->get('locale')][4]):'';
			@endphp

			<x-date-picker name="published_end" id="published_end" value="{{ $published_end }}" :format="$languages[config('app.locale')][4]"/>
		</div>

		{{-- Status --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.estado')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb">
		        <input type="checkbox" name="status" @if($process->status == 1) checked @endif>
		        <span class="chk" style="top: 5px;"></span>
		    </label> 
		</div>
	</div>

	<div class="form-group row">
		{{-- Destacado --}}
		<label for="excerpt" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.destacado')) !!} <br>
		</label>
		<div class="col-lg-10">
			<x-input-textarea name="excerpt" value="{{ $process->excerpt }}"/>
		</div>
	</div>

	<div class="form-group row">
		{{-- Contenido --}}
		<label for="content" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.contenido')) !!} <br>
		</label>
		<div class="col-lg-10">
			<x-input-textarea class="editor" name="content" value="{{ $process->content }}"/>
		</div>
	</div>

	<x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>	
</form>