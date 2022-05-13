@extends('admin.partials.layout')

@section('content')

<div class="row mb-3">
	<div class="col-12">
        <h5>{{ trans('textos.modulos_selecciona_para_empresa') }}</h5>
    </div>

	@foreach($modules as $row)
        @php
        $icon = $row->icon? $row->icon:'check';
        @endphp

        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <div class="widget p-lg text-center" @if($row->color) style="background-color: {{ $row->color }} !important" @endif>
                <div class="m-b-md">
                    <h4 class="text-white text-uppercase m-b-20 mb-4">
                        {!! trans('textos.'.$row->label) !!}
                    </h4>    

                    <i class="fa fa-{{ $icon }} fa-4x text-white"></i>
                    
                    {{-- Info --}}
                    <a href="#" class="btnModuleInfo widget-info" data-module="{{ $row->slug }}" data-toggle="modal" data-target="#modal-module-explanation" rel="tooltip" title="{!! trans('textos.modulo_info') !!}">
                        <i class="fa fa-info-circle"></i>
                    </a>

                    {{-- Select: sólo si el módulo es optativo. Los módulos obligatorios no se pueden eliminar --}}
                    @if($row->level == 3)
                        {{-- Se da opción de activar o desactivar el módulo si el usuario tiene permisos sobre ese módulo --}}
                        @can('module_'.$row->slug)
                            @if(in_array($row->id, $companyModules))
                                <span class="module-selected" data-module="{{ $row->slug }}" rel="tooltip" title="{!! trans('textos.modulo_activo') !!}">
                                    <i class="fa fa-check-circle text-success"></i>
                                </span>
                            @else
                                <span class="module-select" data-module="{{ $row->slug }}" rel="tooltip" title="{!! trans('textos.modulo_agregar') !!}">
                                    <i class="fa fa-chevron-circle-up"></i>
                                </span>
                            @endif
                        @endcan
                    @endif  
                </div>
            </div>
        </div>
	@endforeach

</div>

{{-- Modals: --}}
{{-- Module explanation --}}
<div class="modal fade opened-modal-save" id="modal-module-explanation" tabindex="-1" role="dialog" aria-labelledby="moduleExplanation" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="moduleExplanation"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal info módulo.
$('.btnModuleInfo').on('click', function(){
    var modulo = $(this).data('module');
    $.get('/admin/modules/'+modulo+'/show', function(response, state){
        if(response){
            $('#modal-module-explanation .modal-body').html(response);
        }
    });
});

// Guardar módulo de empresa.
$('.module-select').on('click', function(){
    var modulo = $(this).data('module');

    swal.fire({
        title: txtModuloEmpresaConfirmar,
        text: "",
        icon: "success",
        iconColor: color_success,
        showCancelButton: true,
        confirmButtonColor: color_primary,
        cancelButtonColor: color_danger,
        confirmButtonText: txtAceptar,
        cancelButtonText: txtCancelar
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = '/admin/company-modules/'+modulo+'/store';
        }
    });
});

// Eliminar módulo de empresa:
$('.module-selected').on('click', function(){
    var modulo = $(this).data('module');

    swal.fire({
        title: txtModuloEmpresaEliminar,
        text: "",
        icon: 'warning',
        iconColor: color_warning,
        showCancelButton: true,
        confirmButtonColor: color_primary,
        cancelButtonColor: color_danger,
        confirmButtonText: txtEliminar,
        cancelButtonText: txtCancelar
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = '/admin/company-modules/'+modulo+'/delete';
        }
    });
});
</script>
@endpush

@endsection