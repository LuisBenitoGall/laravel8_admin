<div>
    <div class="row mb-3">
        <div class="col-12">
            {{-- Acciones: --}}
            <a href="{{ route('modules.index') }}" class="btn btn-info">
                <i class="fa fa-angle-left"></i>
                {!! trans('textos.volver_modulos') !!}
            </a>
        </div>
    </div>    

    <div class="row mb-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    {{-- <form wire:submit.prevent="store" method="post" autocomplete="off" accept-charset="utf-8" enctype="multipart/form-data" role="form"> --}}
                    <form method="post" autocomplete="off" action="{{ route('modules.store') }}" accept-charset="utf-8" enctype="multipart/form-data" role="form">
                        {{ csrf_field() }}

                        <div class="form-group row">
                            {{-- Nombre módulo --}}
                            <label for="name" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.modulo')) !!}*</label>
                            <div class="col-lg-4">
                                <x-input type="text" name="name" maxlength="50" required info="module-name"/>
                            </div>

                            {{-- Nivel --}}
                            <label for="level" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.nivel')) !!}*</label>
                            <div class="col-lg-4">
                                @php
                                $options = [];
                                @endphp

                                @foreach($levels as $idx => $level)
                                    @php
                                    $options[$idx] = $level;
                                    @endphp
                                @endforeach 

                                <x-select name="level" id="select2_level" class="select2" label="nivel_selec" :options="$options" required info="module-level"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            {{-- Icono --}}
                            <label for="icon" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.icono')) !!}</label>
                            <div class="col-lg-4">
                                <x-input type="text" name="icon" maxlength="25" info="module-icon"/>
                            </div>

                            {{-- Color --}}
                            <label for="color" class="col-lg-2 col-form-label">{!! ucfirst(trans('textos.color')) !!}</label>
                            <div class="col-lg-4">
                                <x-color-picker name="color" id="colorpicker_color" info="module-color"/>
                            </div>
                        </div>

                        <x-btn-submit name="submit_save" label="{{ trans('textos.guardar') }}"/>
                    </form> 
                </div>
            </div>  
        </div>
    </div>
</div>

