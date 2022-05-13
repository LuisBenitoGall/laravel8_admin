<form method="post" autocomplete="off" action="{{ route('budget-forms.update', $form) }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	@method('PUT')
	{{ csrf_field() }}

	<div class="form-group row">
		{{-- Título del formulario --}}
		<label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.formulario_titulo')) !!}*</label>
		<div class="col-lg-7">
			<x-input type="text" name="name" id="name" value="{{ $form->name }}" autocomplete="off" maxlength="255" required/>
		</div>	

		{{-- Estado --}}
		<label for="status" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.formulario_activo')) !!}</label>
		<div class="col-lg-1">
			<label class="custom-chk-rdb" rel="tooltip">
                <input type="checkbox" name="status" @if($form->status == 1) checked @endif>
                <span class="chk" style="top: 5px;"></span>
            </label>
        </div>
	</div>
	<br>

	{{-- Tabs idiomas: --}}
    <x-tabs-languages :languages="$languages" />

    <div class="tab-content pt-5">
		@php
		$j = 0;

		$arrConditions = unserialize($form->conditions);
		@endphp

		@foreach($languages as $lang)	
			<div class="tab-pane fade show @if($j == 0) active @endif" role="tabpanel" id="tab-{{ $lang[0] }}">
				<div class="form-group row">
					<label for="conditions" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.condiciones')) !!} {{ $lang[3] }}</label>
					<div class="col-lg-10">
						<textarea class="form-control editor" name="conditions_{{ $lang[0] }}">@if(is_array($arrConditions)) {{ old('excerpt_'.$lang[0], $arrConditions[$lang[0]]) }} @else {{ old('excerpt_'.$lang[0]) }} @endif</textarea>
					</div>
				</div>
			</div>

			@php
			$j++;
			@endphp
		@endforeach
    </div>

    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>

