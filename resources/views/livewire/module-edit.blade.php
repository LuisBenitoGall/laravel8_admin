<div>
    {{-- <x-warnings msg="{{ $msg }}" alert="{{ $alert }}"/> --}}

    <div class="row mb-3">
        <div class="col-12">
            {{-- Acciones --}}
            <a href="{{ route('modules.index') }}" class="btn btn-info">
                <i class="fa fa-angle-left"></i>
                {!! trans('textos.volver_modulos') !!}
            </a>

            <a class="btn btn-info" href="#" data-toggle="modal" data-target="#modal-new-functionality">
                <i class="fa fa-plus"></i> 
                {!! ucfirst(trans('textos.funcionalidad_nueva')) !!}
            </a>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="mb-4">{!! ucfirst(trans('textos.modulo_editar')) !!} <u>{{ $name }}</u></h3> 

                    {{-- Tabs: --}}
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item @if(!$tab) active @endif">
                            <a class="nav-link @if(!$tab) active @endif" href="#tab-module-info" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.modulo')) !!}</a>
                        </li>
                        <li class="nav-item @if($tab == 'functionalities') active @endif">
                            <a class="nav-link @if($tab == 'functionalities') active @endif" href="#tab-functionalities" role="tab" data-toggle="tab">{!! ucfirst(trans('textos.funcionalidades')) !!}</a>
                        </li>
                    </ul>

                    <div class="tab-content pt-5">
                        {{-- Datos módulo: --}}
                        <div class="tab-pane fade @if(!$tab) show active @endif" role="tabpanel" id="tab-module-info">
                            @include('admin.modules.partials.module-info')   
                        </div>  

                        {{-- Funcionalidades: --}}
                        <div class="tab-pane fade @if($tab == 'functionalities') show active @endif" role="tabpanel" id="tab-functionalities">
                            @include('admin.modules.partials.functionalities')   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
