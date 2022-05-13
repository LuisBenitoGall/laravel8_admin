@php
// Idiomas:
$languages = App\Helpers\Helpers::languages();

$levels = App\Helpers\Helpers::levels();
@endphp

{{-- <form wire:submit.prevent="update" method="post" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form"> --}}

<form method="post" autocomplete="off" id="frm-edit-module" action="{{ route('modules.update') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
	{{ csrf_field() }}
	<input type="hidden" name="id" value="{{ $module->id }}">

	<div class="form-group row">
        {{-- Nombre módulo --}}
        <label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.modulo')) !!} *</label>
        <div class="col-lg-4">
            <x-input type="text" name="name" maxlength="50" value="{{ $module->name }}" required info="module-name"/>
        </div>
    
        {{-- Nivel --}}
        <label for="level" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nivel')) !!} *</label>
        <div class="col-lg-4">
            @php
            $options = [];
            @endphp

            @foreach($levels as $idx => $row)
                @php
                $options[$idx] = $row;
                @endphp
            @endforeach 

            <x-select name="level" id="select2_level" class="select2" label="nivel_selec" value="{{ $module->level }}" :options="$options" required info="module-level"/>
        </div>
    </div>

    <div class="form-group row">
        {{-- Icono --}}
        <label for="icon" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.icono')) !!}</label>
        <div class="col-lg-4">
            <x-input type="text" name="icon" maxlength="25" value="{{ $module->icon }}" info="module-icon"/>
        </div>

        {{-- Color --}}
        <label for="color" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color')) !!}</label>
        <div class="col-lg-3">
            <div class="input-group m-b">
                <div class="input-group-prepend">
                    <span class="input-group-addon" @if($module->color) style="background-color: {{ $module->color }}" @endif>&nbsp;</span>
                </div>

                <x-color-picker name="color" id="colorpicker_color" value="{{ $module->color }}" info="module-color"/>
            </div>
        </div>
    </div>
    <br>

	{{-- Tabs idiomas: --}}
    <x-tabs-languages :languages="$languages" />

	<div class="tab-content pt-5">
		@php
		$i = 0;
		$explanation = unserialize($module->explanation);
		@endphp

		@foreach($languages as $lang)	
			<div class="tab-pane fade show @if($i == 0) active @endif" role="tabpanel" id="tab-{{ $lang[0] }}">
				<div class="form-group row">
					<label for="explanation" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.explicacion')) !!} {{ $lang[3] }}</label>
					<div class="col-lg-10">
                        @php
                        $expl = $explanation? $explanation[$lang[0]]:'';
                        @endphp

                        <x-input-textarea name="explanation_{{ $lang[0] }}" class="editor" value="{{ $expl }}"/>
					</div>
				</div>
			</div>

			@php
			$i++;
			@endphp
		@endforeach
	</div>
	
    <x-btn-submit name="submit_update" label="{{ trans('textos.guardar') }}"/>
</form>