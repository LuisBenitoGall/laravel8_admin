@php
//Icons:
$pathIcons = storage_path()."/json/fa-icons.json";
$icons = json_decode(file_get_contents($pathIcons), true); 
@endphp

<div class="form-group row">
	{{-- Concepto --}}
    <label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.concepto')) !!}*</label>
	<div class="col-lg-10">
		@php
        $name = $item? $item->name:'';
        @endphp

		<x-input type="text" name="name" id="name" value="{{ $name }}" autocomplete="off" maxlength="191" required/>
    </div>
</div>

<div class="form-group row">
	{{-- Tipo --}}
	<label for="type" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tipo')) !!}*</label>
	<div class="col-lg-2">
		@php
		$types = [1 => trans('textos.entrada'), 2 => trans('textos.salida')];

		$type = $item && $item->type? $item->type:'';

		$disabled = $item? true:false;
		@endphp

		<x-select name="type" id="type" class="select2" value="{{ $type }}" :options="$types" info="timetable-items-type" disabled="{{ $disabled }}" required label="opcion_selec"/>
	</div>

	{{-- Concepto exclusivo de salida --}}
	@if(!$item)
		<label for="add_output" class="col-lg-2 col-form-label addOut">{!! ucfirst(trans('textos.opcion_salida')) !!}</label>
		<div class="col-lg-1 addOut">
			<label class="custom-chk-rdb" rel="tooltip">
	            <input type="checkbox" name="add_output">
	            <span class="chk" style="top: 5px;"></span>

	        	{!! App\Helpers\Helpers::info('timetable-out-option', true) !!}
	        </label>
		</div>
	@endif

	{{-- Hora mínima --}}
	<label for="min_hour" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.hora_min')) !!} </label>	
	<div class="col-lg-2">
		@php
        $min_hour = $item && $item->min_hour? substr($item->min_hour,0,-3):'';
        @endphp

		<x-input type="text" name="min_hour" value="{{ $min_hour }}" class="text-right maskHours" placeholder="hh:mm" autocomplete="off" maxlength="5" info="timetable-min-hour"/>
	</div>
</div>

<div class="form-group row">
	{{-- Duración fija --}}
	<label for="fixed" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.fijo')) !!} ({!! trans('textos.minutos') !!})</label>	
	<div class="col-lg-2">
		@php
        $fixed = $item && $item->fixed? $item->fixed:'';
        @endphp

		<x-input type="text" name="fixed" value="{{ $fixed }}" class="text-right" autocomplete="off" maxlength="5" info="timetable-fixed-time"/>
	</div>

	{{-- Duración mínima --}}
	<label for="min" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tiempo_min')) !!} ({!! trans('textos.minutos') !!})</label>	
	<div class="col-lg-2">
		@php
        $min = $item && $item->min? $item->min:'';
        @endphp

		<x-input type="text" name="min" value="{{ $min }}" class="text-right" autocomplete="off" maxlength="5" info="timetable-min-time"/>
	</div>

	{{-- Duración máxima --}}
	<label for="max" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.tiempo_max')) !!} ({!! trans('textos.minutos') !!})</label>	
	<div class="col-lg-2">
		@php
        $max = $item && $item->max? $item->max:'';
        @endphp

		<x-input type="text" name="max" value="{{ $max }}" class="text-right" autocomplete="off" maxlength="5" info="timetable-max-time"/>
	</div>
</div>

<div class="form-group row">
	{{-- Veces por día --}}
	<label for="qty" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.veces_dia')) !!}</label>	
	<div class="col-lg-2">
		@php
        $qty = $item && $item->qty? $item->qty:'';
        @endphp

		<x-input type="text" name="qty" value="{{ $qty }}" class="text-right" autocomplete="off" maxlength="2" info="timetable-qty"/>
	</div>

	{{-- Color --}}
	<label for="color" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color')) !!}</label>	
	<div class="col-lg-2">
		<div class="input-group ">
            <div class="input-group-prepend">
                <span class="input-group-addon" @if($item && $item->color) style="background-color: {{ $item->color }};" @endif>&nbsp;</span>
            </div>

			@php
	        $color = $item && $item->color? $item->color:'';
	        @endphp

			<x-color-picker name="color" value="{{ $color }}" info="timetable-color"/>
		</div>
	</div>

	{{-- Revisión --}}
	<label for="reviewable" class="col-lg-2 col-form-label ">{!! ucfirst(trans('textos.revision')) !!}</label>
	<div class="col-lg-1">
		<label class="custom-chk-rdb" rel="tooltip">
            <input type="checkbox" name="reviewable" @if($item && $item->reviewable) checked @endif>
            <span class="chk" style="top: 5px;"></span>

        	{!! App\Helpers\Helpers::info('timetable-reviewable', true) !!}
        </label>
	</div>
</div>

<div class="form-group row">
	{{-- Obligatorio --}}
	<label for="required" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.obligatorio_diario')) !!}</label>	
	<div class="col-lg-2">
		@php
        $required = $item && $item->required? $item->required:'';
        @endphp

		<x-input type="text" name="required" value="{{ $required }}" class="text-right" autocomplete="off" maxlength="3" info="timetable-item-required"/>
	</div>
</div>

<div class="form-group row">
	{{-- Icono --}}
    <label for="icono" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.icono')) !!}</label>
	<div class="col-lg-10">
		<input type="hidden" name="icon" class="iconValue" @if($item && $item->icon) value="{{ $item->icon }}" @endif>

		@foreach($icons as $icon)
			<span class="iconGallery @if($item && $item->icon == $icon) selected @endif" data-icono="{{ $icon }}" rel="tooltip" title="{{ $icon }}">
				<i class="fa fa-{{ $icon }}"></i>
			</span>
		@endforeach	
	</div>
</div>