<div class="form-group row">
    {{-- Tipo de jornada --}}
    <label for="name" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.tipo')) }}</label>
    <div class="col-lg-5">
        @php
        $name = $day_type? $day_type->name:'';
        @endphp

        <x-input type="text" name="name" value="{{ $name }}" placeholder="{!! ucfirst(trans('textos.tipo_jornada')) !!}" autocomplete="off" maxlength="150" required info="day-type-name"/>
    </div>

    {{-- Color --}}
    <label for="color" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.color')) }}</label>
    <div class="col-lg-2">
        <div class="input-group m-b">
            <div class="input-group-prepend">
                <span class="input-group-addon" @if($day_type && $day_type->color) style="background-color: {{ $day_type->color }};" @endif>&nbsp;</span>
            </div>

            @php
            $color = $day_type? $day_type->color:'';
            @endphp

            <x-color-picker name="color" id="dayTypeColor" value="{{ $color }}" info="day-type-color"/>
        </div>
    </div> 
</div>

<div class="form-group row">
    {{-- Compatible laborable --}}
    <label for="is_working" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.compatible_laborable')) }}</label>
    <div class="col-lg-1 sinpadding">
        {{-- Este dato no se puede editar --}}
        @if($day_type)
            <span class="d-inline-block align-top ml-2">
                @if($day_type->is_working)
                    <i class="fa fa-check text-success" style="margin-top: 11px;"></i>   
                @else
                    <i class="fa fa-times text-danger" style="margin-top: 12px;"></i>
                @endif  
            </span>

            <span class="position-relative d-inline-block align-top ml-3">
                {!! App\Helpers\Helpers::info('day-type-is-working-no-modifiable') !!}     
            </span>

        @else
            <label class="custom-chk-rdb">
                <input type="checkbox" name="is_working">
                <span class="chk" style="margin-top: 6px;"></span>

                {!! App\Helpers\helpers::info('day-type-is-working', true) !!}
            </label>
        @endif
    </div>

    {{-- Trabajo presencial en oficina --}}
    <label for="in_office" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.trabajo_presencial')) }}</label>
    <div class="col-lg-1 sinpadding">
        {{-- Este dato no se puede editar --}}
        @if($day_type)
            <span class="d-inline-block align-top ml-2">
                @if($day_type->in_office)
                    <i class="fa fa-check text-success" style="margin-top: 11px;"></i>   
                @else
                    <i class="fa fa-times text-danger" style="margin-top: 12px;"></i>
                @endif  
            </span>

            <span class="position-relative d-inline-block align-top ml-3">
                {!! App\Helpers\helpers::info('day-type-in-office-no-modifiable') !!}     
            </span>
        @else
            <label class="custom-chk-rdb">
                <input type="checkbox" name="in_office">
                <span class="chk" style="margin-top: 6px;"></span>

                {!! App\Helpers\helpers::info('day-type-in-office', true) !!}
            </label>
        @endif
    </div>
 
    {{-- Disponible para usuario --}}
    <label for="user_enabled" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.disponible_para_usuario')) }}</label>
    <div class="col-lg-1 sinpadding">
        {{-- Este dato no se puede editar --}}
        @if($day_type)
            <span class="d-inline-block align-top ml-2">
                @if($day_type->user_enabled)
                    <i class="fa fa-check text-success" style="margin-top: 11px;"></i>
                @else
                    <i class="fa fa-times text-danger" style="margin-top: 12px;"></i>
                @endif      
            </span>

            <span class="position-relative d-inline-block align-top ml-3">
                {!! App\Helpers\helpers::info('day-type-user-enabled-no-modifiable') !!}     
            </span>

        @else
            <label class="custom-chk-rdb">
                <input type="checkbox" name="user_enabled">
                <span class="chk" style="margin-top: 6px;"></span>

                {!! App\Helpers\Helpers::info('day-type-user-enabled', true) !!}
            </label>
        @endif
    </div>

    {{-- Acumulable --}}
    <label for="cumulative" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.acumulable')) }}</label>
    <div class="col-lg-1 sinpadding">
        <label class="custom-chk-rdb">
            <input type="checkbox" name="cumulative" @if($day_type && $day_type->cumulative) checked @endif>
            <span class="chk" style="margin-top: 6px;"></span>

            {!! App\Helpers\Helpers::info('day-type-cumulative', true) !!}
        </label>
    </div>
</div>

<div class="form-group row">
    {{-- Rango de fechas --}}
    <label for="rank" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.rango_fechas')) }}</label>
    <div class="col-lg-1 sinpadding">
        <label class="custom-chk-rdb">
            <input type="checkbox" name="rank" @if($day_type && $day_type->rank) checked @endif>
            <span class="chk" style="margin-top: 6px;"></span>

            {!! App\Helpers\Helpers::info('day-type-rank', true) !!}
        </label>
    </div>

    {{-- Modificable --}}
    <label for="modifiable" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.modificable_manualmente')) }}</label>
    <div class="col-lg-1 sinpadding">
        <label class="custom-chk-rdb">
            <input type="checkbox" name="modifiable" @if($day_type && $day_type->modifiable) checked @endif>
            <span class="chk" style="margin-top: 6px;"></span>

            {!! App\Helpers\Helpers::info('day-type-modifiable', true) !!}
        </label>
    </div>

    {{-- Repetición anual --}}
    <label for="annual_repetition" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.repeticion_anual')) }}</label>
    <div class="col-lg-1 sinpadding">
        <label class="custom-chk-rdb">
            <input type="checkbox" name="annual_repetition" @if($day_type && $day_type->annual_repetition) checked @endif>
            <span class="chk" style="margin-top: 6px;"></span>

            {!! App\Helpers\Helpers::info('day-type-annual-repetition', true) !!}
        </label>
    </div>
</div>

<div class="form-group row">
    {{-- Descuento de conceptos horarios obligatorios --}}
    <label for="subtract_required_concept" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.descuenta_conceptos_obligatorios')) }}</label>
    <div class="col-lg-1 sinpadding">
        {{-- Este dato no se puede editar --}}
        @if($day_type)
            <span class="d-inline-block align-top ml-2">
                @if($day_type->subtract_required_concept)
                    <i class="fa fa-check text-success" style="margin-top: 11px;"></i>
                @else
                    <i class="fa fa-times text-danger" style="margin-top: 12px;"></i>
                @endif      
            </span>

            <span class="position-relative d-inline-block align-top ml-3">
                {!! App\Helpers\helpers::info('subtract-required-concept-no-modifiable') !!}     
            </span>

        @else
            <label class="custom-chk-rdb">
                <input type="checkbox" name="subtract_required_concept" @if($day_type && $day_type->subtract_required_concept) checked @endif>
                <span class="chk" style="margin-top: 6px;"></span>

                {!! App\Helpers\Helpers::info('day-type-subtract-required-concept', true) !!}
            </label>
        @endif
    </div>

    {{-- Conceptos obligatorios que se aplican --}}
    <label for="concepts_not_required" class="col-lg-2 col-form-label">{{ ucfirst(trans('textos.conceptos_no_obligatorios')) }}</label>
    <div class="col-lg-2 sinpadding-left">
        @php
        $concepts_not_required = [];
        @endphp
        
        @if($day_type)
            @php
            $concepts_not_required = $day_type->concepts_not_required? unserialize($day_type->concepts_not_required):[];
            @endphp
        @endif
        <x-select name="concepts_not_required[]" class="select2" :options="$required_concepts" info="day-types-no-required-concepts" :values="$concepts_not_required" multiple/>
    </div>
</div>

                     
            